export type NavItem = {
    label: string;
    href: string;
};

export type BreadcrumbItem = {
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

export type ThemeSettingsPayload = {
    morning_accent: string;
    morning_glow: string;
    morning_glow_soft: string;
    sunset_accent: string;
    sunset_glow: string;
    sunset_glow_soft: string;
    header_gradient_angle: number;
    ambient_blur_px: number;
    grid_line_px: number;
};

export type SiteSettingsPayload = {
    site_identity: SiteIdentitySettings;
    contact_details: SiteContact;
    social_links: SocialLinksSettings;
    seo_defaults: SeoDefaultsSettings;
    consent_copy: ConsentCopySettings;
    feature_toggles: FeatureTogglesSettings;
    theme_settings: ThemeSettingsPayload;
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
    social: {
        github_url: string | null;
        linkedin_url: string | null;
    };
    themeSettings: ThemeSettingsPayload;
    shell: {
        headerTagline: string;
        localeSwitcherLabel: string;
        navAriaLabel: string;
        navMenuLabel: string;
        navFallbackLabel: string;
        navCurrentLabel: string;
        navOpenLabel: string;
        footerNote: string;
        privacyControlsLabel: string;
    };
    languageSwitcher: {
        visible: boolean;
        current: string;
        preferred: string;
        options: Array<{
            code: string;
            label: string;
            available: boolean;
            href: string | null;
        }>;
    };
    runtime: {
        staticPreview: boolean;
        staticBasePath: string | null;
    };
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
    breadcrumbs: BreadcrumbItem[];
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
    category: string;
    publication_type: 'note' | 'reference';
    accent_tone: string;
    featured_image: string;
    featured_image_alt: string;
    featured_video: string;
    image: {
        url: string;
        alt: string;
        kind: 'image' | 'placeholder';
    };
    image_url: string;
    image_alt: string;
    reading_time: number;
    body_html: string;
    excerpt: string;
    url: string;
};

export type PublicationWidget = {
    eyebrow: string;
    title: string;
    description: string;
    ctaLabel: string;
    ctaHref: string;
    items: ContentItem[];
};

export type LabItem = {
    slug: string;
    title: string;
    status: string;
    summary: string;
    stack: string[];
};
