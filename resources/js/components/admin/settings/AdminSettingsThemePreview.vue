<script setup lang="ts">
import { computed } from 'vue';
import { themeSettingsToCssVars } from '@/lib/themeStyleVars';
import type { ThemeSettingsPayload } from '@/types/site';

const props = defineProps<{
    settings: ThemeSettingsPayload;
}>();

const cssVars = computed(() => themeSettingsToCssVars(props.settings));
</script>

<template>
    <div class="admin-theme-preview" :style="cssVars">
        <article class="admin-theme-preview__card admin-theme-preview__card--morning">
            <span class="type-meta admin-theme-preview__eyebrow">Morning</span>
            <h3 class="type-h3 admin-theme-preview__title">Warm daylight shell</h3>
            <p class="type-body-sm admin-theme-preview__copy">
                Accent, glow, and shell angle previewed together.
            </p>
            <div class="admin-theme-preview__meta">
                <span class="admin-theme-preview__pill">Accent</span>
                <span class="admin-theme-preview__pill admin-theme-preview__pill--soft">Glow</span>
            </div>
            <button type="button" class="admin-theme-preview__button" disabled>
                Primary action
            </button>
        </article>

        <article class="admin-theme-preview__card admin-theme-preview__card--sunset">
            <span class="type-meta admin-theme-preview__eyebrow">Sunset</span>
            <h3 class="type-h3 admin-theme-preview__title">Lantern and afterglow</h3>
            <p class="type-body-sm admin-theme-preview__copy">
                Dark shell with a calm accent and warmer concert details.
            </p>
            <div class="admin-theme-preview__meta">
                <span class="admin-theme-preview__pill admin-theme-preview__pill--neutral">Accent</span>
                <span class="admin-theme-preview__pill admin-theme-preview__pill--soft-sunset">Glow</span>
            </div>
            <button
                type="button"
                class="admin-theme-preview__button admin-theme-preview__button--sunset"
                disabled
            >
                Primary action
            </button>
        </article>
    </div>
</template>

<style scoped>
.admin-theme-preview {
    display: grid;
    gap: var(--sw-space-sm);
}

.admin-theme-preview__card {
    position: relative;
    display: grid;
    gap: 0.7rem;
    overflow: hidden;
    min-height: 14rem;
    padding: clamp(18px, 3vw, 24px);
    border-radius: calc(var(--sw-radius-lg) + 4px);
    border: 1px solid transparent;
}

.admin-theme-preview__card::before {
    content: '';
    position: absolute;
    inset: -22% auto auto -10%;
    width: 52%;
    aspect-ratio: 1;
    border-radius: 999px;
    filter: blur(calc(var(--sw-config-ambient-blur) * 0.18));
    pointer-events: none;
}

.admin-theme-preview__card--morning {
    color: #1a1510;
    border-color: color-mix(
        in srgb,
        var(--sw-config-morning-accent) 34%,
        rgba(0, 0, 0, 0.1)
    );
    background:
        linear-gradient(
            var(--sw-config-header-angle),
            color-mix(in srgb, #ffffff 72%, var(--sw-config-morning-glow-soft) 28%),
            color-mix(in srgb, #f4f1ea 82%, var(--sw-config-morning-accent) 18%)
        );
    box-shadow: 0 18px 40px rgba(20, 18, 16, 0.08);
}

.admin-theme-preview__card--morning::before {
    background: radial-gradient(
        circle,
        color-mix(in srgb, var(--sw-config-morning-glow) 34%, transparent),
        transparent 68%
    );
}

.admin-theme-preview__card--sunset {
    color: #f2edf0;
    border-color: color-mix(
        in srgb,
        var(--sw-config-sunset-accent) 22%,
        rgba(255, 255, 255, 0.08)
    );
    background:
        radial-gradient(
            circle at 84% 20%,
            color-mix(in srgb, var(--sw-config-sunset-glow) 18%, transparent),
            transparent 34%
        ),
        linear-gradient(
            var(--sw-config-header-angle),
            color-mix(in srgb, #1c1d27 84%, var(--sw-config-sunset-glow-soft) 16%),
            color-mix(in srgb, #13141b 88%, var(--sw-config-sunset-accent) 12%)
        );
    box-shadow: 0 20px 46px rgba(0, 0, 0, 0.26);
}

.admin-theme-preview__card--sunset::before {
    background: radial-gradient(
        circle,
        color-mix(in srgb, var(--sw-config-sunset-glow-soft) 30%, transparent),
        transparent 70%
    );
}

.admin-theme-preview__title,
.admin-theme-preview__copy {
    position: relative;
    z-index: 1;
    margin: 0;
}

.admin-theme-preview__eyebrow {
    position: relative;
    z-index: 1;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.admin-theme-preview__meta {
    position: relative;
    z-index: 1;
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.admin-theme-preview__pill {
    display: inline-flex;
    align-items: center;
    min-height: 2rem;
    border-radius: 999px;
    padding: 0.35rem 0.7rem;
    background: color-mix(in srgb, var(--sw-config-morning-accent) 18%, white);
    color: color-mix(in srgb, var(--sw-config-morning-accent) 78%, #2a2018);
    font-size: 0.78rem;
    font-weight: 600;
}

.admin-theme-preview__pill--soft {
    background: color-mix(in srgb, var(--sw-config-morning-glow-soft) 26%, white);
    color: color-mix(in srgb, var(--sw-config-morning-glow) 78%, #51231c);
}

.admin-theme-preview__pill--neutral {
    background: color-mix(in srgb, var(--sw-config-sunset-accent) 18%, #1a1d26);
    color: color-mix(in srgb, var(--sw-config-sunset-accent) 86%, white);
}

.admin-theme-preview__pill--soft-sunset {
    background: color-mix(in srgb, var(--sw-config-sunset-glow-soft) 18%, #1a1d26);
    color: color-mix(in srgb, var(--sw-config-sunset-glow-soft) 78%, white);
}

.admin-theme-preview__button {
    position: relative;
    z-index: 1;
    width: fit-content;
    min-height: 2.75rem;
    border: 1px solid color-mix(in srgb, var(--sw-config-morning-accent) 76%, transparent);
    border-radius: 999px;
    background: var(--sw-config-morning-accent);
    padding-inline: 1rem;
    color: #f4f1ea;
    font-weight: 600;
}

.admin-theme-preview__button--sunset {
    border-color: color-mix(in srgb, var(--sw-config-sunset-accent) 34%, transparent);
    background: #2b313b;
    color: #f1f4f8;
}

@media (min-width: 960px) {
    .admin-theme-preview {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
</style>
