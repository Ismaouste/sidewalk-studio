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

export type ThemeSettings = {
    default_theme: 'morning' | 'sunset';
    gradient_angle: number;
    surface_blur: number;
    line_thickness: number;
};

export type BrandingSettings = {
    asset_mode: 'uploaded' | 'fallback';
    uploaded_asset_path: string | null;
    fallback_variant:
        | 'avatar'
        | 'monogram_circle'
        | 'monogram_square'
        | 'wordmark';
    fallback_label: string;
    fallback_subtitle: string;
    active_alt: string;
};

export type LoaderQuote = {
    id?: number;
    text: string;
    type: 'message' | 'quote';
    author: string | null;
    locale: 'en' | 'fr';
    is_active: boolean;
    theme_target: 'morning' | 'sunset' | null;
    weight: number;
};

export type StaticExportSettings = {
    static_mode_enabled: boolean;
    github_pages_enabled: boolean;
    preprod_mode_enabled: boolean;
    export_base_path: string;
    export_output_path: string;
};

export type PublishingState = {
    rebuild_required: boolean;
    last_rebuilt_at: string | null;
    last_change_summary: string | null;
};

export type AdminState = {
    onboarding_completed_at: string | null;
    primary_operator_email: string | null;
};

export type SiteSettingsPayload = {
    site_identity: SiteIdentitySettings;
    contact_details: SiteContact;
    social_links: SocialLinksSettings;
    seo_defaults: SeoDefaultsSettings;
    consent_copy: ConsentCopySettings;
    feature_toggles: FeatureTogglesSettings;
    theme_settings: ThemeSettings;
    static_export_settings: StaticExportSettings;
    publishing_state: PublishingState;
    admin_state: AdminState;
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
    branding: BrandingSettings;
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
        preprodModeEnabled?: boolean;
        themeDefaults?: ThemeSettings;
        loaderQuotes?: LoaderQuote[];
    };
    colophonQuote: {
        text: string;
        author: string | null;
    } | null;
};

export type ConsentCategory = {
    key: 'necessary' | 'analytics' | 'media';
    label: string;
    description: string;
    readonly: boolean;
    enabled: boolean;
};

export type AnalyticsDriver = 'none' | 'umami' | 'vercel';

export type AnalyticsServices = {
    driver: AnalyticsDriver;
    umami?: {
        website_id: string | null;
        script_url: string | null;
    };
    vercel?: {
        enabled: boolean;
    };
};

export type ConsentConfig = {
    mode: string;
    driver: AnalyticsDriver;
    cookieName: string;
    categories: ConsentCategory[];
    services: {
        analytics?: AnalyticsServices;
        [key: string]: unknown;
    };
};

export type SeoAlternate = {
    hreflang: string;
    href: string;
};

export type SeoPayload = {
    title: string;
    description: string;
    canonical: string;
    robots: string;
    alternates: SeoAlternate[];
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
    publication_type: 'note' | 'journal' | 'case_study';
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
    accentColor?: string | null;
    items: ContentItem[];
};

export type LabItem = {
    slug: string;
    title: string;
    status: string;
    summary: string;
    stack: string[];
};
