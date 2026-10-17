<?php

namespace App\Blocks;

use App\Blocks\Concerns\HasLayoutControls;
use App\Fields\Partials\Alignment;
use App\Fields\Partials\FullHeight;
use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class Hero extends Block
{
  use HasLayoutControls;

  /**
   * The block name.
   *
   * @var string
   */
  public $name = 'Hero';

  /**
   * The block description.
   *
   * @var string
   */
  public $description = 'A full-width hero with a background image, title, intro text, and call to action.';

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
  public $icon = 'cover-image';

  /**
   * The block keywords.
   *
   * @var array
   */
  public $keywords = ['hero', 'banner', 'header'];

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
      'background_image' => get_field('background_image'),
      'background_color' => get_field('background_color') ?: 'black',
      'title' => get_field('title'),
      'intro_text' => get_field('intro_text'),
      'call_to_action' => get_field('call_to_action'),
    ] + $this->alignment() + $this->fullHeight();
  }

  /**
   * The block field group.
   */
  public function fields(): array
  {
    $fields = Builder::make('hero');

    $fields
      ->addImage('background_image', [
        'label' => 'Background image',
        'return_format' => 'array',
        'preview_size' => 'medium',
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
      ->addText('title', [
        'label' => 'Title',
      ])
      ->addTextarea('intro_text', [
        'label' => 'Intro text',
        'rows' => 3,
      ])
      ->addLink('call_to_action', [
        'label' => 'Call to action',
        'return_format' => 'array',
      ])
      ->addPartial(Alignment::class)
      ->addPartial(FullHeight::class);

    return $fields->build();
  }
}
