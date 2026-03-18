<?php

namespace App\SiteSettings\Data;

final readonly class ConsentCopySettings
{
    public function __construct(
        public string $preferencesTitle,
        public string $preferencesDescription,
        public string $mediaNoticeTitle,
        public string $mediaNoticeDescription,
    ) {}

    public static function fromArray(array $attributes): self
    {
        return new self(
            preferencesTitle: $attributes['preferences_title'],
            preferencesDescription: $attributes['preferences_description'],
            mediaNoticeTitle: $attributes['media_notice_title'],
            mediaNoticeDescription: $attributes['media_notice_description'],
        );
    }

    public function toArray(): array
    {
        return [
            'preferences_title' => $this->preferencesTitle,
            'preferences_description' => $this->preferencesDescription,
            'media_notice_title' => $this->mediaNoticeTitle,
            'media_notice_description' => $this->mediaNoticeDescription,
        ];
    }
}
