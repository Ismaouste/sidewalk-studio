<template>
    <div class="sw-sun-anchor" aria-hidden="true" />
</template>

<style scoped>
.sw-sun-anchor {
    position: absolute;
    top: var(--sw-sun-top);
    right: var(--sw-sun-right);
    bottom: var(--sw-sun-bottom);
    left: var(--sw-sun-left);
    z-index: 0;
    width: var(--sw-sun-size);
    height: var(--sw-sun-size);
    border-radius: var(--sw-radius-pill);
    background: var(--sw-sun-gradient);
    pointer-events: none;
    transition:
        top var(--sw-motion-sun),
        right var(--sw-motion-sun),
        bottom var(--sw-motion-sun),
        left var(--sw-motion-sun),
        background var(--sw-motion-sun);
    animation: sw-sun-drift 14s ease-in-out infinite;
}

@keyframes sw-sun-drift {
    0%,
    100% {
        transform: translate(0, 0) scale(1);
    }

    40% {
        transform: translate(8px, -6px) scale(1.04);
    }

    70% {
        transform: translate(-4px, 7px) scale(0.97);
    }
}

@media (prefers-reduced-motion: reduce) {
    .sw-sun-anchor {
        animation: none;
    }
}

@media (max-width: 640px) {
    .sw-sun-anchor {
        width: min(var(--sw-sun-size), 192px);
        height: min(var(--sw-sun-size), 192px);
        animation-duration: 18s;
    }
}
</style>
