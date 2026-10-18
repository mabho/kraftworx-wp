@php
  $background = match ($background_color) {
    'primary' => 'bg-primary text-white',
    'secondary' => 'bg-secondary text-white',
    'tertiary' => 'bg-tertiary text-black',
    'black' => 'bg-black text-white',
    default => 'bg-white text-black',
  };

  $valueColor = match ($background_color) {
    'primary', 'secondary' => 'text-white',
    'tertiary' => 'text-black',
    default => 'text-primary',
  };

  $stats = is_array($stats) ? array_values($stats) : [];
  $columnCount = count($stats);

  $grid = match (true) {
    $columnCount >= 4 => 'md:grid-cols-2 xl:grid-cols-4',
    $columnCount === 3 => 'md:grid-cols-2 xl:grid-cols-3',
    default => 'md:grid-cols-2',
  };

  $labelSize = $columnCount === 2
    ? 'lg:text-stat-label'
    : 'lg:text-stat-label-compact';

  $hasHeader = !empty($title) || !empty($intro_text);
@endphp

<section {{ $attributes->merge(['class' => "stats py-16 lg:py-24 $background"]) }}>
  <div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid gap-12 lg:grid-cols-12 lg:gap-16">
      @if ($hasHeader)
        <div class="lg:col-span-4">
          @if ($title)
            <h2 class="font-sans text-4xl font-extrabold leading-[1.05] tracking-tight sm:text-5xl lg:text-section">
              {{ $title }}
            </h2>
          @endif

          @if ($intro_text)
            <p class="mt-6 text-2xl font-medium leading-snug whitespace-pre-line md:text-3xl lg:text-section-intro">
              {{ $intro_text }}
            </p>
          @endif
        </div>
      @endif

      <div class="{{ $hasHeader ? 'lg:col-span-8' : 'lg:col-span-12' }}">
        @if ($eyebrow)
          <p class="mb-8 text-lg font-extrabold uppercase tracking-[0.2em] md:text-xl lg:text-label">
            {{ $eyebrow }}
          </p>
        @endif

        @if ($stats)
          <dl class="grid gap-x-8 gap-y-12 {{ $grid }}">
            @foreach ($stats as $stat)
              <div>
                @if (isset($stat['value']) && $stat['value'] !== '')
                  <dt class="text-5xl font-extrabold leading-none sm:text-6xl lg:text-stat {{ $valueColor }}">
                    {{ $stat['value'] }}
                  </dt>
                @endif

                @if (isset($stat['label']) && $stat['label'] !== '')
                  <dd class="mt-4 text-xl font-medium leading-snug whitespace-pre-line {{ $labelSize }}">
                    {{ $stat['label'] }}
                  </dd>
                @endif
              </div>
            @endforeach
          </dl>
        @endif

        @if ($source)
          <p class="mt-12 text-note font-normal leading-relaxed whitespace-pre-line">
            {{ $source }}
          </p>
        @endif
      </div>
    </div>
  </div>
</section>
