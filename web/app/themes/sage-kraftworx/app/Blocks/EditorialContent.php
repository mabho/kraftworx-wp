<?php

namespace App\Blocks;

use Log1x\AcfComposer\Block;

class EditorialContent extends Block
{
  /**
   * The block name.
   *
   * @var string
   */
  public $name = 'Editorial content';

  /**
   * The block description.
   *
   * @var string
   */
  public $description = 'A fixed two-column layout with free block areas in each column.';

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
  public $keywords = ['editorial', 'columns', 'content', 'notes', 'sources'];

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
    return [];
  }

  /**
   * The block template.
   *
   * @var array
   */
  public $template = [
    'core/columns' => [
      'templateLock' => 'all',
      'innerBlocks' => [
        ['core/column' => ['width' => '34%', 'templateLock' => false]],
        ['core/column' => ['width' => '50%', 'templateLock' => false]],
      ],
    ],
  ];
}
