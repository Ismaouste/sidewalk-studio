<script setup lang="ts">
import type { BrandingSettings } from '@/types';

defineProps<{
    branding: BrandingSettings;
    className?: string;
}>();
</script>

<template>
    <div class="brand-mark" :class="[className, `brand-mark--${branding.fallback_variant}`]">
        <img
            v-if="branding.asset_mode === 'uploaded' && branding.uploaded_asset_path"
            :src="branding.uploaded_asset_path"
            :alt="branding.active_alt"
            class="brand-mark__image"
            loading="lazy"
            decoding="async"
        />
        <div v-else class="brand-mark__fallback">
            <span class="brand-mark__label">{{ branding.fallback_label }}</span>
            <span
                v-if="branding.fallback_variant === 'wordmark'"
                class="brand-mark__subtitle"
            >
                {{ branding.fallback_subtitle }}
            </span>
        </div>
    </div>
</template>

<style scoped>
.brand-mark,
.brand-mark__fallback {
    display: inline-grid;
    place-items: center;
}

.brand-mark {
    inline-size: 2.5rem;
    block-size: 2.5rem;
}

.brand-mark__image,
.brand-mark__fallback {
    inline-size: 100%;
    block-size: 100%;
    border-radius: 999px;
}

.brand-mark__image {
    object-fit: cover;
}

.brand-mark__fallback {
    border: 1px solid color-mix(in srgb, var(--sw-border) 88%, transparent);
    background: linear-gradient(
        145deg,
        color-mix(in srgb, var(--sw-bg-elevated) 92%, white 8%),
        color-mix(in srgb, var(--sw-bg-surface) 88%, transparent)
    );
    color: var(--sw-text-primary);
}

.brand-mark--monogram_square .brand-mark__fallback {
    border-radius: var(--sw-radius-md);
}

.brand-mark--wordmark {
    inline-size: auto;
    min-inline-size: 5rem;
}

.brand-mark--wordmark .brand-mark__fallback {
    inline-size: auto;
    block-size: auto;
    gap: 0.1rem;
    padding: 0.45rem 0.7rem;
    border-radius: var(--sw-radius-md);
    justify-items: start;
}

.brand-mark__label {
    font-size: 0.82rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.brand-mark__subtitle {
    font-size: 0.65rem;
    color: var(--sw-text-secondary);
}
</style>
