@php
  $status = in_array($status, ['success', 'error'], true) ? $status : '';
  $inputClass = 'w-full rounded-lg border-0 bg-white px-5 py-4 text-base uppercase tracking-[0.1em] text-black placeholder:text-black/50 focus:outline-none focus:ring-2 focus:ring-primary';
  $formId = wp_unique_id('contact-');
@endphp

<div {{ $attributes->merge(['class' => 'contact mx-auto w-full text-white']) }}>
  @if ($title)
    <h2 class="text-center font-display text-4xl font-normal leading-none sm:text-5xl lg:text-6xl">
      {!! nl2br(e($title)) !!}
    </h2>
  @endif

  @if ($intro_text)
    <p class="mt-6 text-center text-xl font-light leading-snug sm:text-2xl">
      {!! nl2br(e($intro_text)) !!}
    </p>
  @endif

  @if ($status === 'success')
    <p class="mt-8 border border-white/20 px-5 py-4 text-center text-base" role="status">
      {{ $confirmation_message }}
    </p>
  @elseif ($status === 'error')
    <p class="mt-8 border border-white/20 px-5 py-4 text-center text-base" role="alert">
      Something went wrong. Please check your details and try again.
    </p>
  @endif

  <form id="{{ $formId }}" action="{{ esc_url(admin_url('admin-post.php')) }}" method="post" class="space-y-4">
    <input type="hidden" id="{{ $formId }}-nonce" name="kraftworx_contact_nonce" value="{{ wp_create_nonce('kraftworx_contact') }}">
    <input type="hidden" name="_wp_http_referer" value="{{ esc_url(get_permalink() ?: home_url('/')) }}">
    <input type="hidden" name="action" value="kraftworx_contact">

    <div class="sr-only" aria-hidden="true">
      <label for="{{ $formId }}-website">Website</label>
      <input id="{{ $formId }}-website" name="website" type="text" tabindex="-1" autocomplete="off">
    </div>

    <div>
      <label for="{{ $formId }}-name" class="sr-only">Your name</label>
      <input id="{{ $formId }}-name" name="name" type="text" required placeholder="YOUR NAME" class="{{ $inputClass }}">
    </div>

    <div>
      <label for="{{ $formId }}-email" class="sr-only">Your email</label>
      <input id="{{ $formId }}-email" name="email" type="email" required placeholder="YOUR EMAIL" class="{{ $inputClass }}">
    </div>

    <div>
      <label for="{{ $formId }}-message" class="sr-only">How can we help?</label>
      <textarea id="{{ $formId }}-message" name="message" rows="6" required placeholder="HOW CAN WE HELP?" class="min-h-48 resize-y {{ $inputClass }}"></textarea>
    </div>

    <button
      type="submit"
      class="inline-flex cursor-pointer rounded-lg border border-white px-6 py-3 text-lg font-light uppercase tracking-[0.2em] transition hover:bg-white hover:text-black focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white"
    >
      {{ $submit_label }}
    </button>
  </form>
</div>
