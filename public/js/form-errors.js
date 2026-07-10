function scrollToFirstFormError(root) {
    const scope = root instanceof Element ? root : document;
    const error = scope.querySelector('.ek-error');

    if (!error) {
        return;
    }

    let field = error.previousElementSibling;

    if (!field?.matches('input, select, textarea')) {
        const group = error.closest('div');
        field = group?.querySelector('input:not([type=file]):not(.sr-only), select, textarea') ?? error;
    }

    field.scrollIntoView({ behavior: 'smooth', block: 'center' });

    if (field.matches?.('input, select, textarea')) {
        field.classList.add('ek-input--error');
        field.focus({ preventScroll: true });

        field.addEventListener(
            'input',
            () => field.classList.remove('ek-input--error'),
            { once: true },
        );
    }
}

function handleScrollToFirstError(event) {
  const root = event?.target instanceof Element ? event.target : document;
  requestAnimationFrame(() => scrollToFirstFormError(root));
}

function registerScrollToFirstErrorListener() {
    window.addEventListener('scroll-to-first-error', handleScrollToFirstError);

    if (typeof Livewire !== 'undefined' && typeof Livewire.on === 'function') {
        Livewire.on('scroll-to-first-error', () => handleScrollToFirstError());
    }
}

if (window.Livewire) {
    registerScrollToFirstErrorListener();
} else {
    document.addEventListener('livewire:init', registerScrollToFirstErrorListener);
}
