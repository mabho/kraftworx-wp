<a
  href="{{ $url ?? 'https://www.linkedin.com/company/kraftworx' }}"
  target="_blank"
  rel="noopener noreferrer"
  class="{{ $class ?? 'inline-flex text-white/80 transition hover:text-white' }}"
  aria-label="LinkedIn"
>
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="{{ $iconClass ?? 'h-5 w-5' }}" aria-hidden="true">
    <path d="M4.98 3.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5zM3 9h4v12H3V9zm6 0h3.8v1.7h.05c.53-1 1.83-2.05 3.77-2.05 4.03 0 4.78 2.65 4.78 6.1V21h-4v-5.4c0-1.29-.02-2.95-1.8-2.95-1.8 0-2.08 1.4-2.08 2.85V21H9V9z"/>
  </svg>

  @if (!empty($label))
    <span>{{ $label }}</span>
  @endif
</a>
