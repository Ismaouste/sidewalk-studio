<script setup lang="ts">
/**
 * The drawing sheet's frame (specs/018-site-refresh): registration crosses in
 * the four corners and a graduated ruler along the left edge, with a red mark
 * that follows the scroll and reads its position. Mounted once at the root of
 * the app, beside the Stage. Purely decorative (`aria-hidden`, no pointer
 * events), flat, and static under reduced motion: the mark then jumps instead
 * of gliding.
 */
import { onBeforeUnmount, onMounted, ref } from 'vue';

defineOptions({ name: 'SheetFrame' });

const ruler = ref<HTMLElement | null>(null);
const reading = ref('0000');
let frame = 0;

function update(): void {
    frame = 0;
    const doc = document.documentElement;
    const span = Math.max(1, doc.scrollHeight - window.innerHeight);
    const progress = Math.min(1, Math.max(0, window.scrollY / span));

    ruler.value?.style.setProperty('--sheet-p', progress.toFixed(4));
    reading.value = String(Math.round(window.scrollY)).padStart(4, '0');
}

function onScroll(): void {
    if (frame === 0) {
        frame = requestAnimationFrame(update);
    }
}

onMounted(() => {
    update();
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll, { passive: true });
});

onBeforeUnmount(() => {
    cancelAnimationFrame(frame);
    window.removeEventListener('scroll', onScroll);
    window.removeEventListener('resize', onScroll);
});
</script>

<template>
    <div class="sheet" aria-hidden="true">
        <svg
            v-for="corner in ['tl', 'tr', 'bl', 'br']"
            :key="corner"
            class="sheet__mark"
            :class="`sheet__mark--${corner}`"
            viewBox="0 0 16 16"
            focusable="false"
        >
            <path d="M0 8H16M8 0V16" />
            <circle cx="8" cy="8" r="3.5" />
        </svg>

        <div ref="ruler" class="sheet__ruler">
            <svg class="sheet__ticks" focusable="false">
                <defs>
                    <pattern
                        id="sheet-ticks"
                        width="12"
                        height="20"
                        patternUnits="userSpaceOnUse"
                    >
                        <path d="M0 0.5H7M0 10.5H3" />
                    </pattern>
                </defs>
                <rect width="12" height="100%" fill="url(#sheet-ticks)" />
            </svg>
            <span class="sheet__here">
                <i class="sheet__here-line" />
                <span class="sheet__here-text">Y {{ reading }}</span>
            </span>
        </div>
    </div>
</template>

<style scoped>
.sheet {
    position: fixed;
    inset: 0;
    z-index: var(--sw-z-overlay);
    pointer-events: none;
}

.sheet__mark {
    position: absolute;
    width: 14px;
    height: 14px;
    fill: none;
    stroke: var(--sw-text-muted);
    stroke-width: 1;
    opacity: 0.7;
}

.sheet__mark--tl {
    top: 6px;
    left: 6px;
}

.sheet__mark--tr {
    top: 6px;
    right: 6px;
}

.sheet__mark--bl {
    bottom: 6px;
    left: 6px;
}

.sheet__mark--br {
    right: 6px;
    bottom: 6px;
}

.sheet__ruler {
    --sheet-p: 0;
    position: absolute;
    top: 28px;
    bottom: 28px;
    left: 6px;
    width: 12px;
}

.sheet__ticks {
    width: 12px;
    height: 100%;
    stroke: var(--sw-text-muted);
    stroke-width: 1;
    opacity: 0.45;
}

.sheet__here {
    position: absolute;
    left: 0;
    top: calc(var(--sheet-p) * (100% - 1px));
    display: flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
    color: var(--sw-here);
    font-family: var(--sw-font-code);
    font-size: 9px;
    letter-spacing: 0.08em;
    transition: top 90ms linear;
}

.sheet__here-line {
    display: block;
    width: 12px;
    height: 1px;
    background: var(--sw-here);
}

.sheet__here-text {
    display: none;
    padding: 1px 4px;
    background: var(--sw-bg-base);
}

@media (min-width: 1500px) {
    .sheet__here-text {
        display: inline;
    }
}

@media (max-width: 640px) {
    .sheet__ruler {
        width: 8px;
    }

    .sheet__ticks {
        opacity: 0.3;
    }
}

@media (prefers-reduced-motion: reduce) {
    .sheet__here {
        transition: none;
    }
}

:global(html[data-motion='reduced']) .sheet__here {
    transition: none;
}
</style>
