<script setup lang="ts">
import { onBeforeUnmount, onMounted } from 'vue';

let frameId = 0;
let navigationTimer = 0;
let scheduled = false;
let themeObserver: MutationObserver | null = null;
let previousMorningPalette = -1;
let previousSunsetPalette = -1;

type ThemeMode = 'morning' | 'sunset';
type AmbientPalette = {
    flare: string;
    soft: string;
    deep: string;
};

const morningPalettes: AmbientPalette[] = [
    {
        flare: '#cf6445',
        soft: '#f1c58d',
        deep: '#b83528',
    },
    {
        flare: '#d8892d',
        soft: '#f5d39a',
        deep: '#c95c2c',
    },
    {
        flare: '#c97b60',
        soft: '#f0c0a2',
        deep: '#b04f44',
    },
    {
        flare: '#b83528',
        soft: '#efb68a',
        deep: '#8f2b26',
    },
];

const sunsetPalettes: AmbientPalette[] = [
    {
        flare: '#f08b46',
        soft: '#e17a9e',
        deep: '#9f7ad8',
    },
    {
        flare: '#dd7a35',
        soft: '#f0a05e',
        deep: '#c75f88',
    },
    {
        flare: '#e17a9e',
        soft: '#f08b46',
        deep: '#8d63ce',
    },
    {
        flare: '#ff9a5c',
        soft: '#d86e96',
        deep: '#b476e1',
    },
];

function clamp(value: number, min: number, max: number): number {
    return Math.min(Math.max(value, min), max);
}

function currentTheme(): ThemeMode {
    return document.documentElement.getAttribute('data-theme') === 'sunset'
        ? 'sunset'
        : 'morning';
}

function pickPalette(theme: ThemeMode): AmbientPalette {
    const palettes = theme === 'sunset' ? sunsetPalettes : morningPalettes;

    if (palettes.length === 1) {
        return palettes[0];
    }

    const previousIndex =
        theme === 'sunset' ? previousSunsetPalette : previousMorningPalette;
    let nextIndex = previousIndex;

    while (nextIndex === previousIndex) {
        nextIndex = Math.floor(Math.random() * palettes.length);
    }

    if (theme === 'sunset') {
        previousSunsetPalette = nextIndex;
    } else {
        previousMorningPalette = nextIndex;
    }

    return palettes[nextIndex];
}

function applyAmbientPalette(theme = currentTheme()): void {
    const palette = pickPalette(theme);
    const rootStyle = document.documentElement.style;

    rootStyle.setProperty('--sw-ambient-flare', palette.flare);
    rootStyle.setProperty('--sw-ambient-flare-soft', palette.soft);
    rootStyle.setProperty('--sw-ambient-flare-deep', palette.deep);
}

function applyProgress(): void {
    scheduled = false;
    frameId = 0;

    const rootStyle = document.documentElement.style;
    const maxScroll = Math.max(
        document.documentElement.scrollHeight - window.innerHeight,
        window.innerHeight * 1.25,
        1,
    );
    const progress = clamp(
        (window.scrollY || window.pageYOffset || 0) / maxScroll,
        0,
        1,
    );

    rootStyle.setProperty('--sw-ambient-progress', progress.toFixed(4));
}

function scheduleProgress(): void {
    if (scheduled) {
        return;
    }

    scheduled = true;
    frameId = window.requestAnimationFrame(applyProgress);
}

function setNavigationShift(value: number): void {
    document.documentElement.style.setProperty(
        '--sw-ambient-navigation-shift',
        value.toFixed(3),
    );
}

function handleNavigationStart(): void {
    if (navigationTimer) {
        window.clearTimeout(navigationTimer);
    }

    applyAmbientPalette();
    setNavigationShift(1);
    scheduleProgress();
}

function handleNavigationSettle(): void {
    if (navigationTimer) {
        window.clearTimeout(navigationTimer);
    }

    setNavigationShift(0.38);
    scheduleProgress();
    navigationTimer = window.setTimeout(() => {
        setNavigationShift(0);
    }, 180);
}

