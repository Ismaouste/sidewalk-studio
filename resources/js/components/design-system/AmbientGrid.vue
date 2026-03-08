<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue';

const root = ref<HTMLElement | null>(null);

type SceneState = {
    sunX: number;
    sunY: number;
    sunScale: number;
    sunBlur: number;
    sunOpacity: number;
    gridX: number;
    gridY: number;
    gridRotate: number;
    gridScale: number;
    shadowX: number;
    shadowY: number;
    shadowScale: number;
    shadowBlur: number;
    shadowOpacity: number;
    rayAngle: number;
    navigationImpulse: number;
    navigationTint: number;
};

const scene: SceneState = {
    sunX: 14,
    sunY: 10,
    sunScale: 1.04,
    sunBlur: 120,
    sunOpacity: 0.7,
    gridX: -1.4,
    gridY: -0.5,
    gridRotate: -18,
    gridScale: 1.08,
    shadowX: 4.8,
    shadowY: 1.8,
    shadowScale: 1.12,
    shadowBlur: 42,
    shadowOpacity: 0.18,
    rayAngle: 28,
    navigationImpulse: 0,
    navigationTint: 0,
};

let frameId = 0;
let latestScroll = 0;
let reducedMotionQuery: MediaQueryList | null = null;
let themeObserver: MutationObserver | null = null;
let prefersReducedMotion = false;
let isAnimating = false;

const SETTLE_EPSILON = 0.02;
const SETTLE_BLUR_EPSILON = 0.12;

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

    const documentStyle = document.documentElement.style;

    documentStyle.setProperty('--sw-ambient-sun-x', `${scene.sunX}%`);
    documentStyle.setProperty('--sw-ambient-sun-y', `${scene.sunY}%`);
    documentStyle.setProperty('--sw-ambient-sun-scale', `${scene.sunScale}`);
    documentStyle.setProperty('--sw-ambient-sun-blur', `${scene.sunBlur}px`);
    documentStyle.setProperty('--sw-ambient-sun-opacity', `${scene.sunOpacity}`);
    documentStyle.setProperty('--sw-ambient-grid-x', `${scene.gridX}%`);
    documentStyle.setProperty('--sw-ambient-grid-y', `${scene.gridY}%`);
    documentStyle.setProperty('--sw-ambient-grid-rotate', `${scene.gridRotate}deg`);
    documentStyle.setProperty('--sw-ambient-grid-scale', `${scene.gridScale}`);
    documentStyle.setProperty('--sw-ambient-shadow-x', `${scene.shadowX}%`);
    documentStyle.setProperty('--sw-ambient-shadow-y', `${scene.shadowY}%`);
    documentStyle.setProperty('--sw-ambient-shadow-scale', `${scene.shadowScale}`);
    documentStyle.setProperty('--sw-ambient-shadow-blur', `${scene.shadowBlur}px`);
    documentStyle.setProperty('--sw-ambient-shadow-opacity', `${scene.shadowOpacity}`);
    documentStyle.setProperty('--sw-ambient-ray-angle', `${scene.rayAngle}deg`);
    documentStyle.setProperty('--sw-sun-vx', `${scene.sunX}%`);
    documentStyle.setProperty('--sw-sun-vy', `${scene.sunY}%`);
    documentStyle.setProperty('--sw-sun-blur-global', `${scene.sunBlur}px`);
    documentStyle.setProperty('--sw-sun-opacity-global', `${scene.sunOpacity}`);
    documentStyle.setProperty('--sw-sun-ray-angle', `${scene.rayAngle}deg`);
    documentStyle.setProperty(
        '--sw-ambient-navigation-tint',
        `${scene.navigationTint}`,
    );
}

