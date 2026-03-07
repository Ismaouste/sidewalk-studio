<?php

namespace App\SiteSettings;

use App\SiteSettings\Data\ConsentCopySettings;
use App\SiteSettings\Data\ContactDetailsSettings;
use App\SiteSettings\Data\FeatureTogglesSettings;
use App\SiteSettings\Data\SeoDefaultsSettings;
use App\SiteSettings\Data\SiteIdentitySettings;
use App\SiteSettings\Data\SocialLinksSettings;
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
        ]);

        try {
            return $validator->validate();
        } catch (ValidationException $exception) {
            throw InvalidSiteSettings::fromValidationException($exception);
        }
    }
}
