<?php

namespace App\Blocks;

use App\Forms\HubSpotForm;
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
  public $description = 'A contact form for placing inside a section or column.';

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
    'multiple' => true,
  ];

  /**
   * Data to be passed to the block before rendering.
   */
  public function with(): array
  {
    $form = HubSpotForm::schema();

    return [
      'submit_label' => get_field('submit_label') ?: 'Submit Form',
      'confirmation_message' => get_field('confirmation_message') ?: 'Thank you. We will be in touch shortly.',
      'fields' => $form['fields'],
      'recaptcha' => $form['recaptcha'],
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
