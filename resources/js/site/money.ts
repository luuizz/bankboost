const brl = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });

export const formatBRL = (value: number): string => brl.format(value);

/** Lê "1.234,56" ou "1234.56" como número. */
export function parseBRL(value: string): number {
    const normalized = value.replace(/[^\d,.-]/g, '').replace(/\.(?=\d{3}(\D|$))/g, '').replace(',', '.');
    const number = Number.parseFloat(normalized);

    return Number.isFinite(number) ? number : 0;
}
