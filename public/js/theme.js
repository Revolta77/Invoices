function getStoredTheme() {
    return localStorage.getItem('theme');
}

function getSystemTheme() {
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}

function applyTheme(theme) {
    document.documentElement.classList.toggle('dark', theme === 'dark');
}

function initTheme() {
    const theme = getStoredTheme() ?? getSystemTheme();
    applyTheme(theme);
}

function getRevealRadius(x, y) {
    return Math.hypot(
        Math.max(x, window.innerWidth - x),
        Math.max(y, window.innerHeight - y),
    );
}

function toggleTheme(button) {
    const next = document.documentElement.classList.contains('dark') ? 'light' : 'dark';

    const apply = () => {
        localStorage.setItem('theme', next);
        applyTheme(next);
    };

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const canAnimate = button
        && typeof document.startViewTransition === 'function'
        && !prefersReducedMotion;

    if (!canAnimate) {
        apply();
        return;
    }

    const { top, left, width, height } = button.getBoundingClientRect();
    const x = left + width / 2;
    const y = top + height / 2;
    const radius = getRevealRadius(x, y);

    document.documentElement.classList.add('theme-transition-active');

    const transition = document.startViewTransition(apply);

    transition.ready
        .then(() => {
            document.documentElement.animate(
                {
                    clipPath: [
                        `circle(0px at ${x}px ${y}px)`,
                        `circle(${radius}px at ${x}px ${y}px)`,
                    ],
                },
                {
                    duration: 520,
                    easing: 'cubic-bezier(0.4, 0, 0.2, 1)',
                    pseudoElement: '::view-transition-new(root)',
                },
            );
        })
        .catch(() => {})
        .finally(() => {
            window.setTimeout(() => {
                document.documentElement.classList.remove('theme-transition-active');
            }, 520);
        });
}

initTheme();

document.addEventListener('livewire:navigated', () => {
    initTheme();
});

document.addEventListener('click', (event) => {
    const toggle = event.target.closest('[data-theme-toggle]');
    if (toggle) {
        event.preventDefault();
        toggleTheme(toggle);
    }
});

window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (event) => {
    if (!getStoredTheme()) {
        applyTheme(event.matches ? 'dark' : 'light');
    }
});
