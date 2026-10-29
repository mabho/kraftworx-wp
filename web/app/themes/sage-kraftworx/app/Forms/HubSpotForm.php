<?php

namespace App\Forms;

class HubSpotForm
{
  protected const CACHE_PREFIX = 'kraftworx_hubspot_form_';

  protected const CACHE_TTL = 12 * HOUR_IN_SECONDS;

  protected const FAILURE_TTL = 5 * MINUTE_IN_SECONDS;

  protected const FALLBACK_FIELDS = [
    [
      'name' => 'name',
      'label' => 'Your name',
      'type' => 'text',
      'field_type' => 'single_line_text',
      'required' => true,
      'hidden' => false,
      'placeholder' => 'YOUR NAME',
      'description' => '',
      'options' => [],
      'object_type_id' => '0-1',
      'default_value' => '',
    ],
    [
      'name' => 'email',
      'label' => 'Your email',
      'type' => 'email',
      'field_type' => 'email',
      'required' => true,
      'hidden' => false,
      'placeholder' => 'YOUR EMAIL',
      'description' => '',
      'options' => [],
      'object_type_id' => '0-1',
      'default_value' => '',
    ],
    [
      'name' => 'message',
      'label' => 'How can we help?',
      'type' => 'textarea',
      'field_type' => 'multi_line_text',
      'required' => true,
      'hidden' => false,
      'placeholder' => 'HOW CAN WE HELP?',
      'description' => '',
      'options' => [],
      'object_type_id' => '0-1',
      'default_value' => '',
    ],
  ];

  /**
   * Get the normalized HubSpot form schema.
   */
  public static function schema(): array
  {
    $formId = self::formId();

    if ($formId === '') {
      return self::fallback();
    }

    $cacheKey = self::cacheKey($formId);
    $cached = get_transient($cacheKey);

    if (is_array($cached)) {
      return $cached;
    }

    $token = self::env('HUBSPOT_ACCESS_TOKEN');

    if ($token === '') {
      return self::fallback($formId);
    }

    $response = wp_remote_get(
      "https://api.hubapi.com/marketing/v3/forms/{$formId}",
      [
        'timeout' => 10,
        'headers' => [
          'Accept' => 'application/json',
          'Authorization' => "Bearer {$token}",
        ],
      ]
    );

    if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
      $schema = self::fallback($formId);
      set_transient($cacheKey, $schema, self::FAILURE_TTL);

      return $schema;
    }

    $form = json_decode(wp_remote_retrieve_body($response), true);
    $schema = self::normalize(is_array($form) ? $form : [], $formId);

    if (empty($schema['fields'])) {
      $schema = self::fallback($formId);
      set_transient($cacheKey, $schema, self::FAILURE_TTL);

      return $schema;
    }

    set_transient($cacheKey, $schema, self::CACHE_TTL);

