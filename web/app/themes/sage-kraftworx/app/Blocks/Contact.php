<?php

namespace App\Blocks;

use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class Contact extends Block
{
  /**
   * The block name.
   *
   * @var string
   */
  public $name = 'Contact';

  /**
   * The block description.
   *
   * @var string
   */
  public $description = 'A contact section with a short introduction and form.';

  /**
   * The block category.
   *
   * @var string
   */
  public $category = 'kraftworx';

  /**
   * The block icon.
   *
   * @var string|array
   */
  public $icon = 'email';

  /**
   * The block keywords.
   *
   * @var array
   */
  public $keywords = ['contact', 'form', 'email'];

  /**
   * The block API version.
   *
   * @var int
   */
  public $apiVersion = 3;

  /**
   * The ACF block version.
   *
   * @var int
   */
  public $blockVersion = 3;

  /**
   * The supported block features.
   *
   * @var array
   */
  public $supports = [
    'multiple' => false,
  ];

  /**
   * Data to be passed to the block before rendering.
   */
  public function with(): array
  {
    return [
      'title' => get_field('title') ?: 'A 45-Minute Diagnostic',
      'intro_text' => get_field('intro_text'),
      'submit_label' => get_field('submit_label') ?: 'Submit Form',
      'confirmation_message' => get_field('confirmation_message') ?: 'Thank you. We will be in touch shortly.',
      'status' => isset($_GET['contact_status'])
        ? sanitize_key(wp_unslash($_GET['contact_status']))
        : '',
    ];
  }

  /**
   * The block field group.
   */
  public function fields(): array
  {
    $fields = Builder::make('contact');

    $fields
      ->addAccordion('Content', [
        'open' => 1,
        'multi_expand' => 1,
      ])
      ->addTextarea('title', [
        'label' => 'Title',
        'instructions' => 'Line breaks are preserved.',
        'rows' => 2,
        'new_lines' => '',
        'default_value' => "A 45-Minute\nDiagnostic",
      ])
      ->addTextarea('intro_text', [
        'label' => 'Intro text',
        'rows' => 6,
        'new_lines' => '',
      ])
      ->addAccordion('Form', [
        'open' => 1,
        'multi_expand' => 1,
      ])
      ->addText('submit_label', [
        'label' => 'Submit label',
        'default_value' => 'Submit Form',
      ])
      ->addTextarea('confirmation_message', [
        'label' => 'Confirmation message',
        'rows' => 2,
        'new_lines' => '',
        'default_value' => 'Thank you. We will be in touch shortly.',
      ]);

    return $fields->build();
  }
}
