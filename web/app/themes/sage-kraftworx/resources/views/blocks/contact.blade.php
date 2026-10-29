@php
  $status = in_array($status, ['success', 'error'], true) ? $status : '';
  $inputClass = 'w-full rounded-lg border-0 bg-white px-5 py-4 text-base uppercase tracking-[0.1em] text-black placeholder:text-black/50 focus:outline-none focus:ring-2 focus:ring-primary';
  $formId = wp_unique_id('contact-');
@endphp

<div {{ $attributes->merge(['class' => 'contact mx-auto w-full text-white']) }}>
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

    <div class="grid grid-cols-2 gap-4">
      @foreach ($fields as $field)
        @php
          $name = $field['name'];
          $id = $formId.'-'.sanitize_html_class($name);
          $placeholder = $field['placeholder'] ?: $field['label'];
          $fieldClass = in_array($name, ['firstname', 'lastname'], true)
            ? 'min-w-0'
            : 'col-span-2 min-w-0';
        @endphp

        @if ($field['hidden'])
          <input id="{{ $id }}" name="{{ $name }}" type="hidden" value="{{ $field['default_value'] }}">
        @else
          <div class="{{ $fieldClass }}">
            @if ($field['type'] === 'textarea')
              <label for="{{ $id }}" class="sr-only">{{ $field['label'] }}</label>
              <textarea id="{{ $id }}" name="{{ $name }}" rows="6" @if ($field['required']) required @endif placeholder="{{ strtoupper($placeholder) }}" class="min-h-48 resize-y {{ $inputClass }}"></textarea>
            @elseif ($field['type'] === 'select')
              <label for="{{ $id }}" class="sr-only">{{ $field['label'] }}</label>
              <select id="{{ $id }}" name="{{ $name }}" @if ($field['required']) required @endif class="{{ $inputClass }} appearance-none">
                <option value="">{{ strtoupper($placeholder) }}</option>
                @foreach ($field['options'] as $option)
                  <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                @endforeach
              </select>
            @elseif ($field['type'] === 'radio')
              <fieldset>
                <legend class="sr-only">{{ $field['label'] }}</legend>
                <div class="space-y-3">
                  @foreach ($field['options'] as $option)
                    <label class="flex cursor-pointer items-center gap-3 text-base uppercase tracking-[0.1em]">
                      <input name="{{ $name }}" type="radio" value="{{ $option['value'] }}" @if ($field['required']) required @endif class="h-4 w-4 accent-primary">
                      <span>{{ $option['label'] }}</span>
                    </label>
                  @endforeach
                </div>
              </fieldset>
            @elseif ($field['type'] === 'checkbox')
              <label class="flex cursor-pointer items-center gap-3 text-base uppercase tracking-[0.1em]">
                <input name="{{ $name }}" type="checkbox" value="1" @if ($field['required']) required @endif class="h-4 w-4 accent-primary">
                <span>{{ $field['label'] }}</span>
              </label>
            @elseif ($field['type'] === 'checkboxes')
              <fieldset>
                <legend class="sr-only">{{ $field['label'] }}</legend>
                <div class="space-y-3">
                  @foreach ($field['options'] as $option)
                    <label class="flex cursor-pointer items-center gap-3 text-base uppercase tracking-[0.1em]">
                      <input name="{{ $name }}[]" type="checkbox" value="{{ $option['value'] }}" class="h-4 w-4 accent-primary">
                      <span>{{ $option['label'] }}</span>
                    </label>
                  @endforeach
                </div>
              </fieldset>
            @else
              <label for="{{ $id }}" class="sr-only">{{ $field['label'] }}</label>
              <input id="{{ $id }}" name="{{ $name }}" type="{{ $field['type'] }}" @if ($field['required']) required @endif placeholder="{{ strtoupper($placeholder) }}" class="{{ $inputClass }}">
            @endif
          </div>
        @endif
      @endforeach
    </div>

    <button
      type="submit"
      class="inline-flex cursor-pointer rounded-lg border border-white px-4 py-2.5 text-base font-light uppercase tracking-[0.16em] transition hover:bg-white hover:text-black focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white md:px-6 md:py-3 md:text-lg md:tracking-[0.2em]"
    >
      {{ $submit_label }}
    </button>
  </form>
</div>
