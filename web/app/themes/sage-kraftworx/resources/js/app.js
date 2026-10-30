import intlTelInput from 'intl-tel-input';
import 'intl-tel-input/styles';

const initHeader = () => {
  const toggle = document.querySelector('[data-menu-toggle]');
  const menu = document.querySelector('[data-mobile-menu]');

  if (!toggle || !menu) {
    return;
  }

  const desktop = window.matchMedia('(min-width: 48rem)');

  const setOpen = (open) => {
    toggle.setAttribute('aria-expanded', String(open));
    toggle.setAttribute('aria-label', open ? 'Close navigation' : 'Open navigation');
    menu.classList.toggle('is-open', open);
    menu.setAttribute('aria-hidden', String(!open));
    menu.toggleAttribute('inert', !open);
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

const initHeroParallax = () => {
  const sections = [...document.querySelectorAll('[data-hero-parallax]')];

  if (!sections.length) {
    return;
  }

  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  const backgroundRate = 0.15;
  const parallaxScale = 1.2;
  const zoomAmount = 0.15;
  let frame = 0;

  const reset = () => {
    sections.forEach((section) => {
      const background = section.querySelector('[data-hero-parallax-image]');
      const layerImage = section.querySelector('.hero-parallax-layer__image');

      if (background) {
        background.style.transform = '';
      }

      if (layerImage) {
        layerImage.style.transform = '';
      }
    });
  };

  const update = () => {
    frame = 0;

    if (prefersReducedMotion.matches) {
      reset();
      return;
    }

    const viewportHeight = window.innerHeight;

    sections.forEach((section) => {
      const background = section.querySelector('[data-hero-parallax-image]');
      const layer = section.querySelector('[data-hero-parallax-layer]');
      const layerImage = layer?.querySelector('.hero-parallax-layer__image') ?? null;

      if (!background && !layerImage) {
        return;
      }

      const rect = section.getBoundingClientRect();

      if (rect.bottom < 0 || rect.top > viewportHeight) {
        return;
      }

      const offset = -rect.top * backgroundRate;

      if (background) {
        background.style.transform = `translate3d(0, ${offset.toFixed(2)}px, 0) scale(${parallaxScale})`;
      }

      if (!layerImage) {
        return;
      }

      const effect = layer?.dataset.parallaxEffect ?? 'none';
      let transform = '';

      switch (effect) {
        case 'same-speed':
          transform = `translate3d(0, ${offset.toFixed(2)}px, 0) scale(${parallaxScale})`;
          break;
        case 'same-speed-zoom': {
          const progress = Math.min(1, Math.max(0, -rect.top / Math.max(rect.height, 1)));
          const scale = 1 + (progress * zoomAmount);

          transform = `translate3d(0, ${offset.toFixed(2)}px, 0) scale(${scale.toFixed(3)})`;
          break;
        }
        case 'slower':
          transform = `translate3d(0, ${(offset * 0.5).toFixed(2)}px, 0) scale(${parallaxScale})`;
          break;
        case 'faster':
          transform = `translate3d(0, ${(offset * 1.5).toFixed(2)}px, 0) scale(${parallaxScale})`;
          break;
        case 'left-to-right':
          transform = `translate3d(${offset.toFixed(2)}px, ${offset.toFixed(2)}px, 0) scale(${parallaxScale})`;
          break;
        case 'right-to-left':
          transform = `translate3d(${(-offset).toFixed(2)}px, ${offset.toFixed(2)}px, 0) scale(${parallaxScale})`;
          break;
        default:
          transform = '';
      }

      layerImage.style.transform = transform;
    });
  };

  const requestUpdate = () => {
    if (!frame) {
      frame = window.requestAnimationFrame(update);
    }
  };

  window.addEventListener('scroll', requestUpdate, { passive: true });
  window.addEventListener('resize', requestUpdate);
  window.addEventListener('load', requestUpdate);
  prefersReducedMotion.addEventListener('change', requestUpdate);

  update();
};

const initPhoneInputs = () => {
  const inputs = [...document.querySelectorAll('[data-intl-phone]')];

  if (!inputs.length) {
    return;
  }

  const invalidClasses = ['ring-2', 'ring-red-500'];

  const setValidityState = (input, iti) => {
    if (!input.value.trim()) {
      input.setAttribute('aria-invalid', 'false');
      input.classList.remove(...invalidClasses);
      return;
    }

    let isValid = false;

    try {
      isValid = iti.isValidNumber() === true;
    } catch {
      isValid = false;
    }

    input.setAttribute('aria-invalid', String(!isValid));
    input.classList.toggle('ring-2', !isValid);
    input.classList.toggle('ring-red-500', !isValid);
  };

  inputs.forEach((input) => {
    const iti = intlTelInput(input, {
      initialCountry: 'us',
      showFlags: true,
      separateDialCode: true,
      countrySearch: true,
      countrySelectorMode: 'AUTO',
      formatAsYouType: true,
      placeholderNumberPolicy: 'AGGRESSIVE',
      strictMode: false,
      dropdownParent: document.body,
      fullscreenParent: document.body,
      loadUtils: () => import('intl-tel-input/utils'),
    });

    const form = input.closest('form');

    if (form) {
      form.addEventListener('submit', () => {
        try {
          const number = iti.getNumber();

          if (number) {
            input.value = number;
          }
        } catch {
          // Leave the user's value intact if the number cannot be normalized.
        }
      });
    }

    input.addEventListener('blur', () => setValidityState(input, iti));
    input.addEventListener('countrychange', () => setValidityState(input, iti));
    iti.promise.then(() => setValidityState(input, iti)).catch(() => {});
  });
};

const init = () => {
  initHeader();
  initScrollArrows();
  initScrollTop();
  initHeroParallax();
  initPhoneInputs();
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', init);
} else {
  init();
}
