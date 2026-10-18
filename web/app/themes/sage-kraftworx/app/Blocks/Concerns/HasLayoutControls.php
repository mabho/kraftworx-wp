<?php

namespace App\Blocks\Concerns;

trait HasLayoutControls
{
  /**
   * Retrieve the alignment value for the block.
   */
  protected function alignment(): array
  {
    return [
      'alignment' => get_field('alignment') ?: 'right',
    ];
  }

  /**
   * Retrieve the full-height value for the block.
   */
  protected function fullHeight(bool $default = true): array
  {
    $fullHeight = get_field('full_height');

    return [
      'full_height' => $fullHeight === null ? $default : (bool) $fullHeight,
    ];
  }
}
