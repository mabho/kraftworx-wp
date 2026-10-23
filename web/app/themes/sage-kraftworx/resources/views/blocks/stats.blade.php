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

  $barColor = match ($background_color) {
    'primary', 'secondary' => 'bg-white',
    'tertiary' => 'bg-black',
    default => 'bg-primary',
  };

  $stats = is_array($stats) ? array_values($stats) : [];
  $columnCount = count($stats);

  $grid = match (true) {
    $columnCount >= 4 => 'grid-cols-2 2xl:grid-cols-4',
    $columnCount === 3 => 'grid-cols-2 xl:grid-cols-3',
    default => 'grid-cols-2',
  };

  $labelSize = $columnCount === 2
    ? 'lg:text-stat-label'
    : 'lg:text-stat-label-compact';

  $hasHeader = !empty($section_eyebrow) || !empty($title) || !empty($intro_text);

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
          @if ($section_eyebrow)
            <p class="mb-4 text-sm font-extrabold uppercase tracking-[0.12em] md:text-lg md:tracking-[0.2em] lg:text-label">
              {!! nl2br(e($section_eyebrow)) !!}
            </p>
          @endif

          @if ($title)
            <h2 class="font-sans font-extrabold leading-[1.05] text-5xl lg:text-6xl xl:text-section">
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
          @if ($right_content)
            <div class="stats-content mb-8">
              {!! wp_kses_post($right_content) !!}
            </div>
          @endif

          <div class="mb-8 h-6 w-full bg-current"></div>

          @if ($eyebrow)
            <p class="mb-8 text-sm font-extrabold uppercase tracking-[0.12em] md:text-lg md:tracking-[0.2em] lg:text-label">
              {!! nl2br(e($eyebrow)) !!}
            </p>
          @endif

          <dl class="stats-grid grid {{ $grid }}" data-count="{{ $columnCount }}">
            @foreach ($stats as $stat)
              @php
                $itemBottom = match (true) {
                  $columnCount >= 4 && $loop->index < 2 => 'md:pb-0 2xl:pb-30',
                  $columnCount === 3 && $loop->index < 2 => 'md:pb-0 xl:pb-30',
                  default => 'pb-8 xl:pb-30',
                };

                $progress = isset($stat['progress']) && $stat['progress'] !== ''
                  ? max(0, min(100, (int) $stat['progress']))
                  : 0;
              @endphp

              <div class="stats-item flex flex-col pb-0 {{ $itemBottom }}">
                @if (!empty($stat['eyebrow']))
                  <p class="mb-3 text-sm font-extrabold uppercase tracking-[0.12em] md:text-lg md:tracking-[0.2em] lg:text-label">
                    {!! nl2br(e($stat['eyebrow'])) !!}
                  </p>
                @endif

                @if (isset($stat['value']) && $stat['value'] !== '')
                  <dt class="text-5xl font-extrabold leading-none sm:text-6xl xl:text-7xl 2xl:text-stat {{ $valueColor }}">
                    {{ $stat['value'] }}
                  </dt>
                @endif

                @if (isset($stat['label']) && $stat['label'] !== '')
                  <dd class="mt-4 text-xl font-medium {{ $labelSize }}">
                    {!! nl2br(e($stat['label'])) !!}
                  </dd>
                @endif

                @if ($progress > 0)
                  <div class="mt-auto pt-10 w-full xl:w-[90%] 2xl:w-[80%]">
                    <div class="h-4 w-full bg-current/20">
                      <div class="h-full {{ $barColor }}" style="width: {{ $progress }}%"></div>
                    </div>

                    @if (!empty($stat['bar_label']))
                      <p class="mt-3 text-lg uppercase tracking-[0.1em] text-current/70">
                        {{ $stat['bar_label'] }}
                      </p>
                    @endif
                  </div>
                @endif
              </div>
            @endforeach
          </dl>
        @endif

        @if ($source)
          <div class="border-t pt-10 {{ $ruleColor }}">
            <p class="text-lg font-normal md:text-note">
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
