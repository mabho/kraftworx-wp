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
   * Track how many Hero blocks have rendered in the current request.
   *
   * @var int
   */
  protected static int $renderCount = 0;

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
  public $description = 'A full-width hero with desktop/mobile background images, title, intro text, and call to action.';

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
    $largeIntro = get_field('large_intro');
    $title = get_field('title');
    $isFirstHero = is_page()
      && self::$renderCount === 0
      && trim((string) $title) !== '';

    if ($isFirstHero) {
      self::$renderCount++;
    }

    return [
      'is_first_hero' => $isFirstHero,
      'background_image' => get_field('background_image'),
      'mobile_background_image' => get_field('mobile_background_image'),
      'parallax_image' => get_field('parallax_image'),
      'parallax_effect' => get_field('parallax_effect') ?: 'none',
      'background_color' => get_field('background_color') ?: 'black',
      'parallax' => $parallax === null ? false : (bool) $parallax,
      'image_overlay' => $overlay === null ? true : (bool) $overlay,
      'title' => $title,
      'intro_text' => get_field('intro_text'),
      'large_intro' => $largeIntro === null ? false : (bool) $largeIntro,
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
        'instructions' => 'Used at 48rem and above when a mobile image is also set.',
        'return_format' => 'array',
        'preview_size' => 'medium',
      ])
      ->addImage('mobile_background_image', [
        'label' => 'Mobile background image',
        'instructions' => 'Optional. Used below 48rem. If no desktop image is set, this image is used at every size.',
        'return_format' => 'array',
        'preview_size' => 'medium',
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
      ->addTrueFalse('large_intro', [
        'label' => 'Larger intro text',
        'instructions' => 'Increase the intro text size without matching the title size.',
        'default_value' => 0,
        'ui' => 1,
      ])
      ->addPartial(Alignment::class)
      ->addPartial(VerticalAlignment::class)
      ->addPartial(FullHeight::class)
      ->addAccordion('Parallax effect', [
        'open' => 0,
        'multi_expand' => 1,
      ])
      ->addTrueFalse('parallax', [
        'label' => 'Enable parallax',
        'instructions' => 'Enable scroll parallax for the background image and the secondary image.',
        'default_value' => 0,
        'ui' => 1,
      ])
      ->addImage('parallax_image', [
        'label' => 'Secondary parallax image',
        'instructions' => 'Optional. Displayed at 48rem and above. Use an SVG for decorative arrows or vector graphics.',
        'return_format' => 'array',
        'preview_size' => 'medium',
        'conditional_logic' => [
          [
            [
              'field' => 'parallax',
              'operator' => '==',
              'value' => '1',
            ],
          ],
        ],
      ])
      ->addSelect('parallax_effect', [
        'label' => 'Parallax effect',
        'instructions' => 'Controls how the secondary image moves relative to the background. Horizontal effects also move vertically at the background rate.',
        'choices' => [
          'none' => 'None',
          'same-speed' => 'Same direction and speed as background',
          'same-speed-zoom' => 'Same speed with soft zoom-in',
          'slower' => 'Slower than background',
          'faster' => 'Faster than background',
          'left-to-right' => 'Left to right at background speed',
          'right-to-left' => 'Right to left at background speed',
        ],
        'default_value' => 'none',
        'conditional_logic' => [
          [
            [
              'field' => 'parallax',
              'operator' => '==',
              'value' => '1',
            ],
          ],
        ],
      ]);

    return $fields->build();
  }
}