function setReducedMotionState(theme: 'morning' | 'sunset'): void {
    if (theme === 'sunset') {
        scene.sunX = 78;
        scene.sunY = 16;
        scene.sunScale = 1.06;
        scene.sunBlur = 142;
        scene.sunOpacity = 0.42;
        scene.gridX = 2.1;
        scene.gridY = 0.8;
        scene.gridRotate = 16;
        scene.gridScale = 1.1;
        scene.shadowX = 7.8;
        scene.shadowY = 4.4;
        scene.shadowScale = 1.16;
        scene.shadowBlur = 64;
        scene.shadowOpacity = 0.18;
        scene.rayAngle = 18;
    } else {
        scene.sunX = 14;
        scene.sunY = 10;
        scene.sunScale = 1.04;
        scene.sunBlur = 124;
        scene.sunOpacity = 0.72;
        scene.gridX = -1.3;
        scene.gridY = -0.4;
        scene.gridRotate = -22;
        scene.gridScale = 1.08;
        scene.shadowX = 5.6;
        scene.shadowY = 2.2;
        scene.shadowScale = 1.12;
        scene.shadowBlur = 52;
        scene.shadowOpacity = 0.24;
        scene.rayAngle = 32;
    }

    scene.navigationImpulse = 0;
    scene.navigationTint = 0;

    applySceneVariables();
}

function step(current: number, target: number, easing: number): number {
    return current + (target - current) * easing;
}

function isNear(current: number, target: number, epsilon = SETTLE_EPSILON): boolean {
    return Math.abs(current - target) <= epsilon;
}

