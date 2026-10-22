<!doctype html>
<html @php(language_attributes())>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preload" href="{{ Vite::asset('resources/fonts/replica-pro-regular.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ Vite::asset('resources/fonts/avenir-light.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ Vite::asset('resources/fonts/avenir-book.woff2') }}" as="font" type="font/woff2" crossorigin>
    @php(do_action('get_header'))
    @php(wp_head())

    <link
      rel="icon"
      type="image/svg+xml"
      sizes="any"
      href="{{ get_theme_file_uri('favicon.svg') }}?v={{ filemtime(get_theme_file_path('favicon.svg')) }}"
    >
    <link rel="alternate icon" href="{{ home_url('/favicon.ico') }}" sizes="any">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>

  <body @php(body_class())>
    @php(wp_body_open())

    <div id="app" class="flex min-h-[calc(100svh-var(--admin-bar-height))] flex-col">
      <a class="sr-only focus:not-sr-only" href="#main">
        {{ __('Skip to content', 'sage') }}
      </a>

      @include('sections.header')

      <main id="main" class="main flex-1 scroll-mt-16 pt-16">
        @yield('content')
      </main>

      @hasSection('sidebar')
        <aside class="sidebar">
          @yield('sidebar')
        </aside>
      @endif

      @include('sections.footer')
    </div>

    @php(do_action('get_footer'))
    @php(wp_footer())
  </body>
</html>
