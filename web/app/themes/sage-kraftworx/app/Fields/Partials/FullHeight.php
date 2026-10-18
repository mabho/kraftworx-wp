<?php

namespace App\Fields\Partials;

use Log1x\AcfComposer\Builder;
use Log1x\AcfComposer\Partial;

class FullHeight extends Partial
{
  /**
   * The partial field group.
   */
  public function fields(array $args = []): Builder
  {
    $fields = Builder::make('full_height');

    $fields->addTrueFalse('full_height', [
      'label' => $args['label'] ?? 'Full screen height',
      'instructions' => $args['instructions'] ?? 'Fill the viewport and show the scroll arrow.',
      'default_value' => $args['default'] ?? 1,
      'ui' => 1,
    ]);

    return $fields;
  }
}
