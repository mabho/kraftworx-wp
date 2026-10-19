@php
  $image = is_array($image) ? $image : null;
  $url = $image['url'] ?? null;
  $alt = $image['alt'] ?? '';
  $width = $image['width'] ?? null;
  $height = $image['height'] ?? null;

  $background = match ($background_color) {
    'primary' => 'bg-primary text-white',
    'secondary' => 'bg-secondary text-white',
    'tertiary' => 'bg-tertiary text-black',
    'black' => 'bg-black text-white',
    default => 'bg-white text-black',
  };

  $heightClass = $full_height
    ? 'flex min-h-[calc(100svh-var(--site-header-offset))] flex-col justify-center'
    : '';
@endphp

<section {{ $attributes->merge(['class' => "image relative py-16 lg:py-24 $background $heightClass"]) }}>
  <div class="container mx-auto px-4 sm:px-6 lg:px-8">
    @if ($url)
      <img
        src="{{ $url }}"
        alt="{{ $alt }}"
        @if ($width && $height) width="{{ $width }}" height="{{ $height }}" @endif
        class="mx-auto block h-auto max-w-full"
        loading="lazy"
        decoding="async"
      >
    @endif
  </div>

  @if ($full_height)
    @include('partials.scroll-arrow')
  @endif
</section>
