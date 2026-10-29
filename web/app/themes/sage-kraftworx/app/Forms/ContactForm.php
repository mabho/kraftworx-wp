<?php

namespace App\Forms;

class ContactForm
{
  /**
   * Handle a contact form submission.
   */
  public static function handle(): void
  {
    $redirect = wp_get_referer() ?: home_url('/contact/');
    $redirect = remove_query_arg('contact_status', $redirect);

    $nonce = isset($_POST['kraftworx_contact_nonce'])
      ? sanitize_text_field(wp_unslash($_POST['kraftworx_contact_nonce']))
      : '';

    if (! wp_verify_nonce($nonce, 'kraftworx_contact')) {
      self::redirect($redirect, 'error');
    }

    if (! empty($_POST['website'])) {
      self::redirect($redirect, 'success');
    }

    $schema = HubSpotForm::schema();
    $values = [];
    $valid = true;

    foreach ($schema['fields'] as $field) {
      $name = $field['name'];

      if ($field['hidden']) {
        $values[$name] = $field['default_value'];
        continue;
      }

      $raw = $_POST[$name] ?? '';

      if (is_array($raw)) {
        $value = implode(', ', array_map(
          static fn ($item): string => sanitize_text_field(wp_unslash($item)),
          $raw
        ));
      } elseif ($field['type'] === 'email') {
        $value = sanitize_email(wp_unslash($raw));
      } elseif ($field['type'] === 'textarea') {
        $value = sanitize_textarea_field(wp_unslash($raw));
      } else {
        $value = sanitize_text_field(wp_unslash($raw));
      }

      if ($field['required'] && trim($value) === '') {
        $valid = false;
      }

      if ($field['type'] === 'email' && $value !== '' && ! is_email($value)) {
        $valid = false;
      }

      $values[$name] = $value;
    }

    if (! $valid) {
      self::redirect($redirect, 'error');
    }

    if (HubSpotForm::shouldSubmit()) {
      $sent = HubSpotForm::submit($values, [
        'pageUri' => $redirect,
        'pageName' => get_bloginfo('name'),
      ]);
    } else {
      $sent = self::sendEmail($values, $schema);
    }

    self::redirect($redirect, $sent ? 'success' : 'error');
  }

  /**
   * Send the local fallback email.
   *
   * @param  array<string, mixed>  $values
   * @param  array<string, mixed>  $schema
   */
  protected static function sendEmail(array $values, array $schema): bool
  {
    $name = trim(sprintf('%s %s', $values['firstname'] ?? '', $values['lastname'] ?? ''));
    $name = $name !== '' ? $name : (string) ($values['name'] ?? '');
    $email = (string) ($values['email'] ?? '');

    foreach ($schema['fields'] as $field) {
      if ($field['type'] === 'email' && ! empty($values[$field['name']])) {
        $email = (string) $values[$field['name']];
        break;
      }
    }

    if ($email === '') {
      return false;
    }

    $lines = [];
    foreach ($schema['fields'] as $field) {
      $value = $values[$field['name']] ?? '';

      if ($value === '' || $field['hidden']) {
        continue;
      }

      $lines[] = $field['label'].': '.$value;
    }

    $recipient = apply_filters('kraftworx_contact_recipient', get_option('admin_email'));
    $subject = sprintf('Kraftworx contact form: %s', $name ?: $email);
    $body = implode("\n", $lines);
    $headers = $name !== '' ? [sprintf('Reply-To: %s <%s>', $name, $email)] : [];

    return wp_mail($recipient, $subject, $body, $headers);
  }

  /**
   * Redirect back to the contact page with a status.
   */
  protected static function redirect(string $url, string $status): void
  {
    wp_safe_redirect(add_query_arg('contact_status', $status, $url));
    exit;
  }
}
