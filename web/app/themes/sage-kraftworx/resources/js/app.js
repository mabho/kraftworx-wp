const initHeader = () => {
  const toggle = document.querySelector('[data-menu-toggle]');
  const menu = document.querySelector('[data-mobile-menu]');

  if (!toggle || !menu) {
    return;
  }

  const openIcon = toggle.querySelector('[data-menu-icon-open]');
  const closeIcon = toggle.querySelector('[data-menu-icon-close]');
  const desktop = window.matchMedia('(min-width: 48rem)');

  const setOpen = (open) => {
    toggle.setAttribute('aria-expanded', String(open));
    menu.classList.toggle('hidden', !open);
    openIcon?.classList.toggle('hidden', open);
    closeIcon?.classList.toggle('hidden', !open);
  };

  toggle.addEventListener('click', () => {
    setOpen(toggle.getAttribute('aria-expanded') !== 'true');
  });

  menu.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => setOpen(false));
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      setOpen(false);
    }
  });

  desktop.addEventListener('change', (event) => {
    if (event.matches) {
      setOpen(false);
    }
  });
};

const initScrollArrows = () => {
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  const headerBar = document.querySelector('[data-site-header-bar]');
  const adminBar = document.getElementById('wpadminbar');

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
      const offset = (headerBar?.offsetHeight ?? 0) + (adminBar?.offsetHeight ?? 0);

      next.style.scrollMarginTop = offset ? `${offset}px` : '';

      next.scrollIntoView({
        behavior: prefersReducedMotion.matches ? 'auto' : 'smooth',
        block: 'start',
      });
    });
  });
};

const initScrollTop = () => {
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

  document.querySelectorAll('[data-scroll-top]').forEach((button) => {
    button.addEventListener('click', () => {
      window.scrollTo({
        top: 0,
        behavior: prefersReducedMotion.matches ? 'auto' : 'smooth',
      });
    });
  });
};

const init = () => {
  initHeader();
  initScrollArrows();
  initScrollTop();
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', init);
} else {
  init();
}
