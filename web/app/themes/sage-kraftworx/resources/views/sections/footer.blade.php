@php
  $linkedinUrl = 'https://www.linkedin.com/';
  $footerCenter = get_field('footer_center', 'option') ?: '3214 N UNIVERSITY AVE / SUITE 140 / PROVO, UT 84604 / USA';
@endphp

<footer class="site-footer border-t border-white/10 bg-black text-white">
  <div class="container mx-auto px-4 pb-10 pt-6 sm:px-6 lg:px-8">
    <div class="flex justify-end">
      <button
        type="button"
        class="inline-flex items-center justify-center text-white/80 transition hover:text-white"
        aria-label="Back to top"
        data-scroll-top
      >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-6 w-6" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 19.5v-15m0 0l-6 6m6-6l6 6" />
        </svg>
      </button>
    </div>

    <div class="grid gap-8 pt-12 md:grid-cols-[1fr_auto_1fr] md:items-center">
      @include('partials.site-logo', ['class' => 'justify-self-start'])

      <div class="text-center text-sm uppercase tracking-[0.08em] text-white/80">
        {!! nl2br(e($footerCenter)) !!}
      </div>

      <div class="flex items-center gap-8 justify-self-center md:justify-self-end">
        <a
          href="{{ home_url('/contact/') }}"
          class="text-sm uppercase tracking-[0.1em] text-white/80 transition hover:text-white"
        >
          Contact
        </a>

        <a
          href="{{ $linkedinUrl }}"
          target="_blank"
          rel="noopener noreferrer"
          class="inline-flex text-white/80 transition hover:text-white"
          aria-label="LinkedIn"
        >
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5" aria-hidden="true">
            <path d="M4.98 3.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5zM3 9h4v12H3V9zm6 0h3.8v1.7h.05c.53-1 1.83-2.05 3.77-2.05 4.03 0 4.78 2.65 4.78 6.1V21h-4v-5.4c0-1.29-.02-2.95-1.8-2.95-1.8 0-2.08 1.4-2.08 2.85V21H9V9z"/>
          </svg>
        </a>
      </div>
    </div>
  </div>
</footer>
