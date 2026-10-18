const initScrollArrows = () => {
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

  document.querySelectorAll('[data-scroll-next]').forEach((button) => {
    const section = button.closest('section');
    let next = section?.nextElementSibling;

    while (next && (next.hidden || (!next.textContent.trim() && next.offsetHeight === 0))) {
      next = next.nextElementSibling;
    }

    if (!next) {
      button.hidden = true;
      return;
    }

    button.addEventListener('click', () => {
      const adminBar = document.getElementById('wpadminbar');

      next.style.scrollMarginTop = adminBar ? `${adminBar.offsetHeight}px` : '';

      next.scrollIntoView({
        behavior: prefersReducedMotion.matches ? 'auto' : 'smooth',
        block: 'start',
      });
    });
  });
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initScrollArrows);
} else {
  initScrollArrows();
}
