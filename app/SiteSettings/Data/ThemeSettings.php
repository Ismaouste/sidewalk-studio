<?php

namespace App\SiteSettings\Data;

final readonly class ThemeSettings
{
    public function __construct(
        public string $morningAccent,
        public string $morningGlow,
        public string $morningGlowSoft,
        public string $sunsetAccent,
        public string $sunsetGlow,
        public string $sunsetGlowSoft,
        public int $headerGradientAngle,
        public int $ambientBlurPx,
        public float $gridLinePx,
    ) {}

    public static function fromArray(array $attributes): self
    {
        return new self(
            morningAccent: $attributes['morning_accent'],
            morningGlow: $attributes['morning_glow'],
            morningGlowSoft: $attributes['morning_glow_soft'],
            sunsetAccent: $attributes['sunset_accent'],
            sunsetGlow: $attributes['sunset_glow'],
            sunsetGlowSoft: $attributes['sunset_glow_soft'],
            headerGradientAngle: (int) $attributes['header_gradient_angle'],
            ambientBlurPx: (int) $attributes['ambient_blur_px'],
            gridLinePx: (float) $attributes['grid_line_px'],
        );
    }

    public function toArray(): array
    {
        return [
            'morning_accent' => $this->morningAccent,
            'morning_glow' => $this->morningGlow,
            'morning_glow_soft' => $this->morningGlowSoft,
            'sunset_accent' => $this->sunsetAccent,
            'sunset_glow' => $this->sunsetGlow,
            'sunset_glow_soft' => $this->sunsetGlowSoft,
            'header_gradient_angle' => $this->headerGradientAngle,
            'ambient_blur_px' => $this->ambientBlurPx,
            'grid_line_px' => $this->gridLinePx,
        ];
    }
}
