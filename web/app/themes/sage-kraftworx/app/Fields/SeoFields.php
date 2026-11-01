<?php

namespace App\Fields;

use Log1x\AcfComposer\Builder;
use Log1x\AcfComposer\Field;

class SeoFields extends Field
{
  public function fields(): array
  {
    $fields = Builder::make('seo_meta', ['title' => 'SEO & social']);

    $fields
      ->setLocation('post_type', '==', 'page')
      ->addText('meta_title', [
        'label' => 'Meta title',
        'instructions' => 'Optional. Overrides the first Hero title for Open Graph and Twitter previews.',
      ])
      ->addTextarea('meta_description', [
        'label' => 'Meta description',
        'instructions' => 'Optional. Overrides the first Hero intro text for the description meta tag and social previews.',
        'rows' => 3,
        'new_lines' => '',
      ])
      ->addImage('social_image', [
        'label' => 'Social image',
        'instructions' => 'Optional. Overrides the first Hero image for social previews. Use an image at least 1200 x 630.',
        'return_format' => 'array',
        'preview_size' => 'medium',
      ]);

    return $fields->build();
  }
}
