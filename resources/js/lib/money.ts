const brl = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });

/** Formata centavos como moeda brasileira. */
export function formatCents(cents: number): string {
    return brl.format(cents / 100);
}

/** Converte o texto digitado ("1.234,56") em centavos. */
export function parseToCents(value: string): number {
    const digits = value.replace(/\D/g, '');

    return digits === '' ? 0 : Number.parseInt(digits, 10);
}

export function formatDateTime(iso: string): string {
    return new Intl.DateTimeFormat('pt-BR', { dateStyle: 'short', timeStyle: 'short' }).format(new Date(iso));
}

export function newIdempotencyKey(): string {
    return crypto.randomUUID();
}
