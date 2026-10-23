@php
  $background = match ($background_color) {
    'primary' => 'bg-primary',
    'secondary' => 'bg-secondary',
    'tertiary' => 'bg-tertiary',
    'white' => 'bg-white',
    default => 'bg-black',
  };

  $darkText = in_array($background_color, ['tertiary', 'white'], true);
  $text = $darkText ? 'text-black' : 'text-white';
  $muted = $darkText ? 'text-black/70' : 'text-white/75';

  $profiles = is_array($profiles) ? array_values($profiles) : [];
  $leftProfiles = [];
  $rightProfiles = [];

  foreach ($profiles as $index => $profile) {
    if ($index % 2 === 0) {
      $rightProfiles[] = ['profile' => $profile, 'order' => $index + 1];
    } else {
      $leftProfiles[] = ['profile' => $profile, 'order' => $index + 1];
    }
  }

  $height = $full_height ? 'min-h-[calc(100svh-var(--site-header-offset))]' : '';
@endphp

<section {{ $attributes->merge(['class' => "profiles relative overflow-hidden py-20 pb-32 lg:py-28 lg:pb-40 $background $text $height"]) }}>
  <div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col gap-y-16 lg:grid lg:grid-cols-2 lg:gap-x-16 lg:gap-y-0">
      <div class="contents lg:block lg:space-y-20">
        @if ($title)
          <h2 class="font-display text-3xl font-normal leading-none text-center sm:text-4xl lg:text-left lg:text-8xl">
            {{ $title }}
          </h2>
        @endif

        @foreach ($leftProfiles as $item)
          @include('blocks.partials.profile-card', [
            'profile' => $item['profile'],
            'order' => $item['order'],
            'text' => $text,
            'muted' => $muted,
          ])
        @endforeach
      </div>

      <div class="contents lg:block lg:space-y-20">
        @foreach ($rightProfiles as $item)
          @include('blocks.partials.profile-card', [
            'profile' => $item['profile'],
            'order' => $item['order'],
            'text' => $text,
            'muted' => $muted,
          ])
        @endforeach
      </div>
    </div>
  </div>

  @if ($show_brand_mark)
    <div class="absolute bottom-6 left-4 sm:left-6 lg:left-8" aria-hidden="true">
      @include('partials.brand-mark', [
        'class' => 'h-16 w-16 text-current lg:h-20 lg:w-20',
      ])
    </div>
  @endif

  @if ($full_height)
    @include('partials.scroll-arrow')
  @endif
</section>
