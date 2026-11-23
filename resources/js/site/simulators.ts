import { formatBRL } from './money';

/**
 * Simuladores de investimento e empréstimo. Os parâmetros (taxas) vêm dos
 * atributos data-* do HTML, então o texto da página e o cálculo não se desencontram.
 */
export function initSimulators(): void {
    document.querySelectorAll<HTMLFormElement>('[data-simulator="investment"]').forEach(investment);
    document.querySelectorAll<HTMLFormElement>('[data-simulator="loan"]').forEach(loan);
}

function bindRange(form: HTMLFormElement, name: string, format: (value: number) => string, onChange: () => void): () => number {
    const input = form.querySelector<HTMLInputElement>(`[name="${name}"]`)!;
    const output = form.querySelector<HTMLElement>(`[data-output="${name}"]`);

    const read = () => Number(input.value);
    const update = () => {
        if (output) output.textContent = format(read());
        const progress = ((read() - Number(input.min)) / (Number(input.max) - Number(input.min))) * 100;
        input.style.setProperty('--progress', `${progress}%`);
        onChange();
    };

    input.addEventListener('input', update);
    queueMicrotask(update);

    return read;
}

function set(form: HTMLFormElement, key: string, value: string): void {
    form.querySelectorAll<HTMLElement>(`[data-result="${key}"]`).forEach((element) => (element.textContent = value));
}

function investment(form: HTMLFormElement): void {
    // Taxas anuais em %, ex.: data-rate-boost="13.2"
    const boostRate = Number(form.dataset.rateBoost) / 100;
    const savingsRate = Number(form.dataset.rateSavings) / 100;

    const calculate = () => {
        const initial = amount();
        const monthly = deposit();
        const months = period();

        const grow = (yearRate: number) => {
            const rate = (1 + yearRate) ** (1 / 12) - 1;
            let total = initial;

            for (let i = 0; i < months; i++) total = total * (1 + rate) + monthly;

            return total;
        };

        const invested = initial + monthly * months;
        const boost = grow(boostRate);
        const savings = grow(savingsRate);

        set(form, 'invested', formatBRL(invested));
        set(form, 'boost', formatBRL(boost));
        set(form, 'savings', formatBRL(savings));
        set(form, 'difference', formatBRL(boost - savings));

        const bar = form.querySelector<HTMLElement>('[data-result-bar="savings"]');
        if (bar) bar.style.width = `${Math.max(8, (savings / boost) * 100)}%`;
    };

    const amount = bindRange(form, 'initial', formatBRL, () => calculate());
    const deposit = bindRange(form, 'monthly', formatBRL, () => calculate());
    const period = bindRange(form, 'months', (m) => (m % 12 === 0 ? `${m / 12} ${m === 12 ? 'ano' : 'anos'}` : `${m} meses`), () => calculate());
}

function loan(form: HTMLFormElement): void {
    const monthlyRate = Number(form.dataset.rate) / 100;

    const calculate = () => {
        const principal = value();
        const n = installments();
        // Tabela Price
        const payment = (principal * monthlyRate) / (1 - (1 + monthlyRate) ** -n);

        set(form, 'payment', formatBRL(payment));
        set(form, 'total', formatBRL(payment * n));
        set(form, 'interest', formatBRL(payment * n - principal));
    };

    const value = bindRange(form, 'value', formatBRL, () => calculate());
    const installments = bindRange(form, 'installments', (n) => `${n}x`, () => calculate());
}
