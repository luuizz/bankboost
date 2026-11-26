/** Paginação no formato do LengthAwarePaginator do Laravel (paginate()->through()). */
export interface LaravelPage<T> {
    data: T[];
    current_page: number;
    last_page: number;
    total: number;
    from: number | null;
    to: number | null;
    prev_page_url: string | null;
    next_page_url: string | null;
}

export interface AdminAccount {
    id: number;
    agency: string;
    number: string;
    status: 'active' | 'blocked';
    balance: number;
    balance_formatted: string;
    created_at: string;
    holder: { name: string; email: string; cpf: string };
}

export interface AdminPostRow {
    id: number;
    title: string;
    slug: string;
    category: string;
    featured: boolean;
    status: 'draft' | 'scheduled' | 'published';
    published_at: string | null;
    url: string;
}

export interface AdminPost {
    id: number;
    title: string;
    slug: string;
    excerpt: string;
    body: string;
    author_name: string;
    featured: boolean;
    category: string;
    published_at: string | null;
    cover_url: string | null;
    url: string;
    status: AdminPostRow['status'];
}

export interface Faq {
    id: number;
    category: string;
    question: string;
    answer: string;
    position: number;
    active: boolean;
}

export interface JobOpening {
    id: number;
    title: string;
    team: string;
    location: string;
    seniority: string;
    contract: string;
    description: string;
    active: boolean;
}

export interface ContactMessage {
    id: number;
    name: string;
    email: string;
    subject: string;
    message: string;
    read: boolean;
    created_at: string;
}
