<header class="site-header fixed inset-x-0 top-0 z-50 border-b border-white/10 bg-black text-white" data-site-header>
  <div class="container mx-auto grid h-16 grid-cols-[1fr_auto] items-center gap-6 px-4 sm:px-6 md:grid-cols-[auto_1fr_auto] lg:grid-cols-[1fr_auto_1fr] lg:px-8" data-site-header-bar>
    @include('partials.site-logo', ['class' => 'justify-self-start'])

    @if (has_nav_menu('primary_navigation'))
      <nav class="site-nav hidden justify-self-center md:block" aria-label="{{ wp_get_nav_menu_name('primary_navigation') }}">
        {!! wp_nav_menu([
          'theme_location' => 'primary_navigation',
          'container' => false,
          'depth' => 1,
          'fallback_cb' => false,
          'menu_class' => 'menu flex items-center gap-6 lg:gap-10',
          'menu_id' => 'primary-menu-desktop',
        ]) !!}
      </nav>
    @endif

    <div class="flex items-center justify-self-end gap-4">
      @include('partials.linkedin-link', ['class' => 'hidden items-center justify-center text-white/80 transition hover:text-white md:inline-flex'])

      @if (has_nav_menu('primary_navigation'))
        <button
          type="button"
          class="inline-flex items-center justify-center text-white/80 transition hover:text-white md:hidden"
          aria-controls="mobile-menu"
          aria-expanded="false"
          aria-label="Toggle navigation"
          data-menu-toggle
        >
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-6 w-6" aria-hidden="true" data-menu-icon-open>
            <path stroke-linecap="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="hidden h-6 w-6" aria-hidden="true" data-menu-icon-close>
            <path stroke-linecap="round" d="M6 18 18 6M6 6l12 12" />
          </svg>
        </button>
      @endif
    </div>
  </div>

  @if (has_nav_menu('primary_navigation'))
    <div id="mobile-menu" class="hidden border-t border-white/10 bg-black md:hidden" data-mobile-menu>
      <div class="container mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <nav class="site-nav site-nav--mobile" aria-label="{{ wp_get_nav_menu_name('primary_navigation') }}">
          {!! wp_nav_menu([
            'theme_location' => 'primary_navigation',
            'container' => false,
            'depth' => 1,
            'fallback_cb' => false,
            'menu_class' => 'menu flex flex-col gap-4',
            'menu_id' => 'primary-menu-mobile',
          ]) !!}
        </nav>

        @include('partials.linkedin-link', [
          'class' => 'mt-6 inline-flex items-center gap-2 text-sm uppercase tracking-[0.1em] text-white/80 transition hover:text-white',
          'label' => 'LinkedIn',
        ])
      </div>
    </div>
  @endif
</header>
