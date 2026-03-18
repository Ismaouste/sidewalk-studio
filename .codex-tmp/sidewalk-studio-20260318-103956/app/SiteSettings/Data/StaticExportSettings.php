<?php

namespace App\SiteSettings\Data;

final readonly class StaticExportSettings
{
    public function __construct(
        public bool $staticModeEnabled,
        public bool $githubPagesEnabled,
        public bool $preprodModeEnabled,
        public string $exportBasePath,
        public string $exportOutputPath,
    ) {}

    public static function fromArray(array $attributes): self
    {
        return new self(
            staticModeEnabled: (bool) $attributes['static_mode_enabled'],
            githubPagesEnabled: (bool) $attributes['github_pages_enabled'],
            preprodModeEnabled: (bool) $attributes['preprod_mode_enabled'],
            exportBasePath: $attributes['export_base_path'],
            exportOutputPath: $attributes['export_output_path'],
        );
    }

    public function toArray(): array
    {
        return [
            'static_mode_enabled' => $this->staticModeEnabled,
            'github_pages_enabled' => $this->githubPagesEnabled,
            'preprod_mode_enabled' => $this->preprodModeEnabled,
            'export_base_path' => $this->exportBasePath,
            'export_output_path' => $this->exportOutputPath,
        ];
    }
}