function handleThemeMutation(): void {
    applyAmbientPalette();
    scheduleProgress();
}

onMounted(() => {
    applyAmbientPalette();
    scheduleProgress();
    window.addEventListener('scroll', scheduleProgress, { passive: true });
    window.addEventListener('resize', scheduleProgress, { passive: true });
    window.addEventListener('sidewalk:navigation-start', handleNavigationStart);
    window.addEventListener(
        'sidewalk:navigation-settle',
        handleNavigationSettle,
    );

    themeObserver = new MutationObserver((mutations) => {
        for (const mutation of mutations) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'data-theme') {
                handleThemeMutation();
                break;
            }
        }
    });

    themeObserver.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['data-theme'],
    });
});

onBeforeUnmount(() => {
    if (frameId) {
        window.cancelAnimationFrame(frameId);
    }

    if (navigationTimer) {
        window.clearTimeout(navigationTimer);
    }

    window.removeEventListener('scroll', scheduleProgress);
    window.removeEventListener('resize', scheduleProgress);
    window.removeEventListener('sidewalk:navigation-start', handleNavigationStart);
    window.removeEventListener(
        'sidewalk:navigation-settle',
        handleNavigationSettle,
    );
    themeObserver?.disconnect();
});
</script>

<template>
    <div class="ambient-grid" aria-hidden="true">
        <div class="ambient-grid__sun" />
        <div class="ambient-grid__shadow" />
        <div class="ambient-grid__plane" />
    </div>
</template>

<style scoped>
.ambient-grid {
    --grid-columns: 12;
    --ambient-progress: var(--sw-ambient-progress, 0);
    --ambient-nav: var(--sw-ambient-navigation-shift, 0);
    --ambient-sun-x: var(--sw-sun-vx, 14%);
    --ambient-sun-y: var(--sw-sun-vy, 10%);
    --ambient-sun-blur: var(--sw-sun-blur-global, 124px);
    --ambient-sun-opacity: var(--sw-sun-opacity-global, 0.72);
    --ambient-ray-angle: var(--sw-sun-ray-angle, 32deg);
    --ambient-sun-scale: calc(
        1.04 + (0.06 * var(--ambient-progress)) +
            (0.018 * var(--ambient-nav))
    );
    --ambient-grid-x: calc(
        -1.4% + (5.1% * var(--ambient-progress)) +
            (2.4% * var(--ambient-nav))
    );
    --ambient-grid-y: calc(
        -0.5% + (2.6% * var(--ambient-progress)) +
            (1.2% * var(--ambient-nav))
    );
    --ambient-grid-rotate: calc(
        -22deg + (88deg * var(--ambient-progress)) +
            (8deg * var(--ambient-nav))
    );
    --ambient-grid-scale: calc(
        1.08 + (0.03 * var(--ambient-progress)) +
            (0.012 * var(--ambient-nav))
    );
    --ambient-shadow-x: calc(
        5.6% + (2.2% * var(--ambient-progress)) +
            (3.2% * var(--ambient-nav))
    );
    --ambient-shadow-y: calc(2.2% + (2.8% * var(--ambient-progress)));
    --ambient-shadow-scale: calc(1.12 + (0.04 * var(--ambient-progress)));
    --ambient-shadow-blur: calc(
        52px + (16px * var(--ambient-progress)) +
            (6px * var(--ambient-nav))
    );
    --ambient-shadow-opacity: calc(
        0.24 + (0.04 * var(--ambient-progress)) +
            (0.02 * var(--ambient-nav))
    );
}

