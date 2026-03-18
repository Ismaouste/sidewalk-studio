<?php

namespace App\SiteSettings\Data;

final readonly class BrandingSettings
{
    public function __construct(
        public string $assetMode,
        public ?string $uploadedAssetPath,
        public string $fallbackVariant,
        public string $fallbackLabel,
        public string $fallbackSubtitle,
        public string $activeAlt,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            assetMode: (string) ($payload['asset_mode'] ?? 'uploaded'),
            uploadedAssetPath: filled($payload['uploaded_asset_path'] ?? null)
                ? (string) $payload['uploaded_asset_path']
                : null,
            fallbackVariant: (string) ($payload['fallback_variant'] ?? 'monogram_circle'),
            fallbackLabel: (string) ($payload['fallback_label'] ?? 'IR'),
            fallbackSubtitle: (string) ($payload['fallback_subtitle'] ?? 'Sidewalk Studio'),
            activeAlt: (string) ($payload['active_alt'] ?? 'Sidewalk Studio'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'asset_mode' => $this->assetMode,
            'uploaded_asset_path' => $this->uploadedAssetPath,
            'fallback_variant' => $this->fallbackVariant,
            'fallback_label' => $this->fallbackLabel,
            'fallback_subtitle' => $this->fallbackSubtitle,
            'active_alt' => $this->activeAlt,
        ];
    }
}
