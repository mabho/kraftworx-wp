@php
  $desktopImage = is_array($background_image) ? $background_image : null;
  $mobileImage = is_array($mobile_background_image) ? $mobile_background_image : null;

  $desktopId = (int) ($desktopImage['ID'] ?? 0);
  $mobileId = (int) ($mobileImage['ID'] ?? 0);
  $desktopUrl = $desktopImage['url'] ?? null;
  $mobileUrl = $mobileImage['url'] ?? null;

  $hasDesktop = $desktopId || $desktopUrl;
  $hasMobile = $mobileId || $mobileUrl;
  $hasMobileSource = $hasDesktop && $hasMobile;

  $fallbackId = $desktopId ?: $mobileId;
  $fallbackUrl = $desktopUrl ?: $mobileUrl;
  $fallbackAlt = $hasDesktop
    ? ($desktopImage['alt'] ?? ($desktopId ? get_post_meta($desktopId, '_wp_attachment_image_alt', true) : ''))
    : ($mobileImage['alt'] ?? ($mobileId ? get_post_meta($mobileId, '_wp_attachment_image_alt', true) : ''));

  $mobileSrcset = $mobileId ? wp_get_attachment_image_srcset($mobileId, 'full') : false;
  $mobileSrcset = $mobileSrcset ?: $mobileUrl;
  $mobileMime = $mobileId ? get_post_mime_type($mobileId) : null;

  $imageClass = 'absolute inset-0 h-full w-full object-cover' . ($parallax ? ' hero-parallax-image' : '');
  $imageAttrs = [
    'alt' => $fallbackAlt,
    'class' => $imageClass,
    'sizes' => '100vw',
    'loading' => 'eager',
  ];

  if ($parallax) {
    $imageAttrs['data-hero-parallax-image'] = '';
  }

  $background = match ($background_color) {
    'primary' => 'bg-primary text-white',
    'secondary' => 'bg-secondary text-white',
    'tertiary' => 'bg-tertiary text-black',
    'black' => 'bg-black text-white',
    default => 'bg-white text-black',
  };

  $buttonHover = in_array($background_color, ['tertiary', 'white'], true)
    ? 'hover:bg-black hover:text-white'
    : 'hover:bg-white hover:text-black';

  $alignment = match ($alignment ?? 'right') {
    'left' => ['content' => 'mr-auto', 'text' => 'text-left', 'inner' => 'mr-auto'],
    'center' => ['content' => 'mx-auto', 'text' => 'text-center', 'inner' => 'mx-auto'],
    default => ['content' => 'ml-auto', 'text' => 'text-right', 'inner' => 'ml-auto'],
  };

  $vertical = match ($vertical_alignment ?? 'center') {
    'top' => 'justify-start',
    default => 'justify-center',
  };

  $height = $full_height ? 'min-h-[calc(100svh-var(--site-header-offset))]' : '';

  $introSize = $large_intro
    ? 'text-2xl sm:text-3xl lg:text-large-lead'
    : 'text-xl sm:text-2xl lg:text-lead';
  $introWidth = 'max-w-lg';
  $hasCopy = !empty($intro_text) || (is_array($call_to_action) && !empty($call_to_action['url']));
@endphp

<section
  {{ $attributes->merge(['class' => "hero relative isolate overflow-hidden $background"]) }}
  data-background-color="{{ $background_color }}"
  data-full-height="{{ $full_height ? '1' : '0' }}"
  @if ($parallax) data-hero-parallax @endif
>
  @if ($hasDesktop || $hasMobile)
    <div class="hero-media absolute inset-0 -z-20">
      <picture class="absolute inset-0 block h-full w-full">
        @if ($hasMobileSource && $mobileSrcset)
          <source
            media="(max-width: 47.99rem)"
            srcset="{{ $mobileSrcset }}"
            sizes="100vw"
            @if ($mobileMime) type="{{ $mobileMime }}" @endif
          >
        @endif

        @if ($fallbackId)
          {!! wp_get_attachment_image($fallbackId, 'full', false, $imageAttrs) !!}
        @elseif ($fallbackUrl)
          <img
            src="{{ $fallbackUrl }}"
            alt="{{ $fallbackAlt }}"
            class="{{ $imageClass }}"
            loading="eager"
            sizes="100vw"
            @if ($parallax) data-hero-parallax-image @endif
          >
        @endif
      </picture>

      @if ($image_overlay)
        <div class="absolute inset-0 bg-black/45"></div>
      @endif
    </div>
  @endif

  <div class="hero-content container mx-auto flex {{ $height }} flex-col {{ $vertical }} px-4 py-24 sm:px-6 lg:px-8">
    <div class="hero-inner {{ $alignment['content'] }} max-w-5xl {{ $alignment['text'] }}">
      @if ($title)
        @if ($is_first_hero)
          <h1 class="hero-title text-[clamp(2.75rem,10vw,var(--text-hero))] leading-[0.875] tracking-tight {{ $alignment['text'] }}">
            {!! nl2br(e($title)) !!}
          </h1>
        @else
          <h2 class="hero-title text-[clamp(2.75rem,10vw,var(--text-hero))] leading-[0.875] tracking-tight {{ $alignment['text'] }}">
            {!! nl2br(e($title)) !!}
          </h2>
        @endif
      @endif

      <div class="hero-copy {{ $alignment['text'] }} {{ $hasCopy ? '' : 'hidden md:block' }}">
        @if ($intro_text)
          <p class="mt-8 {{ $introWidth }} {{ $introSize }} font-light {{ $alignment['inner'] }}">
            {!! nl2br(e($intro_text)) !!}
          </p>
        @endif

        @if (is_array($call_to_action) && !empty($call_to_action['url']))
          <a
            href="{{ $call_to_action['url'] }}"
            @if (!empty($call_to_action_is_external)) target="_blank" rel="noopener noreferrer" @endif
            class="mt-10 inline-flex rounded-lg border border-current px-4 py-2.5 text-base font-light uppercase tracking-[0.16em] transition md:px-5 md:py-3 md:text-lg md:tracking-[0.2em] {{ $buttonHover }}"
          >
            {{ $call_to_action['title'] ?: 'Learn More' }}
          </a>
        @endif
      </div>
    </div>
  </div>

  @if ($full_height)
    @include('partials.scroll-arrow')
  @endif
</section>
