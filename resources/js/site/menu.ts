export function initMenu(): void {
    const toggle = document.querySelector<HTMLButtonElement>('[data-menu-toggle]');
    const menu = document.querySelector<HTMLElement>('[data-menu]');

    if (!toggle || !menu) return;

    toggle.addEventListener('click', () => {
        const open = menu.classList.toggle('hidden') === false;

        toggle.setAttribute('aria-expanded', String(open));
        toggle.setAttribute('aria-label', open ? 'Fechar menu' : 'Abrir menu');
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !menu.classList.contains('hidden')) {
            menu.classList.add('hidden');
            toggle.setAttribute('aria-expanded', 'false');
            toggle.focus();
        }
    });
}
