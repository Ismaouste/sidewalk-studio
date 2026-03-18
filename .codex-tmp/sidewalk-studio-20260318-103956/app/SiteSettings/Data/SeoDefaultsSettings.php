<?php

namespace App\SiteSettings\Data;

final readonly class SeoDefaultsSettings
{
    public function __construct(
        public string $titleSuffix,
        public string $defaultDescription,
        public string $defaultRobots,
    ) {}

    public static function fromArray(array $attributes): self
    {
        return new self(
            titleSuffix: $attributes['title_suffix'],
            defaultDescription: $attributes['default_description'],
            defaultRobots: $attributes['default_robots'],
        );
    }

    public function toArray(): array
    {
        return [
            'title_suffix' => $this->titleSuffix,
            'default_description' => $this->defaultDescription,
            'default_robots' => $this->defaultRobots,
        ];
    }
}
