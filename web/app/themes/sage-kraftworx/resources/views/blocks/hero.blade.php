@php
  $image = is_array($background_image) ? ($background_image['url'] ?? null) : null;
  $imageAlt = is_array($background_image) ? ($background_image['alt'] ?? '') : '';

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

  $height = $full_height ? 'min-h-screen' : '';
@endphp

<section {{ $attributes->merge(['class' => "hero relative isolate overflow-hidden $background"]) }}>
  @if ($image)
    <img
      src="{{ $image }}"
      alt="{{ $imageAlt }}"
      class="absolute inset-0 -z-20 h-full w-full object-cover"
    />
    <div class="absolute inset-0 -z-10 bg-black/45"></div>
  @endif

  <div class="container mx-auto flex {{ $height }} flex-col justify-center px-4 py-24 sm:px-6 lg:px-8">
    <div class="{{ $alignment['content'] }} max-w-5xl {{ $alignment['text'] }}">
      @if ($title)
        <h1 class="text-[clamp(2.75rem,10vw,var(--text-hero))] leading-[0.95] tracking-tight">
          {{ $title }}
        </h1>
      @endif

      @if ($intro_text)
        <p class="mt-8 max-w-md text-xl font-light leading-relaxed sm:text-2xl lg:text-lead {{ $alignment['inner'] }}">
          {!! nl2br(e($intro_text)) !!}
        </p>
      @endif

      @if (is_array($call_to_action) && !empty($call_to_action['url']))
        <a
          href="{{ $call_to_action['url'] }}"
          @if (!empty($call_to_action['target'])) target="{{ $call_to_action['target'] }}" rel="noopener noreferrer" @endif
          class="mt-10 inline-flex border border-current px-5 py-3 text-lg font-light uppercase tracking-[0.2em] transition {{ $buttonHover }}"
        >
          {{ $call_to_action['title'] ?: 'Learn More' }}
        </a>
      @endif
    </div>
  </div>

  @if ($full_height)
    @include('partials.scroll-arrow')
  @endif
</section>
