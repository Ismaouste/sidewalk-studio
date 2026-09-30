<script setup lang="ts">
/**
 * Schema: a site plan drawn from data (specs/018-site-refresh).
 *
 * - Lots are square boxes; a lot with an address is a link; the lot named in
 *   `here` carries the red "you are here" marker.
 * - Edges are the interfaces between trades: a dashed line whose dashes flow
 *   (`stroke-dashoffset`), with an arrowhead that travels along it and turns
 *   with the curve (SMIL `animateMotion`, `rotate="auto"`).
 * - A compass rose turns with the scroll (scroll-driven animation); a dimension
 *   line and a graphic scale complete the drawing.
 *
 * Motion is bounded: SMIL is paused when the drawing is out of view and when
 * reduced motion is on (the preference can change while the page is open);
 * CSS motion stops under `prefers-reduced-motion` and `data-motion="reduced"`.
 * The drawing is `aria-hidden`; `describeSchema` provides the same content as
 * an ordered list, read by screen readers and shown when CSS is missing.
 */
import { Link } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { describeSchema, edgeGeometry, nodeBox } from './schema';
import type { SchemaData, SchemaTone } from './schema';

const props = withDefaults(
    defineProps<{ data: SchemaData; arrow?: string }>(),
    { arrow: '→' },
);

defineOptions({ name: 'SchemaDrawing' });

const svg = ref<SVGSVGElement | null>(null);

const boxes = computed(() =>
    props.data.nodes.map((node) => {
        const box = nodeBox(node);

        return {
            ...box,
            left: node.x - box.width / 2,
            top: node.y - box.height / 2,
            tone: (node.id === props.data.here
                ? 'here'
                : (node.tone ?? 'ink')) as SchemaTone,
            external: !!node.href && /^https?:\/\//.test(node.href),
        };
    }),
);
const edges = computed(() => edgeGeometry(props.data));
const equivalent = computed(() => describeSchema(props.data, props.arrow));

const cleanups: (() => void)[] = [];

onMounted(() => {
    const el = svg.value;

    if (!el || typeof el.pauseAnimations !== 'function') {
        return;
    }

    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    let visible = true;
    const update = () => {
        if (
            reduced.matches ||
            !visible ||
            document.documentElement.getAttribute('data-motion') === 'reduced'
        ) {
            el.pauseAnimations();
        } else {
            el.unpauseAnimations();
        }
    };

    update();
    reduced.addEventListener('change', update);
    cleanups.push(() => reduced.removeEventListener('change', update));

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(([entry]) => {
            visible = entry?.isIntersecting ?? true;
            update();
        });
        observer.observe(el);
        cleanups.push(() => observer.disconnect());
    }
});

onBeforeUnmount(() => cleanups.forEach((fn) => fn()));
</script>

<template>
    <figure class="schema">
        <svg
            ref="svg"
            class="schema__drawing"
            :viewBox="`0 0 ${data.width} ${data.height}`"
            aria-hidden="true"
            focusable="false"
        >
            <!-- Interfaces between the lots -->
            <g
                v-for="(geometry, i) in edges"
                :key="`e${i}`"
                :class="`schema__edge schema__edge--${geometry.edge.tone ?? 'path'}`"
            >
                <path class="schema__line" :d="geometry.d" />
                <path class="schema__arrow" d="M-6 -4L3 0L-6 4Z">
                    <animateMotion
                        :path="geometry.d"
                        dur="5s"
                        repeatCount="indefinite"
                        rotate="auto"
                        :begin="`${(i % 4) * -1.1}s`"
                    />
                </path>
                <text
                    v-if="geometry.edge.label"
                    class="schema__edge-label"
                    :x="geometry.mid.x"
                    :y="geometry.mid.y - 6"
                    text-anchor="middle"
                >
                    {{ geometry.edge.label }}
                </text>
            </g>

            <!-- Dimension lines -->
            <g
                v-for="(dimension, i) in data.dimensions ?? []"
                :key="`d${i}`"
                class="schema__dimension"
            >
                <line
                    :x1="dimension.from[0]"
                    :y1="dimension.from[1]"
                    :x2="dimension.to[0]"
                    :y2="dimension.to[1]"
                />
                <line
                    :x1="dimension.from[0]"
                    :y1="dimension.from[1] - 5"
                    :x2="dimension.from[0]"
                    :y2="dimension.from[1] + 5"
                />
                <line
                    :x1="dimension.to[0]"
                    :y1="dimension.to[1] - 5"
                    :x2="dimension.to[0]"
                    :y2="dimension.to[1] + 5"
                />
                <text
                    :x="(dimension.from[0] + dimension.to[0]) / 2"
                    :y="(dimension.from[1] + dimension.to[1]) / 2 - 6"
                    text-anchor="middle"
                >
                    {{ dimension.label }}
                </text>
            </g>

            <!-- The lots -->
            <component
                :is="box.node.href ? (box.external ? 'a' : Link) : 'g'"
                v-for="box in boxes"
                :key="box.node.id"
                v-bind="
                    box.node.href
                        ? box.external
                            ? {
                                  href: box.node.href,
                                  target: '_blank',
                                  rel: 'noopener',
                              }
                            : { href: box.node.href }
                        : {}
                "
            >
                <g
                    :class="`schema__lot schema__lot--${box.tone}`"
                    :data-lot="box.node.id"
                >
                    <rect
                        :x="box.left"
                        :y="box.top"
                        :width="box.width"
                        :height="box.height"
                    />
                    <text
                        v-for="(line, li) in box.lines"
                        :key="li"
                        :x="box.node.x"
                        :y="box.top + 10 + 16 * (li + 1) - 4"
                        text-anchor="middle"
                    >
                        {{ line }}
                    </text>
                    <circle
                        v-if="box.tone === 'here'"
                        class="schema__here"
                        :cx="box.left + 8"
                        :cy="box.top + 8"
                        r="3.4"
                    />
                </g>
            </component>

            <!-- Compass rose: turns with the scroll -->
            <g
                v-if="data.compass"
                class="schema__compass"
                :transform="`translate(${data.compass.x} ${data.compass.y})`"
            >
                <g class="schema__compass-rose">
                    <circle r="20" />
                    <path d="M0 -22L4 0L0 22L-4 0Z" />
                    <path
                        d="M-22 0L0 -4L22 0L0 4Z"
                        class="schema__compass-minor"
                    />
                </g>
                <text y="-28" text-anchor="middle">N</text>
            </g>

            <!-- Graphic scale -->
            <g
                v-if="data.scale"
                class="schema__scale"
                :transform="`translate(${data.scale.x} ${data.scale.y})`"
            >
                <rect x="0" y="0" width="30" height="5" />
                <rect
                    x="30"
                    y="0"
                    width="30"
                    height="5"
                    class="schema__scale-fill"
                />
                <rect x="60" y="0" width="30" height="5" />
                <rect
                    x="90"
                    y="0"
                    width="30"
                    height="5"
                    class="schema__scale-fill"
                />
                <text x="0" y="18">{{ data.scale.label }}</text>
            </g>
        </svg>

        <figcaption class="schema__text">
            <span class="schema__title">{{ data.title }}</span>
            <ol>
                <li v-for="(line, i) in equivalent" :key="i">{{ line }}</li>
            </ol>
        </figcaption>
    </figure>
