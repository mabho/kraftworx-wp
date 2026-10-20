<?php

namespace App\Blocks;

use App\Blocks\Concerns\HasLayoutControls;
use App\Fields\Partials\FullHeight;
use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;
use WP_Post;

class Profiles extends Block
{
  use HasLayoutControls;

  /**
   * The block name.
   *
   * @var string
   */
  public $name = 'Profiles';

  /**
   * The block description.
   *
   * @var string
   */
  public $description = 'A staggered leadership section with manually ordered profile cards.';

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
  public $icon = 'groups';

  /**
   * The block keywords.
   *
   * @var array
   */
  public $keywords = ['profiles', 'leadership', 'team', 'people'];

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
    $profiles = get_field('profiles');
    $showBrandMark = get_field('show_brand_mark');

    return [
      'title' => get_field('title') ?: 'Leadership',
      'profiles' => collect(is_array($profiles) ? $profiles : [])
        ->map(fn ($profile) => $this->profileData($profile))
        ->filter()
        ->values()
        ->all(),
      'background_color' => get_field('background_color') ?: 'black',
      'show_brand_mark' => $showBrandMark === null ? true : (bool) $showBrandMark,
    ] + $this->fullHeight(true);
  }

  /**
   * Normalize a selected profile for the view.
   */
  protected function profileData(mixed $profile): ?array
  {
    $id = $profile instanceof WP_Post ? $profile->ID : (int) $profile;

    if (! $id || get_post_type($id) !== 'profile') {
      return null;
    }

    $name = get_the_title($id);
    $portraitId = get_post_thumbnail_id($id);
    $portraitAlt = $portraitId
      ? get_post_meta($portraitId, '_wp_attachment_image_alt', true)
      : '';

    return [
      'id' => $id,
      'name' => $name,
      'role' => get_field('role', $id),
      'bio' => get_field('bio', $id),
      'portrait' => $portraitId ? wp_get_attachment_image($portraitId, 'large', false, [
        'class' => 'h-full w-full object-cover object-bottom',
        'alt' => $portraitAlt ?: $name,
        'loading' => 'eager',
        'sizes' => '(min-width: 1024px) 28vw, (min-width: 640px) 48vw, 100vw',
      ]) : null,
    ];
  }

  /**
   * The block field group.
   */
  public function fields(): array
  {
    $fields = Builder::make('profiles');

    $fields
      ->addAccordion('Content', [
        'open' => 1,
        'multi_expand' => 1,
      ])
      ->addText('title', [
        'label' => 'Title',
        'default_value' => 'Leadership',
      ])
      ->addRelationship('profiles', [
        'label' => 'Profiles',
        'instructions' => 'Select profiles and drag them into order. The first profile starts in the right column.',
        'post_type' => ['profile'],
        'filters' => ['search'],
        'return_format' => 'object',
        'min' => 1,
      ])
      ->addAccordion('Layout', [
        'open' => 1,
        'multi_expand' => 1,
      ])
      ->addSelect('background_color', [
        'label' => 'Background color',
        'choices' => [
          'primary' => 'Primary',
          'secondary' => 'Secondary',
          'tertiary' => 'Tertiary',
          'black' => 'Black',
          'white' => 'White',
        ],
        'default_value' => 'black',
      ])
      ->addTrueFalse('show_brand_mark', [
        'label' => 'Brand mark',
        'instructions' => 'Show the large Kraftworx X mark at the bottom left.',
        'default_value' => 1,
        'ui' => 1,
      ])
      ->addPartial(FullHeight::class, [
        'default' => 1,
      ]);

    return $fields->build();
  }
}
