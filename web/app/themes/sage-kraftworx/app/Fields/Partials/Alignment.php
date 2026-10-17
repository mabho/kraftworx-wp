<?php

namespace App\Fields\Partials;

use Log1x\AcfComposer\Builder;
use Log1x\AcfComposer\Partial;

class Alignment extends Partial
{
  /**
   * The partial field group.
   */
  public function fields(): Builder
  {
    $fields = Builder::make('alignment');

    $fields->addSelect('alignment', [
      'label' => 'Alignment',
      'choices' => [
        'left' => 'Left',
        'center' => 'Center',
        'right' => 'Right',
      ],
      'default_value' => 'right',
    ]);

    return $fields;
  }
}
