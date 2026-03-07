export type NavItem = {
    label: string;
    href: string;
};

export type SiteAuthor = {
    name: string;
    job_title: string;
    email: string;
    same_as: string[];
};

export type SiteContact = {
    email: string;
    location: string;
    availability: string;
};

export type SiteIdentitySettings = {
    name: string;
    tagline: string;
    description: string;
};

export type SocialLinksSettings = {
    github_url: string | null;
    linkedin_url: string | null;
};

export type SeoDefaultsSettings = {
    title_suffix: string;
    default_description: string;
    default_robots: string;
};

export type ConsentCopySettings = {
    preferences_title: string;
    preferences_description: string;
    media_notice_title: string;
    media_notice_description: string;
};

export type FeatureTogglesSettings = {
    show_labs: boolean;
    show_writing: boolean;
    show_case_studies: boolean;
};

export type SiteSettingsPayload = {
    site_identity: SiteIdentitySettings;
    contact_details: SiteContact;
    social_links: SocialLinksSettings;
    seo_defaults: SeoDefaultsSettings;
    consent_copy: ConsentCopySettings;
    feature_toggles: FeatureTogglesSettings;
};

export type FlashProps = {
    status?: string | null;
};

export type SiteProps = {
    name: string;
    tagline: string;
    description: string;
    locale: string;
    url: string;
    navigation: NavItem[];
    author: SiteAuthor;
    contact: SiteContact;
};

export type ConsentCategory = {
    key: 'necessary' | 'analytics' | 'media';
    label: string;
    description: string;
    readonly: boolean;
    enabled: boolean;
};

export type ConsentConfig = {
    mode: string;
    driver: 'none' | 'matomo' | 'posthog';
    cookieName: string;
    categories: ConsentCategory[];
    services: Record<string, unknown>;
};

export type SeoPayload = {
    title: string;
    description: string;
    canonical: string;
    robots: string;
    openGraph: Record<string, string>;
    twitter: Record<string, string>;
    jsonLd: Record<string, unknown>[];
};

export type ContentItem = {
    section: 'writing' | 'case-studies';
    locale: string;
    title: string;
    slug: string;
    summary: string;
    status: 'draft' | 'published';
    published_at: string;
    updated_at: string;
    tags: string[];
    seo_title: string;
    seo_description: string;
    client: string;
    role: string;
    stack: string[];
    outcomes: string[];
    reading_time: number;
    body_html: string;
    excerpt: string;
    url: string;
};

export type LabItem = {
    slug: string;
    title: string;
    status: string;
    summary: string;
    stack: string[];
};
