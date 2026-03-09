<?php

namespace App\SiteSettings\Data;

final readonly class PublishingStateSettings
{
    public function __construct(
        public bool $rebuildRequired,
        public ?string $lastRebuiltAt,
        public ?string $lastChangeSummary,
    ) {}

    public static function fromArray(array $attributes): self
    {
        return new self(
            rebuildRequired: (bool) $attributes['rebuild_required'],
            lastRebuiltAt: $attributes['last_rebuilt_at'] ?: null,
            lastChangeSummary: $attributes['last_change_summary'] ?: null,
        );
    }

    public function toArray(): array
    {
        return [
            'rebuild_required' => $this->rebuildRequired,
            'last_rebuilt_at' => $this->lastRebuiltAt,
            'last_change_summary' => $this->lastChangeSummary,
        ];
    }
}