    return $schema;
  }

  /**
   * Clear the cached schema for the configured form.
   */
  public static function refresh(): void
  {
    $formId = self::formId();

    if ($formId !== '') {
      delete_transient(self::cacheKey($formId));
    }
  }

  /**
   * Whether HubSpot submission is configured for this environment.
   */
  public static function shouldSubmit(): bool
  {
    if (self::formId() === '' || self::env('HUBSPOT_PORTAL_ID') === '') {
      return false;
    }

    $enabled = self::env('HUBSPOT_SUBMIT');

    if ($enabled !== '') {
      return filter_var($enabled, FILTER_VALIDATE_BOOLEAN);
    }

    return function_exists('wp_get_environment_type')
      && wp_get_environment_type() === 'production';
  }

  /**
   * Submit normalized values to HubSpot.
   *
   * @param  array<string, mixed>  $values
   * @param  array<string, string>  $context
   */
  public static function submit(array $values, array $context = []): bool
  {
    $schema = self::schema();
    $fields = [];

    foreach ($schema['fields'] as $field) {
      $name = $field['name'];
      $value = $field['hidden']
        ? $field['default_value']
        : ($values[$name] ?? '');

      if (is_array($value)) {
        $value = implode(', ', array_map('sanitize_text_field', $value));
      }

      if ($value === '' && ! $field['required']) {
        continue;
      }

      $fields[] = [
        'objectTypeId' => $field['object_type_id'],
        'name' => $name,
        'value' => (string) $value,
      ];
    }

    if (empty($fields)) {
      return false;
    }

    $payload = ['fields' => $fields];

    if (! empty($context)) {
      $payload['context'] = array_filter($context);
    }

    if (! empty($_COOKIE['hubspotutk'])) {
      $payload['context']['hutk'] = sanitize_text_field(wp_unslash($_COOKIE['hubspotutk']));
    }

    $response = wp_remote_post(
      sprintf(
        'https://api.hsforms.com/submissions/v3/integration/submit/%s/%s',
        rawurlencode(self::env('HUBSPOT_PORTAL_ID')),
        rawurlencode(self::formId())
      ),
      [
        'timeout' => 10,
        'headers' => ['Content-Type' => 'application/json'],
        'body' => wp_json_encode($payload),
      ]
    );

    if (is_wp_error($response)) {
      return false;
    }

    $status = wp_remote_retrieve_response_code($response);

    return $status >= 200 && $status < 300;
  }

  /**
   * The configured form GUID.
   */
  public static function formId(): string
  {
    return self::env('HUBSPOT_FORM_GUID');
  }

  /**
   * Normalize the HubSpot form definition.
   */
  protected static function normalize(array $form, string $formId): array
  {
    $fields = [];

    foreach (($form['fieldGroups'] ?? []) as $group) {
      foreach (($group['fields'] ?? []) as $field) {
        $name = (string) ($field['name'] ?? '');
        $type = self::fieldType((string) ($field['fieldType'] ?? ''));

        if ($name === '' || $type === null) {
          continue;
        }

        $fields[] = [
          'name' => $name,
          'label' => (string) ($field['label'] ?? $name),
          'type' => $type,
          'field_type' => (string) $field['fieldType'],
          'required' => ! empty($field['required']),
          'hidden' => ! empty($field['hidden']),
          'placeholder' => (string) ($field['placeholder'] ?? ''),
          'description' => (string) ($field['description'] ?? ''),
          'options' => self::options($field['options'] ?? []),
          'object_type_id' => (string) ($field['objectTypeId'] ?? '0-1'),
          'default_value' => (string) ($field['defaultValue'] ?? ''),
        ];
      }
    }

    return [
      'id' => (string) ($form['id'] ?? $formId),
      'name' => (string) ($form['name'] ?? 'Contact'),
      'fields' => $fields,
      'legal_consent' => $form['legalConsentOptions'] ?? ['type' => 'none'],
      'recaptcha' => ! empty($form['configuration']['recaptchaEnabled']),
    ];
  }

  /**
   * Map a HubSpot field type to a renderable type.
   */
  protected static function fieldType(string $fieldType): ?string
  {
    return match ($fieldType) {
      'email' => 'email',
      'single_line_text' => 'text',
      'multi_line_text' => 'textarea',
      'phone', 'mobile_phone' => 'tel',
      'number' => 'number',
      'dropdown' => 'select',
      'radio' => 'radio',
      'multiple_checkboxes' => 'checkboxes',
      'single_checkbox' => 'checkbox',
      'datepicker' => 'date',
      default => null,
    };
  }

  /**
   * Normalize selectable field options.
   */
  protected static function options(array $options): array
  {
    return array_values(array_map(
      static fn (array $option): array => [
        'label' => (string) ($option['label'] ?? $option['value'] ?? ''),
        'value' => (string) ($option['value'] ?? ''),
      ],
      array_filter($options, 'is_array')
    ));
  }

  /**
   * Fallback schema used until the remote definition is available.
   */
  protected static function fallback(string $formId = ''): array
  {
    return [
      'id' => $formId,
      'name' => 'Contact',
      'fields' => self::FALLBACK_FIELDS,
      'legal_consent' => ['type' => 'none'],
      'recaptcha' => false,
    ];
  }

  protected static function cacheKey(string $formId): string
  {
    return self::CACHE_PREFIX.md5($formId);
  }

  protected static function env(string $key): string
  {
    $value = function_exists('Env\\env')
      ? \Env\env($key, '')
      : ($_ENV[$key] ?? getenv($key));

    if (is_bool($value)) {
      return $value ? '1' : '0';
    }

    if (is_int($value) || is_float($value)) {
      return (string) $value;
    }

    return is_string($value) ? trim($value) : '';
  }
}
