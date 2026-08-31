<?php

namespace App\SiteSettings;

use App\SiteSettings\Data\AdminStateSettings;
use App\SiteSettings\Data\BrandingSettings;
use App\SiteSettings\Data\ConsentCopySettings;
use App\SiteSettings\Data\ContactDetailsSettings;
use App\SiteSettings\Data\FeatureTogglesSettings;
use App\SiteSettings\Data\PublishingStateSettings;
use App\SiteSettings\Data\SeoDefaultsSettings;
use App\SiteSettings\Data\SiteIdentitySettings;
use App\SiteSettings\Data\SocialLinksSettings;
use App\SiteSettings\Data\StaticExportSettings;
use App\SiteSettings\Data\ThemeSettings;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final readonly class SiteSettings
{
    public function __construct(
        public SiteIdentitySettings $siteIdentity,
        public ContactDetailsSettings $contactDetails,
        public SocialLinksSettings $socialLinks,
        public SeoDefaultsSettings $seoDefaults,
        public ConsentCopySettings $consentCopy,
        public FeatureTogglesSettings $featureToggles,
        public ThemeSettings $themeSettings,
        public BrandingSettings $brandingSettings,
        public StaticExportSettings $staticExportSettings,
        public PublishingStateSettings $publishingState,
        public AdminStateSettings $adminState,
    ) {}

    public static function fromPayload(array $payload): self
    {
        $validated = self::validate($payload);

        return new self(
            siteIdentity: SiteIdentitySettings::fromArray($validated['site_identity']),
            contactDetails: ContactDetailsSettings::fromArray($validated['contact_details']),
            socialLinks: SocialLinksSettings::fromArray($validated['social_links']),
            seoDefaults: SeoDefaultsSettings::fromArray($validated['seo_defaults']),
            consentCopy: ConsentCopySettings::fromArray($validated['consent_copy']),
            featureToggles: FeatureTogglesSettings::fromArray($validated['feature_toggles']),
            themeSettings: ThemeSettings::fromArray($validated['theme_settings']),
            brandingSettings: BrandingSettings::fromArray($validated['branding_settings']),
            staticExportSettings: StaticExportSettings::fromArray($validated['static_export_settings']),
            publishingState: PublishingStateSettings::fromArray($validated['publishing_state']),
            adminState: AdminStateSettings::fromArray($validated['admin_state']),
        );
    }

    public function toPersistenceArray(): array
    {
        return [
            'site_identity' => $this->siteIdentity->toArray(),
            'contact_details' => $this->contactDetails->toArray(),
            'social_links' => $this->socialLinks->toArray(),
            'seo_defaults' => $this->seoDefaults->toArray(),
            'consent_copy' => $this->consentCopy->toArray(),
            'feature_toggles' => $this->featureToggles->toArray(),
            'theme_settings' => $this->themeSettings->toArray(),
            'branding_settings' => $this->brandingSettings->toArray(),
            'static_export_settings' => $this->staticExportSettings->toArray(),
            'publishing_state' => $this->publishingState->toArray(),
            'admin_state' => $this->adminState->toArray(),
        ];
    }

    protected static function validate(array $payload): array
    {
        $validator = Validator::make($payload, [
            'site_identity' => ['required', 'array:name,tagline,description'],
            'site_identity.name' => ['required', 'string', 'max:120'],
            'site_identity.tagline' => ['required', 'string', 'max:255'],
            'site_identity.description' => ['required', 'string', 'max:500'],

            'contact_details' => ['required', 'array:email,location,availability'],
            'contact_details.email' => ['required', 'email:rfc', 'max:255'],
            'contact_details.location' => ['required', 'string', 'max:120'],
            'contact_details.availability' => ['required', 'string', 'max:500'],

            'social_links' => ['required', 'array:github_url,linkedin_url'],
            'social_links.github_url' => ['nullable', 'url', 'max:255'],
            'social_links.linkedin_url' => ['nullable', 'url', 'max:255'],

            'seo_defaults' => ['required', 'array:title_suffix,default_description,default_robots'],
            'seo_defaults.title_suffix' => ['required', 'string', 'max:120'],
            'seo_defaults.default_description' => ['required', 'string', 'max:500'],
            'seo_defaults.default_robots' => ['required', 'string', 'max:40'],

            'consent_copy' => ['required', 'array:preferences_title,preferences_description,media_notice_title,media_notice_description'],
            'consent_copy.preferences_title' => ['required', 'string', 'max:120'],
            'consent_copy.preferences_description' => ['required', 'string', 'max:500'],
            'consent_copy.media_notice_title' => ['required', 'string', 'max:120'],
            'consent_copy.media_notice_description' => ['required', 'string', 'max:500'],

            'feature_toggles' => ['required', 'array:show_labs,show_writing,show_case_studies'],
            'feature_toggles.show_labs' => ['required', 'boolean'],
            'feature_toggles.show_writing' => ['required', 'boolean'],
            'feature_toggles.show_case_studies' => ['required', 'boolean'],

            'theme_settings' => ['required', 'array:default_theme,gradient_angle,surface_blur,line_thickness'],
            'theme_settings.default_theme' => ['required', 'string', 'in:morning,sunset'],
            'theme_settings.gradient_angle' => ['required', 'integer', 'between:0,360'],
            'theme_settings.surface_blur' => ['required', 'integer', 'between:0,40'],
            'theme_settings.line_thickness' => ['required', 'integer', 'between:1,8'],

            'branding_settings' => ['required', 'array:asset_mode,uploaded_asset_path,fallback_variant,fallback_label,fallback_subtitle,active_alt'],
            'branding_settings.asset_mode' => ['required', 'string', 'in:uploaded,fallback'],
            'branding_settings.uploaded_asset_path' => ['nullable', 'string', 'max:255'],
            'branding_settings.fallback_variant' => ['required', 'string', 'in:avatar,monogram_circle,monogram_square,wordmark'],
            'branding_settings.fallback_label' => ['required', 'string', 'max:64'],
            'branding_settings.fallback_subtitle' => ['required', 'string', 'max:120'],
            'branding_settings.active_alt' => ['required', 'string', 'max:160'],

            'static_export_settings' => ['required', 'array:static_mode_enabled,github_pages_enabled,preprod_mode_enabled,export_base_path,export_output_path'],
            'static_export_settings.static_mode_enabled' => ['required', 'boolean'],
            'static_export_settings.github_pages_enabled' => ['required', 'boolean'],
            'static_export_settings.preprod_mode_enabled' => ['required', 'boolean'],
            'static_export_settings.export_base_path' => ['required', 'string', 'max:120'],
            'static_export_settings.export_output_path' => ['required', 'string', 'max:255'],

            'publishing_state' => ['required', 'array:rebuild_required,last_rebuilt_at,last_change_summary'],
            'publishing_state.rebuild_required' => ['required', 'boolean'],
            'publishing_state.last_rebuilt_at' => ['nullable', 'date'],
            'publishing_state.last_change_summary' => ['nullable', 'string', 'max:255'],

            'admin_state' => ['required', 'array:onboarding_completed_at,primary_operator_email'],
            'admin_state.onboarding_completed_at' => ['nullable', 'date'],
            'admin_state.primary_operator_email' => ['nullable', 'email:rfc', 'max:255'],
        ]);

        try {
            return $validator->validate();
        } catch (ValidationException $exception) {
            throw InvalidSiteSettings::fromValidationException($exception);
        }
    }
}
