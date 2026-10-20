<?php

namespace App\Fields;

use Log1x\AcfComposer\Builder;
use Log1x\AcfComposer\Field;

class ProfileFields extends Field
{
  /**
   * The field group.
   */
  public function fields(): array
  {
    $fields = Builder::make('profile_details');

    $fields
      ->setLocation('post_type', '==', 'profile')
      ->addText('role', [
        'label' => 'Role',
        'instructions' => 'e.g. Co-Founder or Managing Partner.',
      ])
      ->addWysiwyg('bio', [
        'label' => 'Bio',
        'instructions' => 'Use the Styles dropdown for the Lead paragraph and Accent, Eyebrow, or Muted spans.',
        'toolbar' => 'Kraftworx',
        'tabs' => 'visual',
        'media_upload' => 0,
      ]);

    return $fields->build();
  }
}