function renderFrame(): void {
    const theme = currentTheme();

    if (prefersReducedMotion) {
        setReducedMotionState(theme);
        isAnimating = false;
        frameId = 0;
        return;
    }

    const maxScroll = Math.max(
        document.documentElement.scrollHeight - window.innerHeight,
        window.innerHeight * 1.25,
        1,
    );
    const scrollProgress = clamp(latestScroll / maxScroll, 0, 1);

    const sunTargetX =
        theme === 'sunset'
            ? 78 - scrollProgress * 12
            : 13 + scrollProgress * 34;
    const sunTargetY =
        theme === 'sunset'
            ? 18 + scrollProgress * 36
            : 9 + scrollProgress * 22;
    const focusX = theme === 'sunset' ? 62 : 54;
    const focusY = theme === 'sunset' ? 52 : 34;
    const angleRadians = Math.atan2(focusY - sunTargetY, focusX - sunTargetX);
    const angleDegrees = (angleRadians * 180) / Math.PI;
    const vectorX = Math.cos(angleRadians);
    const vectorY = Math.sin(angleRadians);
    const navigationBoost = scene.navigationImpulse;
    const navigationSweep =
        theme === 'sunset' ? navigationBoost * 2.2 : navigationBoost * 1.4;

    const gridTargetX =
        (theme === 'sunset' ? 1 : -1) * (sunTargetX - 50) * 0.08 +
        vectorX * 1.45 +
        navigationSweep * vectorX;
    const gridTargetY =
        (theme === 'sunset' ? 1 : -0.7) * (sunTargetY - 34) * 0.05 +
        vectorY * 0.95 +
        navigationSweep * vectorY * 0.6;
    const shadowTargetX =
        gridTargetX + vectorX * (theme === 'sunset' ? 4.8 : 5.6 + navigationBoost);
    const shadowTargetY =
        gridTargetY + vectorY * (theme === 'sunset' ? 3.8 : 4.4);
    const rotationTarget = angleDegrees - 92 + navigationBoost * 1.6;
    const shadowScaleTarget =
        (theme === 'sunset' ? 1.16 : 1.12) + scrollProgress * 0.04;
    const gridScaleTarget =
        (theme === 'sunset' ? 1.1 : 1.08) + scrollProgress * 0.03 + navigationBoost * 0.012;
    const sunScaleTarget =
        (theme === 'sunset' ? 1.08 : 1.04) + scrollProgress * 0.06 + navigationBoost * 0.018;
    const sunBlurTarget =
        (theme === 'sunset' ? 136 : 132) + scrollProgress * 26 + navigationBoost * 12;
    const shadowBlurTarget =
        (theme === 'sunset' ? 62 : 48) + scrollProgress * 16 + navigationBoost * 5;
    const sunOpacityTarget =
        (theme === 'sunset' ? 0.38 : 0.68) + scrollProgress * 0.04 + navigationBoost * 0.03;
    const shadowOpacityTarget =
        (theme === 'sunset' ? 0.18 : 0.2) + scrollProgress * 0.06 + navigationBoost * 0.025;
    const navigationTintTarget = navigationBoost * (theme === 'sunset' ? 0.58 : 0.32);

    scene.sunX = step(scene.sunX, sunTargetX, 0.07);
    scene.sunY = step(scene.sunY, sunTargetY, 0.07);
    scene.sunScale = step(scene.sunScale, sunScaleTarget, 0.055);
    scene.sunBlur = step(scene.sunBlur, sunBlurTarget, 0.05);
    scene.sunOpacity = step(scene.sunOpacity, sunOpacityTarget, 0.055);
    scene.gridX = step(scene.gridX, gridTargetX, 0.09);
    scene.gridY = step(scene.gridY, gridTargetY, 0.09);
    scene.gridRotate = step(scene.gridRotate, rotationTarget, 0.085);
    scene.gridScale = step(scene.gridScale, gridScaleTarget, 0.06);
    scene.shadowX = step(scene.shadowX, shadowTargetX, 0.045);
    scene.shadowY = step(scene.shadowY, shadowTargetY, 0.045);
    scene.shadowScale = step(scene.shadowScale, shadowScaleTarget, 0.04);
    scene.shadowBlur = step(scene.shadowBlur, shadowBlurTarget, 0.04);
    scene.shadowOpacity = step(scene.shadowOpacity, shadowOpacityTarget, 0.04);
    scene.rayAngle = step(scene.rayAngle, angleDegrees, 0.055);
    scene.navigationTint = step(scene.navigationTint, navigationTintTarget, 0.08);
    scene.navigationImpulse = step(scene.navigationImpulse, 0, 0.08);

    applySceneVariables();

    const settled =
        isNear(scene.sunX, sunTargetX) &&
        isNear(scene.sunY, sunTargetY) &&
        isNear(scene.sunScale, sunScaleTarget) &&
        isNear(scene.sunBlur, sunBlurTarget, SETTLE_BLUR_EPSILON) &&
        isNear(scene.sunOpacity, sunOpacityTarget) &&
        isNear(scene.gridX, gridTargetX) &&
        isNear(scene.gridY, gridTargetY) &&
        isNear(scene.gridRotate, rotationTarget) &&
        isNear(scene.gridScale, gridScaleTarget) &&
        isNear(scene.shadowX, shadowTargetX) &&
        isNear(scene.shadowY, shadowTargetY) &&
        isNear(scene.shadowScale, shadowScaleTarget) &&
        isNear(scene.shadowBlur, shadowBlurTarget, SETTLE_BLUR_EPSILON) &&
        isNear(scene.shadowOpacity, shadowOpacityTarget) &&
        isNear(scene.rayAngle, angleDegrees) &&
        isNear(scene.navigationImpulse, 0) &&
        isNear(scene.navigationTint, 0);

    if (settled) {
        isAnimating = false;
        frameId = 0;
        return;
    }

    frameId = window.requestAnimationFrame(renderFrame);
}

function startAnimation(): void {
    if (prefersReducedMotion || isAnimating) {
        return;
    }

    isAnimating = true;
    frameId = window.requestAnimationFrame(renderFrame);
}

function handleScrollOrResize(): void {
    updateScrollTarget();
    startAnimation();
}

function handleThemeMutation(): void {
    startAnimation();
}

