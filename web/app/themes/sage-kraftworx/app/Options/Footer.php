<?php

namespace App\Options;

use Log1x\AcfComposer\Builder;
use Log1x\AcfComposer\Options as Field;

class Footer extends Field
{
  /**
   * The option page menu name.
   *
   * @var string
   */
  public $name = 'Footer';

  /**
   * The option page document title.
   *
   * @var string
   */
  public $title = 'Footer | Options';

  /**
   * The option page field group.
   */
  public function fields(): array
  {
    $fields = Builder::make('footer');

    $fields->addTextarea('footer_center', [
      'label' => 'Center content',
      'instructions' => 'Content displayed in the center of the footer. Line breaks are preserved.',
      'default_value' => '3214 N UNIVERSITY AVE / SUITE 140 / PROVO, UT 84604 / USA',
      'rows' => 3,
      'new_lines' => '',
    ]);

    return $fields->build();
  }
}
