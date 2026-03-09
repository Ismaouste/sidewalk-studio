import type { ThemeSettingsPayload } from '@/types/site';

export function themeSettingsToCssVars(
    settings: ThemeSettingsPayload,
): Record<string, string> {
    return {
        '--sw-config-morning-accent': settings.morning_accent,
        '--sw-config-morning-glow': settings.morning_glow,
        '--sw-config-morning-glow-soft': settings.morning_glow_soft,
        '--sw-config-sunset-accent': settings.sunset_accent,
        '--sw-config-sunset-glow': settings.sunset_glow,
        '--sw-config-sunset-glow-soft': settings.sunset_glow_soft,
        '--sw-config-header-angle': `${settings.header_gradient_angle}deg`,
        '--sw-config-ambient-blur': `${settings.ambient_blur_px}px`,
        '--sw-config-grid-line': `${settings.grid_line_px}px`,
    };
}
