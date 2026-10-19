<?php

namespace App\Blocks;

use App\Blocks\Concerns\HasLayoutControls;
use App\Fields\Partials\FullHeight;
use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class Image extends Block
{
  use HasLayoutControls;

  /**
   * The block name.
   *
   * @var string
   */
  public $name = 'Image';

  /**
   * The block description.
   *
   * @var string
   */
  public $description = 'A centered, contained image with optional full-height and background color.';

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
  public $icon = 'format-image';

  /**
   * The block keywords.
   *
   * @var array
   */
  public $keywords = ['image', 'media', 'picture', 'photo'];

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
    return [
      'image' => get_field('image'),
      'background_color' => get_field('background_color') ?: 'white',
    ] + $this->fullHeight(false);
  }

  /**
   * The block field group.
   */
  public function fields(): array
  {
    $fields = Builder::make('image');

    $fields
      ->addAccordion('Content', [
        'open' => 1,
        'multi_expand' => 1,
      ])
      ->addImage('image', [
        'label' => 'Image',
        'return_format' => 'array',
        'preview_size' => 'medium',
        'required' => 1,
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
        'default_value' => 'white',
      ])
      ->addPartial(FullHeight::class, [
        'default' => 0,
      ]);

    return $fields->build();
  }
}
