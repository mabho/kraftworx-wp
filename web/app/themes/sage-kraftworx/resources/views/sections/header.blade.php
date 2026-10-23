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
      @include('partials.linkedin-link', ['class' => 'inline-flex items-center justify-center text-white/80 transition hover:text-white'])

      @if (has_nav_menu('primary_navigation'))
        <button
          type="button"
          class="inline-flex cursor-pointer items-center justify-center text-white/80 transition hover:text-white md:hidden"
          aria-controls="mobile-menu"
          aria-expanded="false"
          aria-label="Open navigation"
          data-menu-toggle
        >
          <span class="menu-icon" aria-hidden="true">
            <span class="menu-icon__bar"></span>
            <span class="menu-icon__bar"></span>
            <span class="menu-icon__bar"></span>
          </span>
        </button>
      @endif
    </div>
  </div>

  @if (has_nav_menu('primary_navigation'))
    <div id="mobile-menu" class="mobile-menu fixed inset-x-0 bottom-0 top-[var(--site-header-offset)] z-40 overflow-y-auto border-t border-white/10 bg-black md:hidden" data-mobile-menu aria-hidden="true" inert>
      <div class="container mx-auto flex min-h-full flex-col justify-center px-4 py-12 sm:px-6 lg:px-8">
        <nav class="site-nav site-nav--mobile" aria-label="{{ wp_get_nav_menu_name('primary_navigation') }}">
          {!! wp_nav_menu([
            'theme_location' => 'primary_navigation',
            'container' => false,
            'depth' => 1,
            'fallback_cb' => false,
            'menu_class' => 'menu flex flex-col',
            'menu_id' => 'primary-menu-mobile',
          ]) !!}
        </nav>
      </div>
    </div>
  @endif
</header>
