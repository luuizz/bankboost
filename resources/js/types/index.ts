export type TransactionType = 'deposit' | 'withdrawal' | 'transfer_in' | 'transfer_out' | 'pix_in' | 'pix_out';

export interface Account {
    agency: string;
    number: string;
    status: 'active' | 'blocked';
    balance: number;
    balance_formatted: string;
    holder?: { name: string; email: string };
    created_at: string;
}

export interface Transaction {
    id: string;
    type: TransactionType;
    type_label: string;
    direction: 'credit' | 'debit';
    amount: number;
    amount_formatted: string;
    balance_after: number;
    description: string | null;
    counterparty_name: string | null;
    transfer_id?: string | null;
    created_at: string;
}

export interface PixKey {
    id: number;
    type: 'cpf' | 'email' | 'phone' | 'random';
    type_label: string;
    key: string;
    created_at: string;
}

export interface PixRecipient {
    key: string;
    type: PixKey['type'];
    name: string;
    document: string;
    bank: string;
    agency: string;
    number: string;
}

export interface TransferReceipt {
    id: string;
    method: 'internal' | 'pix';
    method_label: string;
    amount: number;
    amount_formatted: string;
    description: string | null;
    pix_key: string | null;
    source: { name: string; agency: string; number: string };
    destination: { name: string; agency: string; number: string };
    created_at: string;
}

export interface Option<T extends string = string> {
    value: T;
    label: string;
}

export interface Paginated<T> {
    data: T[];
    links: { first: string | null; last: string | null; prev: string | null; next: string | null };
    meta: {
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        from: number | null;
        to: number | null;
    };
}

export interface SharedProps {
    appName: string;
    auth: { user: { name: string; email: string } | null };
    flash: { success: string | null };
    errors: Record<string, string>;
    [key: string]: unknown;
}
