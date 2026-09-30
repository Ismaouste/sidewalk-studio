<?php

namespace App\SiteSettings\Data;

final readonly class SocialLinksSettings
{
    public function __construct(
        public ?string $githubUrl,
        public ?string $linkedinUrl,
    ) {}

    public static function fromArray(array $attributes): self
    {
        return new self(
            githubUrl: $attributes['github_url'],
            linkedinUrl: $attributes['linkedin_url'],
        );
    }

    public function sameAs(): array
    {
        // The owner's own public projects that identify him: his GitHub, his
        // LinkedIn when set, and the astralmanach site he edits. Un art voulu
        // voyant belongs to Florian Rosinski and is not an identity of the owner.
        return array_values(array_filter([
            $this->githubUrl,
            $this->linkedinUrl,
            'https://astralmanach.eu',
        ]));
    }

    public function toArray(): array
    {
        return [
            'github_url' => $this->githubUrl,
            'linkedin_url' => $this->linkedinUrl,
        ];
    }
}
