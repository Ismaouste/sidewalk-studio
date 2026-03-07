<?php

namespace App\SiteSettings\Data;

final readonly class FeatureTogglesSettings
{
    public function __construct(
        public bool $showLabs,
        public bool $showWriting,
        public bool $showCaseStudies,
    ) {}

    public static function fromArray(array $attributes): self
    {
        return new self(
            showLabs: $attributes['show_labs'],
            showWriting: $attributes['show_writing'],
            showCaseStudies: $attributes['show_case_studies'],
        );
    }

    public function toArray(): array
    {
        return [
            'show_labs' => $this->showLabs,
            'show_writing' => $this->showWriting,
            'show_case_studies' => $this->showCaseStudies,
        ];
    }
}
