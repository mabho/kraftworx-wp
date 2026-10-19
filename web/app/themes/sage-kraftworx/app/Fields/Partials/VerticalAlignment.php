<?php

namespace App\Fields\Partials;

use Log1x\AcfComposer\Builder;
use Log1x\AcfComposer\Partial;

class VerticalAlignment extends Partial
{
  /**
   * The partial field group.
   */
  public function fields(): Builder
  {
    $fields = Builder::make('vertical_alignment');

    $fields->addSelect('vertical_alignment', [
      'label' => 'Vertical alignment',
      'choices' => [
        'center' => 'Center',
        'top' => 'Top',
      ],
      'default_value' => 'center',
    ]);

    return $fields;
  }
}
