<script setup lang="ts">
/**
 * The Stage: the slow background of the site plan (specs/018-site-refresh).
 * Flat organic stains drift behind a canvas of specks. It is mounted ONCE, at
 * the root of the app (resources/js/app.ts), beside Inertia's page: pages wrap
 * themselves in `<SiteLayout>`, so anything inside the layout would restart at
 * every link. Here a fast navigation does not restart or stack anything.
 *
 * Flat by rule: stains are plain SVG fills blended with `multiply` on paper
 * and `screen` on the night theme; no blur, no gradient. The drift is CSS
 * `transform` only (compositor), 60 to 120 seconds a leg, and stops under
 * reduced motion.
 */
import { onMounted, ref } from 'vue';
import { readStorage, writeStorage } from '@/lib/safeStorage';
import { layoutStains } from './stains';
import type { Stain } from './stains';
import { useStage } from './useStage';

defineOptions({ name: 'StageBackground' });

const SEED_KEY = 'sidewalk-stage-seed';
const canvas = ref<HTMLCanvasElement | null>(null);
const stains = ref<Stain[]>([]);

useStage(canvas);

onMounted(() => {
    // One arrangement for the whole session; storage may be blocked, then it is random.
    const stored = Number.parseInt(readStorage('session', SEED_KEY) ?? '', 10);
    const seed = Number.isFinite(stored)
        ? stored
        : Math.floor(Math.random() * 2 ** 31);
    writeStorage('session', SEED_KEY, String(seed));
    stains.value = layoutStains(seed);
});

function styleOf(stain: Stain): Record<string, string> {
    return {
        left: `${stain.x}vw`,
        top: `${stain.y}vh`,
        width: `${stain.size}vmax`,
        height: `${stain.size}vmax`,
        '--dx': `${stain.dx}vw`,
        '--dy': `${stain.dy}vh`,
        '--rot': `${stain.rot}deg`,
        '--dur': `${stain.duration}s`,
        '--delay': `${stain.delay}s`,
    };
}
</script>

<template>
    <div class="stage" aria-hidden="true">
        <svg
            v-for="(stain, index) in stains"
            :key="index"
            class="stage__stain"
            :class="`stage__stain--${stain.tone}`"
            :style="styleOf(stain)"
            viewBox="0 0 100 100"
            focusable="false"
        >
            <path :d="stain.path" />
        </svg>
        <canvas ref="canvas" class="stage__canvas" />
    </div>
</template>

<style scoped>
.stage {
    position: fixed;
    inset: 0;
    z-index: var(--sw-z-base);
    overflow: hidden;
    pointer-events: none;
    contain: strict;
}

.stage__canvas {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
}

.stage__stain {
    position: absolute;
    mix-blend-mode: var(--sw-stain-blend);
    will-change: transform;
    animation: stage-drift var(--dur) ease-in-out var(--delay) infinite
        alternate;
}

.stage__stain path {
    fill: var(--sw-stain-grey);
    opacity: var(--sw-stain-grey-opacity);
}

.stage__stain--mark path {
    fill: var(--sw-mark);
    opacity: var(--sw-stain-mark-opacity);
}

.stage__stain--path path {
    fill: var(--sw-path);
    opacity: var(--sw-stain-path-opacity);
}

.stage__stain--here path {
    fill: var(--sw-here);
    opacity: var(--sw-stain-here-opacity);
}

@keyframes stage-drift {
    from {
        transform: translate3d(0, 0, 0) rotate(0deg);
    }

    to {
        transform: translate3d(var(--dx), var(--dy), 0) rotate(var(--rot));
    }
}

@media (prefers-reduced-motion: reduce) {
    .stage__stain {
        animation: none;
        will-change: auto;
    }
}

:global(html[data-motion='reduced']) .stage__stain {
    animation: none;
    will-change: auto;
}
</style>
