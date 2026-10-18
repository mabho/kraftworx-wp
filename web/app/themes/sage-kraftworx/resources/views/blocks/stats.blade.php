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
    $columnCount >= 4 => 'md:grid-cols-2 2xl:grid-cols-4',
    $columnCount === 3 => 'md:grid-cols-2 xl:grid-cols-3',
    default => 'md:grid-cols-2',
  };

  $labelSize = $columnCount === 2
    ? 'lg:text-stat-label'
    : 'lg:text-stat-label-compact';

  $hasHeader = !empty($title) || !empty($intro_text);

  $gridLayout = $hasHeader
    ? 'lg:grid-cols-[minmax(0,4fr)_minmax(0,8fr)]'
    : 'lg:grid-cols-1';

  $ruleColor = in_array($background_color, ['primary', 'secondary', 'black'], true)
    ? 'border-white/20'
    : 'border-black/20';

  $height = $full_height ? 'flex min-h-[calc(100svh-var(--site-header-offset))] flex-col justify-center' : '';
@endphp

<section {{ $attributes->merge(['class' => "stats relative overflow-hidden py-16 lg:py-24 $background $height"]) }}>
  <div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid gap-12 lg:gap-16 {{ $gridLayout }}">
      @if ($hasHeader)
        <div class="min-w-0 xl:pe-12 2xl:pe-16">
          @if ($title)
            <h2 class="font-sans text-4xl font-extrabold leading-[1.05] sm:text-5xl lg:text-6xl xl:text-section">
              {!! nl2br(e($title)) !!}
            </h2>
          @endif

          @if ($intro_text)
            <p class="mt-6 text-2xl font-medium md:text-3xl lg:text-4xl xl:text-section-intro">
              {!! nl2br(e($intro_text)) !!}
            </p>
          @endif
        </div>
      @endif

      <div class="min-w-0">
        @if ($stats)
          <div class="mb-8 h-6 w-full bg-current"></div>

          @if ($eyebrow)
            <p class="mb-8 text-lg font-extrabold uppercase tracking-[0.2em] md:text-xl lg:text-label">
              {{ $eyebrow }}
            </p>
          @endif

          <dl class="stats-grid grid {{ $grid }}" data-count="{{ $columnCount }}">
            @foreach ($stats as $stat)
              @php
                $itemBottom = match (true) {
                  $columnCount >= 4 && $loop->index < 2 => 'md:pb-0 2xl:pb-10',
                  $columnCount === 3 && $loop->index < 2 => 'md:pb-0 xl:pb-10',
                  default => '',
                };
              @endphp

              <div class="stats-item pb-10 {{ $itemBottom }}">
                @if (isset($stat['value']) && $stat['value'] !== '')
                  <dt class="text-5xl font-extrabold leading-none sm:text-6xl lg:text-stat {{ $valueColor }}">
                    {{ $stat['value'] }}
                  </dt>
                @endif

                @if (isset($stat['label']) && $stat['label'] !== '')
                  <dd class="mt-4 text-xl font-medium {{ $labelSize }}">
                    {!! nl2br(e($stat['label'])) !!}
                  </dd>
                @endif
              </div>
            @endforeach
          </dl>
        @endif

        @if ($source)
          <div class="border-t pt-4 {{ $ruleColor }}">
            <p class="text-note font-normal">
              {{ $source }}
            </p>
          </div>
        @endif
      </div>
    </div>
  </div>

  @if ($full_height)
    @include('partials.scroll-arrow')
  @endif
</section>
