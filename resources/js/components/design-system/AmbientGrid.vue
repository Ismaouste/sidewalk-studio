<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue';

const root = ref<HTMLElement | null>(null);

let frameId = 0;
let latestScroll = 0;
let reducedMotionQuery: MediaQueryList | null = null;
let prefersReducedMotion = false;
let sunX = 14;
let sunY = 10;
let sunScale = 1.04;
let sunBlur = 120;
let sunOpacity = 0.7;
let gridX = -1.4;
let gridY = -0.5;
let gridRotate = -18;
let gridScale = 1.08;
let shadowX = 4.8;
let shadowY = 1.8;
let shadowScale = 1.12;
let shadowBlur = 42;
let shadowOpacity = 0.18;
let rayAngle = 28;

function clamp(value: number, min: number, max: number): number {
    return Math.min(Math.max(value, min), max);
}

function currentTheme(): 'morning' | 'sunset' {
    return document.documentElement.getAttribute('data-theme') === 'sunset'
        ? 'sunset'
        : 'morning';
}

function syncMotionPreference(): void {
    prefersReducedMotion = reducedMotionQuery?.matches ?? false;
}

function updateScrollTarget(): void {
    latestScroll = window.scrollY || window.pageYOffset || 0;
}

function applySceneVariables(): void {
    if (!root.value) {
        return;
    }

    const target = root.value.style;
    const documentStyle = document.documentElement.style;

    target.setProperty('--sun-x', `${sunX}%`);
    target.setProperty('--sun-y', `${sunY}%`);
    target.setProperty('--sun-scale', `${sunScale}`);
    target.setProperty('--sun-blur', `${sunBlur}px`);
    target.setProperty('--sun-opacity', `${sunOpacity}`);
    target.setProperty('--grid-x', `${gridX}%`);
    target.setProperty('--grid-y', `${gridY}%`);
    target.setProperty('--grid-rotate', `${gridRotate}deg`);
    target.setProperty('--grid-scale', `${gridScale}`);
    target.setProperty('--shadow-x', `${shadowX}%`);
    target.setProperty('--shadow-y', `${shadowY}%`);
    target.setProperty('--shadow-scale', `${shadowScale}`);
    target.setProperty('--shadow-blur', `${shadowBlur}px`);
    target.setProperty('--shadow-opacity', `${shadowOpacity}`);
    target.setProperty('--ray-angle', `${rayAngle}deg`);

    documentStyle.setProperty('--sw-sun-vx', `${sunX}%`);
    documentStyle.setProperty('--sw-sun-vy', `${sunY}%`);
    documentStyle.setProperty('--sw-sun-blur-global', `${sunBlur}px`);
    documentStyle.setProperty('--sw-sun-opacity-global', `${sunOpacity}`);
    documentStyle.setProperty('--sw-sun-ray-angle', `${rayAngle}deg`);
}

function setReducedMotionState(theme: 'morning' | 'sunset'): void {
    if (theme === 'sunset') {
        sunX = 78;
        sunY = 18;
        sunScale = 1.08;
        sunBlur = 132;
        sunOpacity = 0.54;
        gridX = 2.1;
        gridY = 0.8;
        gridRotate = 16;
        gridScale = 1.1;
        shadowX = 7.8;
        shadowY = 4.4;
        shadowScale = 1.16;
        shadowBlur = 58;
        shadowOpacity = 0.22;
        rayAngle = 18;
    } else {
        sunX = 14;
        sunY = 10;
        sunScale = 1.04;
        sunBlur = 124;
        sunOpacity = 0.72;
        gridX = -1.3;
        gridY = -0.4;
        gridRotate = -22;
        gridScale = 1.08;
        shadowX = 5.6;
        shadowY = 2.2;
        shadowScale = 1.12;
        shadowBlur = 52;
        shadowOpacity = 0.24;
        rayAngle = 32;
    }

    applySceneVariables();
}

