/**
 * JSON as it round-trips between the content backend and the admin editors.
 *
 * Typed structurally rather than as `unknown` so Inertia's form helper can
 * prove the payload is serialisable: its FormDataType mapping resolves
 * `unknown` to `never` and rejects the form outright.
 */
export type JsonValue =
    string | number | boolean | null | JsonValue[] | JsonObject;

// An interface (not an alias) so TypeScript defers resolution: Inertia's
// FormDataKeys walks the shape to build dotted key paths and an alias here
// trips its recursion limit.
export interface JsonObject {
    [key: string]: JsonValue;
}

export type AdminAuditLogEntry = {
    id: number;
    actor: {
        type: string;
        name: string | null;
        email: string | null;
    };
    action: string;
    subject: string;
    summary: {
        changed_groups?: string[];
        changed_fields?: string[];
        field_count?: number;
        created_user_email?: string;
        created_user_name?: string;
    };
    created_at: string | null;
};

export type ContactSubmissionEntry = {
    id: number;
    locale: string;
    name: string;
    email: string;
    company: string | null;
    summary: string;
    status: string;
    created_at: string | null;
    read_at: string | null;
};

export type PublicationAdminEntry = {
    section: 'writing' | 'case-studies';
    locale: 'en' | 'fr';
    title: string;
    slug: string;
    summary: string;
    status: 'draft' | 'published';
    published_at: string;
    updated_at: string;
    tags: string[];
    seo_title: string;
    seo_description: string;
    robots: string;
    canonical_url: string;
    client: string;
    role: string;
    stack: string[];
    outcomes: string[];
    category: string;
    publication_type: 'note' | 'journal' | 'case_study';
    accent_tone: string;
    featured_image: string;
    featured_image_alt: string;
    open_graph_image: string;
    featured_video: string;
    body_markdown: string;
    source_path: string | null;
    source_driver: 'file' | 'database' | 'hybrid';
    metadata: {
        client?: string;
        role?: string;
        stack?: string[];
        outcomes?: string[];
    };
    original_publication_type?: 'note' | 'journal' | 'case_study';
    original_locale?: 'en' | 'fr';
    original_slug?: string;
};

export type PublicationTypeSetting = {
    type: 'note' | 'journal' | 'case_study';
    cta_label: string;
    cta_target: string;
    accent_color: string;
};

export type AdminPageEntry = {
    page_key: string;
    locale: 'en' | 'fr';
    title: string;
    description: string;
    seo_title: string;
    seo_description: string;
    robots: string;
    canonical_url: string;
    open_graph_image: string;
    payload: JsonObject;
    source_path: string | null;
    source_driver: 'file' | 'database' | 'hybrid';
};

export type LoaderQuoteAdminEntry = {
    id?: number;
    text: string;
    type: 'message' | 'quote';
    author: string | null;
    locale: 'en' | 'fr';
    is_active: boolean;
    theme_target: 'morning' | 'sunset' | null;
    weight: number;
};

export type ManagedLanguageFile = {
    key: string;
    label: string;
    locale: 'en' | 'fr';
    path: string;
    payload: JsonObject;
};
