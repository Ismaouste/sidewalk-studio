<?php

namespace App\SiteSettings\Data;

final readonly class SiteIdentitySettings
{
    public function __construct(
        public string $name,
        public string $tagline,
        public string $description,
    ) {}

    public static function fromArray(array $attributes): self
    {
        return new self(
            name: $attributes['name'],
            tagline: $attributes['tagline'],
            description: $attributes['description'],
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'tagline' => $this->tagline,
            'description' => $this->description,
        ];
    }
}
