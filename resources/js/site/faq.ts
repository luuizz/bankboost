/** Filtra as perguntas da central de ajuda enquanto a pessoa digita. */
export function initFaqSearch(): void {
    const input = document.querySelector<HTMLInputElement>('[data-faq-search]');

    if (!input) return;

    const items = Array.from(document.querySelectorAll<HTMLDetailsElement>('[data-faq-item]'));
    const groups = Array.from(document.querySelectorAll<HTMLElement>('[data-faq-group]'));
    const empty = document.querySelector<HTMLElement>('[data-faq-empty]');
    const normalize = (text: string) => text.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();

    input.addEventListener('input', () => {
        const term = normalize(input.value.trim());

        items.forEach((item) => {
            const match = term === '' || normalize(item.textContent ?? '').includes(term);
            item.hidden = !match;
            item.open = term !== '' && match;
        });

        groups.forEach((group) => {
            group.hidden = !group.querySelector('[data-faq-item]:not([hidden])');
        });

        if (empty) empty.hidden = groups.some((group) => !group.hidden);
    });
}
