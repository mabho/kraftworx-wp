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

    $name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
    $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    $message = isset($_POST['message']) ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '';

    if ($name === '' || ! is_email($email) || $message === '') {
      self::redirect($redirect, 'error');
    }

    $recipient = apply_filters('kraftworx_contact_recipient', get_option('admin_email'));
    $subject = sprintf('Kraftworx contact form: %s', $name);
    $body = sprintf(
      "Name: %s\nEmail: %s\n\n%s",
      $name,
      $email,
      $message
    );
    $headers = [sprintf('Reply-To: %s <%s>', $name, $email)];

    $sent = wp_mail($recipient, $subject, $body, $headers);

    self::redirect($redirect, $sent ? 'success' : 'error');
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
