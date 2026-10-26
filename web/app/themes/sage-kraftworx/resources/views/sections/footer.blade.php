@php
  $footerCenter = get_field('footer_center', 'option') ?: '3214 N UNIVERSITY AVE / SUITE 140 / PROVO, UT 84604 / USA';
  $footerCenterParts = preg_split('/\s*\/\s*|\R/', $footerCenter, -1, PREG_SPLIT_NO_EMPTY) ?: [];
  $footerCenter = implode('', array_map(
    static fn (string $part): string => '<span class="footer-address__part">' . e(trim($part)) . '</span>',
    $footerCenterParts
  ));
@endphp

<footer class="site-footer border-t border-white/10 bg-black text-white">
  <div class="container mx-auto px-4 pb-10 pt-6 sm:px-6 lg:px-8">
    <div class="flex justify-end">
      <button
        type="button"
        class="arrow-hover-up inline-flex cursor-pointer items-center justify-center text-white/80 transition hover:text-white"
        aria-label="Back to top"
        data-scroll-top
      >
        @include('partials.arrow-icon', ['class' => 'h-10 w-10 rotate-180'])
      </button>
    </div>

    <div class="grid grid-cols-[1fr_auto] items-end gap-x-6 gap-y-4 pt-10 lg:grid-cols-[1fr_auto_1fr] lg:items-center lg:gap-8 lg:pt-12">
      @include('partials.site-logo', ['class' => 'order-2 col-span-2 justify-self-start lg:order-0 lg:col-auto'])

      <div class="footer-address order-3 text-left text-sm uppercase tracking-[0.08em] text-white/80 lg:order-0 lg:col-auto lg:text-center">
        {!! $footerCenter !!}
      </div>

      <div class="contents lg:flex lg:items-center lg:gap-8 lg:justify-self-end">
        @if (has_nav_menu('footer_navigation'))
          <nav class="footer-nav order-1 col-span-2 lg:order-0 lg:col-auto" aria-label="{{ wp_get_nav_menu_name('footer_navigation') }}">
            {!! wp_nav_menu([
              'theme_location' => 'footer_navigation',
              'container' => false,
              'depth' => 1,
              'fallback_cb' => false,
              'menu_class' => 'menu flex items-center gap-8',
              'menu_id' => 'footer-menu',
            ]) !!}
          </nav>
        @endif

        @include('partials.linkedin-link', [
          'class' => 'order-4 inline-flex h-10 w-10 items-center justify-center justify-self-end self-end text-white/80 transition hover:text-white lg:order-none lg:col-auto lg:justify-self-auto lg:self-auto',
          'iconClass' => 'h-6 w-6',
        ])
      </div>
    </div>
  </div>
</footer>
