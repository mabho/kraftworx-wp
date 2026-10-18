<?php

namespace App\Blocks;

use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class Stats extends Block
{
  /**
   * The block name.
   *
   * @var string
   */
  public $name = 'Stats';

  /**
   * The block description.
   *
   * @var string
   */
  public $description = 'A section with a title, intro, and two to four key stats.';

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
  public $icon = 'chart-bar';

  /**
   * The block keywords.
   *
   * @var array
   */
  public $keywords = ['stats', 'numbers', 'metrics', 'kpi'];

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
      'title' => get_field('title'),
      'intro_text' => get_field('intro_text'),
      'eyebrow' => get_field('eyebrow'),
      'source' => get_field('source'),
      'background_color' => get_field('background_color') ?: 'white',
      'stats' => get_field('stats') ?: [],
    ];
  }

  /**
   * The block field group.
   */
  public function fields(): array
  {
    $fields = Builder::make('stats');

    $fields
      ->addAccordion('Content', [
        'open' => 1,
        'multi_expand' => 1,
      ])
      ->addText('title', [
        'label' => 'Title',
      ])
      ->addTextarea('intro_text', [
        'label' => 'Intro text',
        'rows' => 3,
        'new_lines' => '',
      ])
      ->addAccordion('Stats', [
        'open' => 1,
        'multi_expand' => 1,
      ])
      ->addText('eyebrow', [
        'label' => 'Stats label',
        'instructions' => 'Optional uppercase label shown above the stats.',
      ])
      ->addRepeater('stats', [
        'label' => 'Stats',
        'instructions' => 'Add between two and four stats.',
        'min' => 2,
        'max' => 4,
        'layout' => 'block',
        'button_label' => 'Add stat',
      ])
        ->addText('value', [
          'label' => 'Value',
          'instructions' => 'e.g. 69%, 40% lower, 1.7x',
        ])
        ->addTextarea('label', [
          'label' => 'Label',
          'rows' => 2,
          'new_lines' => '',
        ])
      ->endRepeater()
      ->addText('source', [
        'label' => 'Source note',
        'instructions' => 'Optional. Include a leading * if needed.',
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
      ]);

    return $fields->build();
  }
}
