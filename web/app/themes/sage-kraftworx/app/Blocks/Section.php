<?php

namespace App\Blocks;

use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class Section extends Block
{
  /**
   * The block name.
   *
   * @var string
   */
  public $name = 'Section';

  /**
   * The block description.
   *
   * @var string
   */
  public $description = 'A reusable layout section with container, width, columns, and background options.';

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
  public $icon = 'layout';

  /**
   * The block keywords.
   *
   * @var array
   */
  public $keywords = ['layout', 'section', 'container'];

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
   * The block template.
   *
   * @var array
   */
  public $template = [
    'core/paragraph' => ['placeholder' => 'Add content...'],
  ];

  /**
   * Data to be passed to the block before rendering.
   */
  public function with(): array
  {
    return [
      'contained' => get_field('contained') ?: 'contained',
      'column_width' => get_field('column_width') ?: 'default',
      'columns' => get_field('columns') ?: '100',
      'background_color' => get_field('background_color') ?: 'white',
    ];
  }

  /**
   * The block field group.
   */
  public function fields(): array
  {
    $fields = Builder::make('section');

    $fields
      ->addSelect('contained', [
        'label' => 'Container',
        'choices' => [
          'contained' => 'Contained',
          'uncontained' => 'Uncontained',
        ],
        'default_value' => 'contained',
      ])
      ->addSelect('column_width', [
        'label' => 'Content width',
        'choices' => [
          'default' => 'Default',
          'narrow' => 'Narrow',
        ],
        'default_value' => 'default',
      ])
      ->addSelect('columns', [
        'label' => 'Grid layout',
        'choices' => [
          '100' => '100',
          '50-50' => '50 / 50',
          '33-33-33' => '33 / 33 / 33',
        ],
        'default_value' => '100',
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
      ]);

    return $fields->build();
  }
}
