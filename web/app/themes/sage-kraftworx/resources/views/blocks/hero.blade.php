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

  <div class="mx-auto flex {{ $height }} max-w-7xl flex-col justify-center px-4 py-24 sm:px-6 lg:px-8">
    <div class="{{ $alignment['content'] }} max-w-2xl {{ $alignment['text'] }}">
      @if ($title)
        <h1 class="text-4xl font-extrabold leading-[1.05] tracking-tight sm:text-5xl lg:text-7xl">
          {{ $title }}
        </h1>
      @endif

      @if ($intro_text)
        <p class="mt-6 max-w-md leading-relaxed sm:text-lg {{ $alignment['inner'] }}">
          {{ $intro_text }}
        </p>
      @endif

      @if (is_array($call_to_action) && !empty($call_to_action['url']))
        <a
          href="{{ $call_to_action['url'] }}"
          @if (!empty($call_to_action['target'])) target="{{ $call_to_action['target'] }}" rel="noopener noreferrer" @endif
          class="mt-8 inline-flex border border-current px-5 py-3 text-xs font-semibold uppercase tracking-[0.2em] transition {{ $buttonHover }}"
        >
          {{ $call_to_action['title'] ?: 'Learn More' }}
        </a>
      @endif
    </div>
  </div>

  @if ($full_height)
    <div class="absolute bottom-6 right-6 opacity-80" aria-hidden="true">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-6 w-6">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0l-6-6m6 6l6-6" />
      </svg>
    </div>
  @endif
</section>
