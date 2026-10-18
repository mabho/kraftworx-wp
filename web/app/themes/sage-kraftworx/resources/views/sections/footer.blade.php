@php
  $footerCenter = get_field('footer_center', 'option') ?: '3214 N UNIVERSITY AVE / SUITE 140 / PROVO, UT 84604 / USA';
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

    <div class="grid gap-8 pt-12 md:grid-cols-[1fr_auto_1fr] md:items-center">
      @include('partials.site-logo', ['class' => 'justify-self-start'])

      <div class="text-center text-sm uppercase tracking-[0.08em] text-white/80">
        {!! nl2br(e($footerCenter)) !!}
      </div>

      <div class="flex items-center gap-8 justify-self-center md:justify-self-end">
        @if (has_nav_menu('footer_navigation'))
          <nav class="footer-nav" aria-label="{{ wp_get_nav_menu_name('footer_navigation') }}">
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

        @include('partials.linkedin-link', ['class' => 'inline-flex text-white/80 transition hover:text-white'])
      </div>
    </div>
  </div>
</footer>