function renderFrame(timestamp: number): void {
    if (!root.value) {
        return;
    }

    const theme = currentTheme();

    if (prefersReducedMotion) {
        setReducedMotionState(theme);
        frameId = window.requestAnimationFrame(renderFrame);
        return;
    }

    const maxScroll = Math.max(
        document.documentElement.scrollHeight - window.innerHeight,
        window.innerHeight * 1.25,
        1,
    );
    const scrollProgress = clamp(latestScroll / maxScroll, 0, 1);
    const idleX = Math.sin(timestamp / 6200) * 1.4;
    const idleY = Math.cos(timestamp / 7600) * 1.15;

    const sunTargetX =
        theme === 'sunset'
            ? 78 - scrollProgress * 12 + idleX * 0.5
            : 13 + scrollProgress * 34 + idleX * 0.75;
    const sunTargetY =
        theme === 'sunset'
            ? 18 + scrollProgress * 36 + idleY * 0.6
            : 9 + scrollProgress * 22 + idleY * 0.55;
    const focusX = theme === 'sunset' ? 62 : 54;
    const focusY = theme === 'sunset' ? 52 : 34;
    const angleRadians = Math.atan2(focusY - sunTargetY, focusX - sunTargetX);
    const angleDegrees = (angleRadians * 180) / Math.PI;
    const vectorX = Math.cos(angleRadians);
    const vectorY = Math.sin(angleRadians);

    const gridTargetX =
        (theme === 'sunset' ? 1 : -1) * (sunTargetX - 50) * 0.08 +
        vectorX * 1.45;
    const gridTargetY =
        (theme === 'sunset' ? 1 : -0.7) * (sunTargetY - 34) * 0.05 +
        vectorY * 0.95;
    const shadowTargetX =
        gridTargetX + vectorX * (theme === 'sunset' ? 4.8 : 5.6);
    const shadowTargetY =
        gridTargetY + vectorY * (theme === 'sunset' ? 3.8 : 4.4);
    const rotationTarget = angleDegrees - 92;
    const shadowScaleTarget =
        (theme === 'sunset' ? 1.16 : 1.12) + scrollProgress * 0.04;
    const gridScaleTarget =
        (theme === 'sunset' ? 1.1 : 1.08) + scrollProgress * 0.03;
    const sunScaleTarget =
        (theme === 'sunset' ? 1.08 : 1.04) + scrollProgress * 0.06;
    const sunBlurTarget =
        (theme === 'sunset' ? 118 : 132) + scrollProgress * 34;
    const shadowBlurTarget =
        (theme === 'sunset' ? 54 : 48) + scrollProgress * 20;
    const sunOpacityTarget =
        (theme === 'sunset' ? 0.5 : 0.68) + scrollProgress * 0.06;
    const shadowOpacityTarget =
        (theme === 'sunset' ? 0.24 : 0.2) + scrollProgress * 0.08;

    sunX += (sunTargetX - sunX) * 0.055;
    sunY += (sunTargetY - sunY) * 0.055;
    sunScale += (sunScaleTarget - sunScale) * 0.04;
    sunBlur += (sunBlurTarget - sunBlur) * 0.04;
    sunOpacity += (sunOpacityTarget - sunOpacity) * 0.045;
    gridX += (gridTargetX - gridX) * 0.08;
    gridY += (gridTargetY - gridY) * 0.08;
    gridRotate += (rotationTarget - gridRotate) * 0.075;
    gridScale += (gridScaleTarget - gridScale) * 0.05;
    shadowX += (shadowTargetX - shadowX) * 0.038;
    shadowY += (shadowTargetY - shadowY) * 0.038;
    shadowScale += (shadowScaleTarget - shadowScale) * 0.03;
    shadowBlur += (shadowBlurTarget - shadowBlur) * 0.03;
    shadowOpacity += (shadowOpacityTarget - shadowOpacity) * 0.03;
    rayAngle += (angleDegrees - rayAngle) * 0.045;

    applySceneVariables();

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

    document.documentElement.style.removeProperty('--sw-sun-vx');
    document.documentElement.style.removeProperty('--sw-sun-vy');
    document.documentElement.style.removeProperty('--sw-sun-blur-global');
    document.documentElement.style.removeProperty('--sw-sun-opacity-global');
    document.documentElement.style.removeProperty('--sw-sun-ray-angle');

    reducedMotionQuery?.removeEventListener('change', syncMotionPreference);
    window.removeEventListener('scroll', updateScrollTarget);
    window.removeEventListener('resize', updateScrollTarget);
});
</script>

<template>
    <div ref="root" class="ambient-grid" aria-hidden="true">
        <div class="ambient-grid__sun" />
        <div class="ambient-grid__shadow" />
        <div class="ambient-grid__plane" />
    </div>
</template>

<style scoped>
.ambient-grid {
    --grid-columns: 12;
    --sun-x: 14%;
    --sun-y: 10%;
    --sun-scale: 1.04;
    --sun-blur: 124px;
    --sun-opacity: 0.72;
    --grid-x: -1.4%;
    --grid-y: -0.5%;
    --grid-rotate: -22deg;
    --grid-scale: 1.08;
    --shadow-x: 5.6%;
    --shadow-y: 2.2%;
    --shadow-scale: 1.12;
    --shadow-blur: 52px;
    --shadow-opacity: 0.24;
    --ray-angle: 32deg;
}

.ambient-grid__plane,
.ambient-grid__sun,
.ambient-grid__shadow {
    position: absolute;
    inset: -8%;
    transform-origin: top center;
    will-change: transform, filter, opacity;
    pointer-events: none;
}

.ambient-grid__sun {
    opacity: var(--sun-opacity);
    transform: scale(var(--sun-scale));
    filter: blur(var(--sun-blur));
    background:
        radial-gradient(
            circle at var(--sun-x) var(--sun-y),
            color-mix(in srgb, white 22%, transparent) 0,
            color-mix(in srgb, var(--sw-accent-sun) 34%, transparent) 7%,
            color-mix(in srgb, var(--sw-accent-dominant) 18%, transparent) 18%,
            transparent 36%
        ),
        radial-gradient(
            circle at var(--sun-x) var(--sun-y),
            color-mix(in srgb, var(--sw-accent-sun) 16%, transparent),
            transparent 48%
        );
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
        ),
        linear-gradient(
            var(--ray-angle),
            color-mix(in srgb, var(--sw-accent-sun) 10%, transparent),
            transparent 52%
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
        rotate(var(--grid-rotate)) scale(var(--shadow-scale));
    filter: blur(var(--shadow-blur));
    background-image:
        radial-gradient(
            circle at var(--sun-x) var(--sun-y),
            color-mix(in srgb, var(--sw-accent-sun) 24%, transparent),
            transparent 24%
        ),
        linear-gradient(
            var(--ray-angle),
            color-mix(in srgb, var(--sw-accent-dominant) 20%, transparent),
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
    .ambient-grid__sun,
    .ambient-grid__plane,
    .ambient-grid__shadow {
        will-change: auto;
    }
}
</style>
