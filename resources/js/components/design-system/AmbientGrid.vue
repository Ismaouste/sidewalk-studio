<script setup lang="ts">
/**
 * AmbientGrid is now driven entirely by tokens + CSS.
 * - Palette: read from --sw-ambient-flare(/-soft/-deep) defined per theme
 *   in resources/css/tokens.css. Theme switching auto-propagates via
 *   html[data-theme] and CSS variable inheritance.
 * - Movement: scroll-driven keyframes, gated on
 *   @supports (animation-timeline: scroll()) and prefers-reduced-motion.
 *   On unsupported browsers / reduced motion / mobile, the layers render
 *   as a static atmospheric placeholder.
 *
 * Kept as a Vue file so the existing import path stays stable and a
 * future enhancement can add reactive props if needed.
 */
defineOptions({ name: 'AmbientGrid' });
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
    --ambient-sun-x: 14%;
    --ambient-sun-y: 10%;
    --ambient-sun-scale: 1.04;
    --ambient-sun-blur: 124px;
    --ambient-sun-opacity: 0.72;
    --ambient-ray-angle: 32deg;
    --ambient-grid-x: -1.4%;
    --ambient-grid-y: -0.5%;
    --ambient-grid-rotate: -22deg;
    --ambient-grid-scale: 1.08;
    --ambient-shadow-x: 5.6%;
    --ambient-shadow-y: 2.2%;
    --ambient-shadow-scale: 1.12;
    --ambient-shadow-blur: 52px;
    --ambient-shadow-opacity: 0.24;
}

html[data-theme='sunset'] .ambient-grid {
    --ambient-sun-x: 78%;
    --ambient-sun-y: 18%;
    --ambient-sun-scale: 1.08;
    --ambient-sun-blur: 136px;
    --ambient-sun-opacity: 0.38;
    --ambient-ray-angle: 18deg;
    --ambient-grid-x: 2.1%;
    --ambient-grid-y: 0.8%;
    --ambient-grid-rotate: 16deg;
    --ambient-grid-scale: 1.1;
    --ambient-shadow-x: 7.8%;
    --ambient-shadow-y: 4.4%;
    --ambient-shadow-scale: 1.16;
    --ambient-shadow-blur: 64px;
    --ambient-shadow-opacity: 0.18;
}

.ambient-grid__plane,
.ambient-grid__sun,
.ambient-grid__shadow {
    position: absolute;
    inset: -8%;
    transform-origin: top center;
    pointer-events: none;
    transition: background-image 180ms linear;
}

.ambient-grid__sun {
    opacity: var(--ambient-sun-opacity);
    transform: translate3d(0, 0, 0) scale(var(--ambient-sun-scale));
    filter: blur(var(--ambient-sun-blur));
    background:
        radial-gradient(
            circle at var(--ambient-sun-x) var(--ambient-sun-y),
            color-mix(in srgb, white 22%, transparent) 0,
            color-mix(in srgb, var(--sw-ambient-flare-soft) 48%, transparent) 7%,
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
    opacity: 0.76;
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
            color-mix(in srgb, var(--sw-ambient-flare) 10%, transparent) 46%,
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
            color-mix(in srgb, var(--sw-ambient-flare-soft) 12%, transparent)
                38%,
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

@supports (animation-timeline: scroll()) {
    .ambient-grid__sun,
    .ambient-grid__plane,
    .ambient-grid__shadow {
        animation-duration: 1s;
        animation-timing-function: linear;
        animation-fill-mode: both;
        animation-timeline: scroll(root block);
        animation-range: 0% 100%;
        will-change: transform, opacity, filter;
    }

    .ambient-grid__sun {
        animation-name: ambient-sun-morning;
    }

    .ambient-grid__plane {
        animation-name: ambient-plane-morning;
    }

    .ambient-grid__shadow {
        animation-name: ambient-shadow-morning;
    }

    html[data-theme='sunset'] .ambient-grid__sun {
        animation-name: ambient-sun-sunset;
    }

    html[data-theme='sunset'] .ambient-grid__plane {
        animation-name: ambient-plane-sunset;
    }

    html[data-theme='sunset'] .ambient-grid__shadow {
        animation-name: ambient-shadow-sunset;
    }
}

@keyframes ambient-sun-morning {
    from {
        transform: translate3d(-2%, -1%, 0) scale(1.04);
        opacity: 0.72;
        filter: blur(124px);
    }

    to {
        transform: translate3d(4%, 6%, 0) scale(1.1);
        opacity: 0.68;
        filter: blur(148px);
    }
}

@keyframes ambient-plane-morning {
    from {
        transform: translate3d(-1.4%, -0.5%, 0) rotate(-22deg) scale(1.08);
    }

    to {
        transform: translate3d(3.8%, 2.1%, 0) rotate(66deg) scale(1.11);
    }
}

@keyframes ambient-shadow-morning {
    from {
        transform: translate3d(5.6%, 2.2%, 0) rotate(-22deg) scale(1.12);
        opacity: 0.24;
        filter: blur(52px);
    }

    to {
        transform: translate3d(7.8%, 5%, 0) rotate(66deg) scale(1.16);
        opacity: 0.28;
        filter: blur(68px);
    }
}

@keyframes ambient-sun-sunset {
    from {
        transform: translate3d(1%, 1%, 0) scale(1.08);
        opacity: 0.38;
        filter: blur(136px);
    }

    to {
        transform: translate3d(-5%, 8%, 0) scale(1.13);
        opacity: 0.44;
        filter: blur(160px);
    }
}

@keyframes ambient-plane-sunset {
    from {
        transform: translate3d(2.1%, 0.8%, 0) rotate(16deg) scale(1.1);
    }

    to {
        transform: translate3d(-1.6%, 2.8%, 0) rotate(128deg) scale(1.13);
    }
}

@keyframes ambient-shadow-sunset {
    from {
        transform: translate3d(7.8%, 4.4%, 0) rotate(16deg) scale(1.16);
        opacity: 0.18;
        filter: blur(64px);
    }

    to {
        transform: translate3d(4.8%, 6.8%, 0) rotate(128deg) scale(1.19);
        opacity: 0.24;
        filter: blur(78px);
    }
}

@media (max-width: 640px) {
    .ambient-grid {
        --grid-columns: 4;
    }

    .ambient-grid__plane {
        opacity: 0.58;
    }

    .ambient-grid__shadow {
        opacity: 0.12;
    }
}

@media (max-width: 768px) {
    .ambient-grid__sun,
    .ambient-grid__plane,
    .ambient-grid__shadow {
        animation: none !important;
        transition: none;
        will-change: auto;
    }
}

@media (prefers-reduced-motion: reduce) {
    .ambient-grid__sun,
    .ambient-grid__plane,
    .ambient-grid__shadow {
        animation: none !important;
        transition: none;
        will-change: auto;
    }
}

:global(html[data-motion='reduced'] .ambient-grid__sun),
:global(html[data-motion='reduced'] .ambient-grid__plane),
:global(html[data-motion='reduced'] .ambient-grid__shadow) {
    animation: none !important;
    transition: none;
    will-change: auto;
}
</style>
