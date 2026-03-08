<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue';

const root = ref<HTMLElement | null>(null);

let frameId = 0;
let latestScroll = 0;
let gridProgress = 0;
let shadowProgress = 0;
let reducedMotionQuery: MediaQueryList | null = null;
let prefersReducedMotion = false;

function clamp(value: number, min: number, max: number): number {
    return Math.min(Math.max(value, min), max);
}

function syncMotionPreference(): void {
    prefersReducedMotion = reducedMotionQuery?.matches ?? false;
}

function updateScrollTarget(): void {
    latestScroll = window.scrollY || window.pageYOffset || 0;
}

function renderFrame(timestamp: number): void {
    if (!root.value) {
        return;
    }

    if (prefersReducedMotion) {
        root.value.style.setProperty('--grid-x', '-1.2%');
        root.value.style.setProperty('--grid-y', '-0.4%');
        root.value.style.setProperty('--grid-rotate', '-1.75deg');
        root.value.style.setProperty('--grid-scale', '1.08');
        root.value.style.setProperty('--shadow-x', '5%');
        root.value.style.setProperty('--shadow-y', '2.4%');
        root.value.style.setProperty('--shadow-rotate', '-1.15deg');
        root.value.style.setProperty('--shadow-scale', '1.11');
        root.value.style.setProperty('--shadow-blur', '28px');
        root.value.style.setProperty('--shadow-opacity', '0.18');
        return;
    }

    const viewportRange = Math.max(window.innerHeight * 1.8, 1);
    const targetProgress = clamp(latestScroll / viewportRange, 0, 1);
    const idleX = Math.sin(timestamp / 5400) * 0.45;
    const idleY = Math.cos(timestamp / 6800) * 0.28;

    gridProgress += (targetProgress - gridProgress) * 0.075;
    shadowProgress += (targetProgress - shadowProgress) * 0.038;

    root.value.style.setProperty(
        '--grid-x',
        `${-1.2 + gridProgress * 2.8 + idleX}%`,
    );
    root.value.style.setProperty(
        '--grid-y',
        `${-0.4 + gridProgress * 1.45 + idleY}%`,
    );
    root.value.style.setProperty(
        '--grid-rotate',
        `${-1.75 - gridProgress * 1.2 + idleX * 0.18}deg`,
    );
    root.value.style.setProperty(
        '--grid-scale',
        `${1.08 + gridProgress * 0.04}`,
    );

    root.value.style.setProperty(
        '--shadow-x',
        `${4.6 + shadowProgress * 4.4 + idleX * 1.6}%`,
    );
    root.value.style.setProperty(
        '--shadow-y',
        `${1.8 + shadowProgress * 2.6 + idleY * 2.2}%`,
    );
    root.value.style.setProperty(
        '--shadow-rotate',
        `${-1.1 - shadowProgress * 0.8 + idleX * 0.1}deg`,
    );
    root.value.style.setProperty(
        '--shadow-scale',
        `${1.11 + shadowProgress * 0.055}`,
    );
    root.value.style.setProperty(
        '--shadow-blur',
        `${28 + shadowProgress * 16}px`,
    );
    root.value.style.setProperty(
        '--shadow-opacity',
        `${0.18 + shadowProgress * 0.08}`,
    );

    frameId = window.requestAnimationFrame(renderFrame);
}

onMounted(() => {
    reducedMotionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
    syncMotionPreference();
    updateScrollTarget();

    reducedMotionQuery.addEventListener('change', syncMotionPreference);
    window.addEventListener('scroll', updateScrollTarget, { passive: true });
    window.addEventListener('resize', updateScrollTarget, { passive: true });

    frameId = window.requestAnimationFrame(renderFrame);
});

onBeforeUnmount(() => {
    if (frameId) {
        window.cancelAnimationFrame(frameId);
    }

    reducedMotionQuery?.removeEventListener('change', syncMotionPreference);
    window.removeEventListener('scroll', updateScrollTarget);
    window.removeEventListener('resize', updateScrollTarget);
});
</script>

<template>
    <div ref="root" class="ambient-grid" aria-hidden="true">
        <div class="ambient-grid__shadow" />
        <div class="ambient-grid__plane" />
    </div>
</template>

<style scoped>
.ambient-grid {
    --grid-columns: 12;
    --grid-x: -1.2%;
    --grid-y: -0.4%;
    --grid-rotate: -1.75deg;
    --grid-scale: 1.08;
    --shadow-x: 5%;
    --shadow-y: 2.4%;
    --shadow-rotate: -1.15deg;
    --shadow-scale: 1.11;
    --shadow-blur: 28px;
    --shadow-opacity: 0.18;
}

.ambient-grid__plane,
.ambient-grid__shadow {
    position: absolute;
    inset: -4%;
    transform-origin: top center;
    will-change: transform, filter, opacity;
    pointer-events: none;
}

.ambient-grid__plane {
    opacity: 0.9;
    transform: translate3d(var(--grid-x), var(--grid-y), 0)
        rotate(var(--grid-rotate)) scale(var(--grid-scale));
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

.ambient-grid__shadow {
    opacity: var(--shadow-opacity);
    transform: translate3d(var(--shadow-x), var(--shadow-y), 0)
        rotate(var(--shadow-rotate)) scale(var(--shadow-scale));
    filter: blur(var(--shadow-blur));
    background-image:
        radial-gradient(
            circle at 14% 10%,
            color-mix(in srgb, var(--sw-accent-sun) 26%, transparent),
            transparent 38%
        ),
        linear-gradient(
            180deg,
            color-mix(in srgb, var(--sw-accent-dominant) 18%, transparent),
            transparent 78%
        ),
        repeating-linear-gradient(
            90deg,
            color-mix(in srgb, var(--sw-accent-sun) 18%, transparent) 0,
            color-mix(in srgb, var(--sw-accent-sun) 18%, transparent) 1px,
            transparent 1px,
            transparent calc(100% / var(--grid-columns))
        );
    mask-image: linear-gradient(
        180deg,
        rgba(0, 0, 0, 0.42),
        rgba(0, 0, 0, 0.22) 58%,
        transparent 96%
    );
}

@media (max-width: 640px) {
    .ambient-grid {
        --grid-columns: 4;
    }
}

@media (prefers-reduced-motion: reduce) {
    .ambient-grid__plane,
    .ambient-grid__shadow {
        will-change: auto;
    }
}
</style>
