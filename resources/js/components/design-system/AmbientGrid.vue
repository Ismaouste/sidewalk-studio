<script setup lang="ts">
/**
 * The drawing sheet under the page (specs/018-site-refresh): a flat grid of
 * hairlines with a small cross at every major intersection, the way a site
 * plan is ruled. Static and flat: no sun, no flare, no blur, no gradient.
 *
 * Kept under its old name so the import path in `SiteLayout.vue` stays stable.
 * Colour comes from `--sw-grid-line` in `resources/css/tokens.css`, so the
 * light and the night theme follow on their own.
 */
defineOptions({ name: 'AmbientGrid' });
</script>

<template>
    <svg class="ambient-grid" aria-hidden="true" focusable="false">
        <defs>
            <pattern
                id="sw-plan-minor"
                width="16"
                height="16"
                patternUnits="userSpaceOnUse"
            >
                <path d="M16 0H0V16" class="ambient-grid__minor" />
            </pattern>
            <pattern
                id="sw-plan-major"
                width="80"
                height="80"
                patternUnits="userSpaceOnUse"
            >
                <rect width="80" height="80" fill="url(#sw-plan-minor)" />
                <path d="M80 0H0V80" class="ambient-grid__major" />
                <path d="M-3 0H3M0 -3V3" class="ambient-grid__cross" />
            </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#sw-plan-major)" />
    </svg>
</template>

<style scoped>
.ambient-grid {
    position: fixed;
    inset: 0;
    z-index: var(--sw-z-base);
    width: 100%;
    height: 100%;
    pointer-events: none;
}

.ambient-grid__minor {
    fill: none;
    stroke: var(--sw-grid-line);
    stroke-width: 0.5;
    opacity: 0.5;
}

.ambient-grid__major {
    fill: none;
    stroke: var(--sw-grid-line);
    stroke-width: 1;
}

.ambient-grid__cross {
    fill: none;
    stroke: var(--sw-text-muted);
    stroke-width: 1;
    opacity: 0.55;
}
</style>
