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
