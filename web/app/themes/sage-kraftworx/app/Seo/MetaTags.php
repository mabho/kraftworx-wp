<?php

namespace App\Seo;

use Illuminate\Support\Facades\Vite;
use WP_Post;

class MetaTags
{
  public function boot(): void
  {
    add_action('wp_head', [$this, 'render'], 5);
  }

  public function render(): void
  {
    if (! is_singular()) {
      return;
    }

    $post = get_queried_object();

    if (! $post instanceof WP_Post) {
      return;
    }

    $meta = $this->resolve($post);
    $image = $meta['image'];

    if ($meta['description'] !== '') {
      $this->nameTag('description', $meta['description']);
    }

    $this->propertyTag('og:type', $post->post_type === 'post' ? 'article' : 'website');
    $this->propertyTag('og:title', $meta['title']);
    $this->propertyTag('og:description', $meta['description']);
    $this->propertyTag('og:url', (string) get_permalink($post));
    $this->propertyTag('og:site_name', (string) get_bloginfo('name'));
    $this->propertyTag('og:locale', str_replace('-', '_', get_locale()));

    if ($image) {
      $this->propertyTag('og:image', $image['url']);
      $this->propertyTag('og:image:width', (string) $image['width']);
      $this->propertyTag('og:image:height', (string) $image['height']);
      $this->propertyTag('og:image:alt', $image['alt']);
    }

    $this->nameTag('twitter:card', $image ? 'summary_large_image' : 'summary');
    $this->nameTag('twitter:title', $meta['title']);
    $this->nameTag('twitter:description', $meta['description']);

    if ($image) {
      $this->nameTag('twitter:image', $image['url']);
      $this->nameTag('twitter:image:alt', $image['alt']);
    }

    if ($post->post_type === 'post') {
      $this->propertyTag('article:published_time', (string) get_post_time(DATE_W3C, true, $post));
      $this->propertyTag('article:modified_time', (string) get_post_modified_time(DATE_W3C, true, $post));
    }
  }

  protected function resolve(WP_Post $post): array
  {
    $hero = $this->firstHero($post);

    $title = $this->cleanText($this->field('meta_title', $post->ID))
      ?: $this->cleanText($hero['title'] ?? '')
      ?: $this->cleanText(get_the_title($post))
      ?: (string) get_bloginfo('name');

    $description = $this->cleanText($this->field('meta_description', $post->ID))
      ?: $this->cleanText($hero['intro'] ?? '')
      ?: $this->cleanText(get_post_field('post_excerpt', $post))
      ?: $this->cleanText($post->post_content)
      ?: (string) get_bloginfo('description');

    return [
      'title' => $title,
      'description' => $description,
      'image' => $this->resolveImage($post, $title, $hero),
    ];
  }

  protected function resolveImage(WP_Post $post, string $title, ?array $hero): ?array
  {
    $candidates = [
      $this->field('social_image', $post->ID),
      $hero['image'] ?? null,
      $hero['mobile_image'] ?? null,
      get_post_thumbnail_id($post),
    ];

    foreach ($candidates as $candidate) {
      $imageId = $this->imageId($candidate);

      if (! $imageId) {
        continue;
      }

      $image = $this->attachmentImage($imageId, $title);

      if ($image) {
        return $image;
      }
    }

    return $this->defaultImage();
  }

  protected function firstHero(WP_Post $post): ?array
  {
    $block = $this->findBlock(parse_blocks((string) $post->post_content), 'acf/hero');

    if (! $block) {
      return null;
    }

    $data = $block['attrs']['data'] ?? [];

    return [
      'title' => $data['title'] ?? '',
      'intro' => $data['intro_text'] ?? '',
      'image' => $data['background_image'] ?? null,
      'mobile_image' => $data['mobile_background_image'] ?? null,
    ];
  }

  protected function findBlock(array $blocks, string $name): ?array
  {
    foreach ($blocks as $block) {
      if (($block['blockName'] ?? null) === $name) {
        return $block;
      }

      if (! empty($block['innerBlocks'])) {
        $found = $this->findBlock($block['innerBlocks'], $name);

        if ($found) {
          return $found;
        }
      }
    }

    return null;
  }

  protected function attachmentImage(int $imageId, string $title): ?array
  {
    $mime = (string) get_post_mime_type($imageId);

    if (! in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
      return null;
    }

    $image = wp_get_attachment_image_src($imageId, 'kraftworx-social')
      ?: wp_get_attachment_image_src($imageId, 'full');

    if (! $image) {
      return null;
    }

    $alt = trim((string) get_post_meta($imageId, '_wp_attachment_image_alt', true));

    return [
      'url' => (string) $image[0],
      'width' => (int) $image[1],
      'height' => (int) $image[2],
      'alt' => $alt ?: $title,
    ];
  }

  protected function defaultImage(): ?array
  {
    $path = 'resources/images/social-default.jpg';

    if (! file_exists(get_theme_file_path($path))) {
      return null;
    }

    try {
      $url = Vite::asset($path);
    } catch (\Throwable) {
      $url = get_theme_file_uri($path);
    }

    return [
      'url' => (string) $url,
      'width' => 1200,
      'height' => 630,
      'alt' => (string) get_bloginfo('name'),
    ];
  }

  protected function imageId(mixed $value): int
  {
    if (is_numeric($value)) {
      return absint($value);
    }

    if (is_array($value)) {
      return absint($value['ID'] ?? $value['id'] ?? 0);
    }

    return 0;
  }

  protected function cleanText(mixed $value): string
  {
    if (! is_scalar($value)) {
      return '';
    }

    $text = (string) preg_replace('/<!--.*?-->/s', ' ', (string) $value);
    $text = wp_strip_all_tags($text);
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    return trim((string) preg_replace('/\s+/u', ' ', $text));
  }

  protected function field(string $name, int $postId): mixed
  {
    return function_exists('get_field') ? get_field($name, $postId) : null;
  }

  protected function nameTag(string $name, string $content): void
  {
    if ($content === '') {
      return;
    }

    echo '<meta name="' . esc_attr($name) . '" content="' . esc_attr($content) . '">' . "\n";
  }

  protected function propertyTag(string $property, string $content): void
  {
    if ($content === '') {
      return;
    }

    echo '<meta property="' . esc_attr($property) . '" content="' . esc_attr($content) . '">' . "\n";
  }
}
