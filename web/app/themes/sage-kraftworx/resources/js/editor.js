import domReady from '@wordpress/dom-ready';

const styleFormats = [
  { title: 'Lead', block: 'p', classes: 'wysiwyg-lead' },
  { title: 'Accent', inline: 'span', classes: 'wysiwyg-accent' },
  { title: 'Eyebrow', inline: 'span', classes: 'wysiwyg-eyebrow' },
  { title: 'Muted', inline: 'span', classes: 'wysiwyg-muted' },
];

let acfFilterRegistered = false;

const registerAcfWysiwygFilter = () => {
  if (acfFilterRegistered || !window.acf || typeof window.acf.addFilter !== 'function') {
    return;
  }

  window.acf.addFilter('wysiwyg_tinymce_settings', (settings) => {
    settings.style_formats = styleFormats;
    settings.toolbar1 = 'bold,italic,bullist,numlist,link,unlink,styleselect,removeformat';
    settings.toolbar2 = 'undo,redo';

    return settings;
  });

  acfFilterRegistered = true;
};

registerAcfWysiwygFilter();

domReady(() => {
  registerAcfWysiwygFilter();

  if (!acfFilterRegistered) {
    const timer = window.setInterval(() => {
      registerAcfWysiwygFilter();

      if (acfFilterRegistered) {
        window.clearInterval(timer);
      }
    }, 100);

    window.setTimeout(() => window.clearInterval(timer), 5000);
  }

  document.addEventListener('keydown', (event) => {
    if (event.key !== ' ' && event.code !== 'Space') {
      return;
    }

    if (!(event.target instanceof Element)) {
      return;
    }

    const title = event.target.closest('.acf-accordion-title');

    if (!title) {
      return;
    }

    event.preventDefault();
    title.click();
  });
});