function handleNavigationStart(): void {
    scene.navigationImpulse = Math.max(scene.navigationImpulse, 1);
    startAnimation();
}

function handleNavigationSettle(): void {
    scene.navigationImpulse = Math.max(scene.navigationImpulse, 0.56);
    startAnimation();
}

onMounted(() => {
    reducedMotionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
    syncMotionPreference();
    updateScrollTarget();
    applySceneVariables();

    reducedMotionQuery.addEventListener('change', syncMotionPreference);
    window.addEventListener('scroll', handleScrollOrResize, { passive: true });
    window.addEventListener('resize', handleScrollOrResize, { passive: true });

    themeObserver = new MutationObserver(handleThemeMutation);
    themeObserver.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['data-theme'],
    });

    window.addEventListener('sidewalk:navigation-start', handleNavigationStart);
    window.addEventListener('sidewalk:navigation-settle', handleNavigationSettle);

    startAnimation();
});

onBeforeUnmount(() => {
    if (frameId) {
        window.cancelAnimationFrame(frameId);
    }

    reducedMotionQuery?.removeEventListener('change', syncMotionPreference);
    themeObserver?.disconnect();
    window.removeEventListener('scroll', handleScrollOrResize);
    window.removeEventListener('resize', handleScrollOrResize);
    window.removeEventListener('sidewalk:navigation-start', handleNavigationStart);
    window.removeEventListener('sidewalk:navigation-settle', handleNavigationSettle);
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
    opacity: var(--sw-ambient-sun-opacity);
    transform: scale(var(--sw-ambient-sun-scale));
    filter: blur(var(--sw-ambient-sun-blur));
    background:
        radial-gradient(
            circle at var(--sw-ambient-sun-x) var(--sw-ambient-sun-y),
            color-mix(in srgb, white 22%, transparent) 0,
            color-mix(in srgb, var(--sw-accent-sun) 34%, transparent) 7%,
            color-mix(in srgb, var(--sw-accent-dominant) 18%, transparent) 18%,
            transparent 36%
        ),
        radial-gradient(
            circle at var(--sw-ambient-sun-x) var(--sw-ambient-sun-y),
            color-mix(in srgb, var(--sw-accent-sun) 16%, transparent),
            transparent 48%
        );
}

.ambient-grid__plane {
    opacity: 0.9;
    transform: translate3d(var(--sw-ambient-grid-x), var(--sw-ambient-grid-y), 0)
        rotate(var(--sw-ambient-grid-rotate)) scale(var(--sw-ambient-grid-scale));
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
            var(--sw-ambient-ray-angle),
            color-mix(in srgb, var(--sw-accent-sun) 10%, transparent),
            transparent 52%
        ),
        linear-gradient(
            90deg,
            transparent 0%,
            color-mix(
                in srgb,
                var(--sw-accent-coral) calc(var(--sw-ambient-navigation-tint, 0) * 24%),
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
    opacity: var(--sw-ambient-shadow-opacity);
    transform: translate3d(var(--sw-ambient-shadow-x), var(--sw-ambient-shadow-y), 0)
        rotate(var(--sw-ambient-grid-rotate)) scale(var(--sw-ambient-shadow-scale));
    filter: blur(var(--sw-ambient-shadow-blur));
    background-image:
        radial-gradient(
            circle at var(--sw-ambient-sun-x) var(--sw-ambient-sun-y),
            color-mix(in srgb, var(--sw-accent-sun) 24%, transparent),
            transparent 24%
        ),
        linear-gradient(
            var(--sw-ambient-ray-angle),
            color-mix(in srgb, var(--sw-accent-dominant) 20%, transparent),
            transparent 78%
        ),
        linear-gradient(
            90deg,
            transparent 0%,
            color-mix(
                in srgb,
                var(--sw-accent-sun) calc(var(--sw-ambient-navigation-tint, 0) * 18%),
                transparent
            ) 38%,
            transparent 74%
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
