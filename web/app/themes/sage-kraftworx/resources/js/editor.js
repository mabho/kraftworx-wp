import domReady from '@wordpress/dom-ready';

domReady(() => {
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
