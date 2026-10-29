@php
  $order = $order ?? 0;
@endphp

<article class="profile-card" style="order: {{ $order }}">
  <div class="profile-portrait">
    @if ($profile['portrait'])
      {!! $profile['portrait'] !!}
    @endif
  </div>

  <div class="profile-content">
    <h3 class="text-2xl font-extrabold leading-tight lg:text-3xl {{ $text }}">
      {{ $profile['name'] }}
    </h3>

    @if ($profile['role'])
      <p class="mt-1 text-lg font-light italic lg:text-xl {{ $muted }}">
        {{ $profile['role'] }}
      </p>
    @endif

    @if ($profile['bio'])
      <div class="profile-bio mt-5 text-xl font-light leading-snug {{ $muted }}">
        {!! wp_kses_post($profile['bio']) !!}
      </div>
    @endif
  </div>
</article>
