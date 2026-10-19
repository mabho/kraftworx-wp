<?php

namespace App\Blocks;

use App\Blocks\Concerns\HasLayoutControls;
use App\Fields\Partials\FullHeight;
use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class Stats extends Block
{
  use HasLayoutControls;

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
      'section_eyebrow' => get_field('section_eyebrow'),
      'right_content' => get_field('right_content'),
      'title' => get_field('title'),
      'intro_text' => get_field('intro_text'),
      'eyebrow' => get_field('eyebrow'),
      'source' => get_field('source'),
      'background_color' => get_field('background_color') ?: 'white',
      'stats' => get_field('stats') ?: [],
    ] + $this->fullHeight(false);
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
      ->addTextarea('section_eyebrow', [
        'label' => 'Section eyebrow',
        'instructions' => 'Optional label shown above the title.',
        'rows' => 2,
        'new_lines' => '',
      ])
      ->addTextarea('title', [
        'label' => 'Title',
        'rows' => 3,
        'new_lines' => '',
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
      ->addWysiwyg('right_content', [
        'label' => 'Right column content',
        'instructions' => 'Rich text shown above the stats. Use the Styles dropdown for the Lead paragraph and Accent, Eyebrow, or Muted spans.',
        'toolbar' => 'Kraftworx',
        'tabs' => 'visual',
        'media_upload' => 0,
      ])
      ->addTextarea('eyebrow', [
        'label' => 'Stats eyebrow',
        'instructions' => 'Optional uppercase label shown above the stats grid.',
        'rows' => 2,
        'new_lines' => '',
      ])
      ->addRepeater('stats', [
        'label' => 'Stats',
        'instructions' => 'Add between two and four stats.',
        'min' => 2,
        'max' => 4,
        'layout' => 'block',
        'button_label' => 'Add stat',
      ])
        ->addTextarea('eyebrow', [
          'label' => 'Card eyebrow',
          'instructions' => 'e.g. BEFORE, AFTER',
          'rows' => 2,
          'new_lines' => '',
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
        ->addRange('progress', [
          'label' => 'Progress',
          'instructions' => '0–100. Shows the bar when greater than 0.',
          'min' => 0,
          'max' => 100,
          'step' => 1,
          'default_value' => 0,
          'append' => '%',
        ])
        ->addText('bar_label', [
          'label' => 'Bar label',
          'instructions' => 'Optional label under the bar, e.g. 100% OF TRACK.',
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
      ])
      ->addPartial(FullHeight::class, [
        'default' => 0,
      ]);

    return $fields->build();
  }
}
