<template>
    <div class="ambient-grid" aria-hidden="true" />
</template>

<style scoped>
.ambient-grid {
    --grid-columns: 12;
    opacity: 0.9;
    background-image:
        repeating-linear-gradient(
            90deg,
            var(--sw-grid-line) 0,
            var(--sw-grid-line) 1px,
            transparent 1px,
            transparent calc(100% / var(--grid-columns))
        ),
        linear-gradient(
            180deg,
            color-mix(in srgb, var(--sw-grid-line) 35%, transparent),
            transparent 72%
        );
    mask-image: linear-gradient(
        180deg,
        rgba(0, 0, 0, 0.82),
        rgba(0, 0, 0, 0.52) 58%,
        transparent 94%
    );
}

@media (max-width: 640px) {
    .ambient-grid {
        --grid-columns: 4;
    }
}
</style>
