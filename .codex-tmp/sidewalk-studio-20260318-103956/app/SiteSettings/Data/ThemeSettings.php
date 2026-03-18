<?php

namespace App\SiteSettings\Data;

final readonly class ThemeSettings
{
    public function __construct(
        public string $defaultTheme,
        public int $gradientAngle,
        public int $surfaceBlur,
        public int $lineThickness,
    ) {}

    public static function fromArray(array $attributes): self
    {
        return new self(
            defaultTheme: $attributes['default_theme'],
            gradientAngle: (int) $attributes['gradient_angle'],
            surfaceBlur: (int) $attributes['surface_blur'],
            lineThickness: (int) $attributes['line_thickness'],
        );
    }

    public function toArray(): array
    {
        return [
            'default_theme' => $this->defaultTheme,
            'gradient_angle' => $this->gradientAngle,
            'surface_blur' => $this->surfaceBlur,
            'line_thickness' => $this->lineThickness,
        ];
    }
}