</template>

<style scoped>
.schema {
    margin: 0;
}

.schema__drawing {
    display: block;
    width: 100%;
    height: auto;
    overflow: visible;
    font-family: var(--sw-font-code);
    font-size: 11px;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}

/* Lots */
.schema__lot rect {
    fill: var(--sw-bg-surface);
    stroke: var(--sw-text-primary);
    stroke-width: var(--sw-hairline);
}

.schema__lot text {
    fill: var(--sw-text-primary);
}

.schema__lot--mark rect {
    fill: var(--sw-mark);
    stroke: var(--sw-ink);
}

.schema__lot--mark text {
    fill: var(--sw-ink);
}

.schema__lot--path rect {
    stroke: var(--sw-path);
    stroke-width: var(--sw-hairline-strong);
}

.schema__lot--here rect {
    stroke: var(--sw-here);
    stroke-width: var(--sw-hairline-strong);
}

.schema__here {
    fill: var(--sw-here);
}

a .schema__lot,
a:any-link .schema__lot {
    cursor: pointer;
}

a:hover .schema__lot rect,
a:focus-visible .schema__lot rect {
    stroke: var(--sw-path);
    stroke-width: var(--sw-hairline-strong);
}

/* Interfaces */
.schema__line {
    fill: none;
    stroke: var(--sw-path);
    stroke-width: var(--sw-hairline);
    stroke-dasharray: 5 5;
    animation: schema-flow var(--sw-motion-flow) linear infinite;
}

.schema__edge--ink .schema__line {
    stroke: var(--sw-text-primary);
}

.schema__edge--here .schema__line {
    stroke: var(--sw-here);
}

.schema__arrow {
    fill: var(--sw-path);
}

.schema__edge--ink .schema__arrow {
    fill: var(--sw-text-primary);
}

.schema__edge--here .schema__arrow {
    fill: var(--sw-here);
}

.schema__edge-label {
    fill: var(--sw-text-muted);
    font-size: 9px;
}

@keyframes schema-flow {
    to {
        stroke-dashoffset: -20;
    }
}

/* Dimension lines and scale */
.schema__dimension line {
    stroke: var(--sw-path);
    stroke-width: var(--sw-hairline);
}

.schema__dimension text,
.schema__scale text {
    fill: var(--sw-text-muted);
    font-size: 9px;
}

.schema__scale rect {
    fill: none;
    stroke: var(--sw-text-primary);
    stroke-width: var(--sw-hairline);
}

.schema__scale-fill {
    fill: var(--sw-text-primary) !important;
}

/* Compass: turns with the scroll where the browser can, slowly by itself otherwise */
.schema__compass circle,
.schema__compass path {
    fill: none;
    stroke: var(--sw-text-primary);
    stroke-width: var(--sw-hairline);
}

.schema__compass-minor {
    stroke: var(--sw-text-muted);
}

.schema__compass text {
    fill: var(--sw-here);
}

.schema__compass-rose {
    transform-box: fill-box;
    transform-origin: center;
    animation: schema-turn 90s linear infinite;
}

@supports (animation-timeline: scroll()) {
    .schema__compass-rose {
        animation: schema-turn linear both;
        animation-timeline: scroll(root block);
    }
}

@keyframes schema-turn {
    to {
        transform: rotate(360deg);
    }
}

/* The text equivalent: read by screen readers; visible only without CSS. */
.schema__text {
    position: absolute;
    width: 1px;
    height: 1px;
    margin: -1px;
    padding: 0;
    overflow: hidden;
    clip-path: inset(50%);
    white-space: nowrap;
}

@media (prefers-reduced-motion: reduce) {
    .schema__line,
    .schema__compass-rose {
        animation: none;
    }
}

:global(html[data-motion='reduced']) .schema__line,
:global(html[data-motion='reduced']) .schema__compass-rose {
    animation: none;
}
</style>
