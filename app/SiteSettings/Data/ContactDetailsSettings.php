<?php

namespace App\SiteSettings\Data;

final readonly class ContactDetailsSettings
{
    public function __construct(
        public string $email,
        public string $location,
        public string $availability,
    ) {}

    public static function fromArray(array $attributes): self
    {
        return new self(
            email: $attributes['email'],
            location: $attributes['location'],
            availability: $attributes['availability'],
        );
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'location' => $this->location,
            'availability' => $this->availability,
        ];
    }
}
