<template>
    <div class="ambient-grid" aria-hidden="true" />
</template>

<style scoped>
.ambient-grid {
    --grid-columns: 12;
    opacity: 0.9;
    transform: translate3d(-1.2%, -0.4%, 0) rotate(-1.75deg) scale(1.08);
    transform-origin: top center;
    animation: ambient-grid-drift 20s ease-in-out infinite alternate;
    will-change: transform;
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

@keyframes ambient-grid-drift {
    0% {
        transform: translate3d(-1.2%, -0.4%, 0) rotate(-1.75deg) scale(1.08);
    }

    50% {
        transform: translate3d(0.25%, 0.35%, 0) rotate(-2.1deg) scale(1.095);
    }

    100% {
        transform: translate3d(1.4%, 0.8%, 0) rotate(-2.35deg) scale(1.11);
    }
}

@media (max-width: 640px) {
    .ambient-grid {
        --grid-columns: 4;
    }
}

@media (prefers-reduced-motion: reduce) {
    .ambient-grid {
        animation: none;
    }
}
</style>
