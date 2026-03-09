<?php

namespace App\SiteSettings\Data;

final readonly class AdminStateSettings
{
    public function __construct(
        public ?string $onboardingCompletedAt,
        public ?string $primaryOperatorEmail,
    ) {}

    public static function fromArray(array $attributes): self
    {
        return new self(
            onboardingCompletedAt: $attributes['onboarding_completed_at'] ?: null,
            primaryOperatorEmail: $attributes['primary_operator_email'] ?: null,
        );
    }

    public function toArray(): array
    {
        return [
            'onboarding_completed_at' => $this->onboardingCompletedAt,
            'primary_operator_email' => $this->primaryOperatorEmail,
        ];
    }
}
