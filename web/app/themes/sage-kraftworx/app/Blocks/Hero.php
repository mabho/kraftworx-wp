<?php

namespace App\Blocks;

use App\Blocks\Concerns\HasLayoutControls;
use App\Fields\Partials\Alignment;
use App\Fields\Partials\FullHeight;
use App\Fields\Partials\VerticalAlignment;
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
    $callToAction = get_field('call_to_action');
    $overlay = get_field('image_overlay');
    $parallax = get_field('parallax');

    return [
      'background_image' => get_field('background_image'),
      'background_color' => get_field('background_color') ?: 'black',
      'parallax' => $parallax === null ? false : (bool) $parallax,
      'image_overlay' => $overlay === null ? true : (bool) $overlay,
      'title' => get_field('title'),
      'intro_text' => get_field('intro_text'),
      'call_to_action' => $callToAction,
      'call_to_action_is_external' => $this->isExternalUrl(
        is_array($callToAction) ? ($callToAction['url'] ?? null) : null
      ),
    ] + $this->alignment() + $this->verticalAlignment() + $this->fullHeight();
  }

  /**
   * Determine whether a URL points outside the current site.
   */
  protected function isExternalUrl(?string $url): bool
  {
    $host = wp_parse_url((string) $url, PHP_URL_HOST);
    $homeHost = wp_parse_url(home_url('/'), PHP_URL_HOST);

    if (! $host || ! $homeHost) {
      return false;
    }

    $normalize = static fn (string $value): string => strtolower(
      preg_replace('/^www\./i', '', $value) ?? $value
    );

    return $normalize($host) !== $normalize($homeHost);
  }

  /**
   * The block field group.
   */
  public function fields(): array
  {
    $fields = Builder::make('hero');

    $fields
      ->addAccordion('Content', [
        'open' => 1,
        'multi_expand' => 1,
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
      ->addLink('call_to_action', [
        'label' => 'Call to action',
        'instructions' => 'External links open in a new tab automatically.',
        'return_format' => 'array',
      ])
      ->addAccordion('Layout', [
        'open' => 1,
        'multi_expand' => 1,
      ])
      ->addImage('background_image', [
        'label' => 'Background image',
        'return_format' => 'array',
        'preview_size' => 'medium',
      ])
      ->addTrueFalse('parallax', [
        'label' => 'Parallax background',
        'instructions' => 'Apply a subtle scroll-based parallax effect to the background image.',
        'default_value' => 0,
        'ui' => 1,
      ])
      ->addTrueFalse('image_overlay', [
        'label' => 'Image shade',
        'instructions' => 'Apply the dark shade over the background image for text contrast.',
        'default_value' => 1,
        'ui' => 1,
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
      ->addPartial(Alignment::class)
      ->addPartial(VerticalAlignment::class)
      ->addPartial(FullHeight::class);

    return $fields->build();
  }
}