html[data-theme='sunset'] .ambient-grid {
    --ambient-sun-scale: calc(
        1.08 + (0.05 * var(--ambient-progress)) +
            (0.02 * var(--ambient-nav))
    );
    --ambient-grid-x: calc(
        2.1% - (3.4% * var(--ambient-progress)) -
            (1.8% * var(--ambient-nav))
    );
    --ambient-grid-y: calc(
        0.8% + (1.9% * var(--ambient-progress)) +
            (1.3% * var(--ambient-nav))
    );
    --ambient-grid-rotate: calc(
        16deg + (112deg * var(--ambient-progress)) +
            (10deg * var(--ambient-nav))
    );
    --ambient-grid-scale: calc(
        1.1 + (0.03 * var(--ambient-progress)) +
            (0.015 * var(--ambient-nav))
    );
    --ambient-shadow-x: calc(
        7.8% - (2.8% * var(--ambient-progress)) -
            (2.6% * var(--ambient-nav))
    );
    --ambient-shadow-y: calc(4.4% + (2.2% * var(--ambient-progress)));
    --ambient-shadow-scale: calc(1.16 + (0.03 * var(--ambient-progress)));
    --ambient-shadow-blur: calc(
        64px + (14px * var(--ambient-progress)) +
            (6px * var(--ambient-nav))
    );
    --ambient-shadow-opacity: calc(
        0.18 + (0.06 * var(--ambient-progress)) +
            (0.02 * var(--ambient-nav))
    );
}

.ambient-grid__plane,
.ambient-grid__sun,
.ambient-grid__shadow {
    position: absolute;
    inset: -8%;
    transform-origin: top center;
    pointer-events: none;
    transition:
        transform 120ms linear,
        filter 160ms linear,
        opacity 120ms linear,
        background-image 140ms linear;
}

.ambient-grid__sun {
    opacity: var(--ambient-sun-opacity);
    transform: scale(var(--ambient-sun-scale));
    filter: blur(var(--ambient-sun-blur));
    background:
        radial-gradient(
            circle at var(--ambient-sun-x) var(--ambient-sun-y),
            color-mix(in srgb, white 22%, transparent) 0,
            color-mix(in srgb, var(--sw-ambient-flare-soft) 48%, transparent)
                7%,
            color-mix(in srgb, var(--sw-ambient-flare) 26%, transparent) 18%,
            color-mix(in srgb, var(--sw-accent-dominant) 14%, transparent) 28%,
            transparent 36%
        ),
        radial-gradient(
            circle at var(--ambient-sun-x) var(--ambient-sun-y),
            color-mix(in srgb, var(--sw-ambient-flare) 20%, transparent),
            transparent 48%
        );
}

.ambient-grid__plane {
    opacity: 0.9;
    transform: translate3d(var(--ambient-grid-x), var(--ambient-grid-y), 0)
        rotate(var(--ambient-grid-rotate)) scale(var(--ambient-grid-scale));
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
        ),
        linear-gradient(
            var(--ambient-ray-angle),
            color-mix(in srgb, var(--sw-ambient-flare-soft) 14%, transparent),
            transparent 52%
        ),
        linear-gradient(
            90deg,
            transparent 0%,
            color-mix(
                in srgb,
                var(--sw-ambient-flare) calc(var(--ambient-nav) * 24%),
                transparent
            ) 46%,
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
    opacity: var(--ambient-shadow-opacity);
    transform: translate3d(var(--ambient-shadow-x), var(--ambient-shadow-y), 0)
        rotate(var(--ambient-grid-rotate)) scale(var(--ambient-shadow-scale));
    filter: blur(var(--ambient-shadow-blur));
    background-image:
        radial-gradient(
            circle at var(--ambient-sun-x) var(--ambient-sun-y),
            color-mix(in srgb, var(--sw-ambient-flare) 28%, transparent),
            transparent 24%
        ),
        linear-gradient(
            var(--ambient-ray-angle),
            color-mix(in srgb, var(--sw-ambient-flare-deep) 24%, transparent),
            transparent 78%
        ),
        linear-gradient(
            90deg,
            transparent 0%,
            color-mix(
                in srgb,
                var(--sw-ambient-flare-soft) calc(var(--ambient-nav) * 24%),
                transparent
            ) 38%,
            transparent 74%
        ),
        repeating-linear-gradient(
            90deg,
            color-mix(in srgb, var(--sw-ambient-flare) 20%, transparent) 0,
            color-mix(in srgb, var(--sw-ambient-flare) 20%, transparent) 1px,
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
    .ambient-grid__sun,
    .ambient-grid__plane,
    .ambient-grid__shadow {
        transition: none;
    }
}
</style>
