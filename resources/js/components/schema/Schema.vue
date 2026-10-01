<script setup lang="ts">
/**
 * Schema: a site plan drawn from data (specs/018-site-refresh).
 *
 * - Lots are square boxes; the lot named in `here` carries the red "you are
 *   here" marker.
 * - Edges are the interfaces between trades: a smooth S-curve whose dashes flow
 *   (`stroke-dashoffset`), with an arrowhead that travels along it and turns
 *   with the curve (SMIL `animateMotion`, `rotate="auto"`).
 * - A lot that has an address or a card is a target in a layer laid over the
 *   drawing (the drawing itself stays a picture). Pointing at it, focusing it
 *   or tapping it opens its card (period, role, figures, links) and lights the
 *   lot with the edges that touch it; the rest of the plan steps back.
 * - A compass rose turns with the scroll (scroll-driven animation); a dimension
 *   line and a graphic scale complete the drawing.
 *
 * Motion is bounded: SMIL is paused when the drawing is out of view and when
 * reduced motion is on (the preference can change while the page is open); the
 * lots and edges come in once, in a short cascade of opacity, when the plan
 * first scrolls into view. CSS motion stops under `prefers-reduced-motion` and
 * `data-motion="reduced"`. The drawing is `aria-hidden`; `describeSchema`
 * provides the same content as an ordered list, read by screen readers and
 * shown when CSS is missing.
 */
import { Link } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref, useId } from 'vue';
import { describeSchema, edgeGeometry, nodeBox } from './schema';
import type { SchemaData, SchemaTone } from './schema';

const props = withDefaults(
    defineProps<{ data: SchemaData; arrow?: string }>(),
    { arrow: '→' },
);

defineOptions({ name: 'SchemaDrawing' });

const svg = ref<SVGSVGElement | null>(null);
const uid = useId();
const open = ref<string | null>(null);
const revealed = ref(false);

const isExternal = (href: string) => /^https?:\/\//.test(href);
const hostOf = (href: string) => new URL(href).host.replace(/^www\./, '');

const boxes = computed(() =>
    props.data.nodes.map((node) => {
        const box = nodeBox(node, props.data.wrap);

        return {
            ...box,
            left: node.x - box.width / 2,
            top: node.y - box.height / 2,
            tone: (node.id === props.data.here
                ? 'here'
                : (node.tone ?? 'ink')) as SchemaTone,
        };
    }),
);
const edges = computed(() => edgeGeometry(props.data));
const nodeCount = computed(() => props.data.nodes.length);
const equivalent = computed(() => describeSchema(props.data, props.arrow));

/** The lots the open one is joined to, by an edge. */
const neighbours = computed(() => {
    const set = new Set<string>();

    if (open.value === null) {
        return set;
    }

    for (const edge of props.data.edges ?? []) {
        if (edge.from === open.value) {
            set.add(edge.to);
        } else if (edge.to === open.value) {
            set.add(edge.from);
        }
    }

    return set;
});

const edgeLit = (from: string, to: string) =>
    open.value !== null && (from === open.value || to === open.value);
const lotState = (id: string) =>
    open.value === null
        ? ''
        : id === open.value
          ? 'is-open'
          : neighbours.value.has(id)
            ? 'is-near'
            : 'is-dim';

/** The layer of targets over the drawing, in percent of the sheet so it scales with it. */
const hits = computed(() =>
    boxes.value
        .filter((box) => box.node.href || box.node.detail)
        .map((box) => {
            const { width, height } = props.data;
            const cardLinks = [
                ...(box.node.detail?.links ?? []),
                ...(box.node.detail && box.node.href
                    ? [
                          {
                              label: isExternal(box.node.href)
                                  ? hostOf(box.node.href)
                                  : box.node.label,
                              href: box.node.href,
                              nofollow: false,
                          },
                      ]
                    : []),
            ];

            return {
                box,
                id: `${uid}-${box.node.id}`,
                style: {
                    left: `${(box.left / width) * 100}%`,
                    top: `${(box.top / height) * 100}%`,
                    width: `${(box.width / width) * 100}%`,
                    height: `${(box.height / height) * 100}%`,
                },
                // Cards stay on the sheet: aligned to the edge a lot is near, and below the lots at the top.
                align:
                    box.node.x / width < 0.2
                        ? 'start'
                        : box.node.x / width > 0.8
                          ? 'end'
                          : 'center',
                below: box.node.y / height < 0.22,
                hasCard: !!box.node.detail,
                // A lot with a card is a button (its own address is a link inside the card); one without is a plain link.
                asLink: !box.node.detail && !!box.node.href,
                cardLinks,
            };
        }),
);

let closing: ReturnType<typeof setTimeout> | undefined;

function show(id: string) {
    clearTimeout(closing);
    open.value = id;
}

function hideSoon() {
    clearTimeout(closing);
    // A short grace, so the pointer can cross from the lot to its card.
    closing = setTimeout(() => (open.value = null), 160);
}

function toggle(id: string) {
    clearTimeout(closing);
    open.value = open.value === id ? null : id;
}

const cleanups: (() => void)[] = [];

onMounted(() => {
    const el = svg.value;

    const onKey = (event: KeyboardEvent) => {
        if (event.key === 'Escape') {
            open.value = null;
        }
    };
    const onOutside = (event: PointerEvent) => {
        if (
            open.value !== null &&
            !(event.target as Element | null)?.closest('.schema__hit')
        ) {
            open.value = null;
        }
    };

    document.addEventListener('keydown', onKey);
    document.addEventListener('pointerdown', onOutside);
    cleanups.push(() => {
        document.removeEventListener('keydown', onKey);
        document.removeEventListener('pointerdown', onOutside);
        clearTimeout(closing);
    });

    if (!el) {
        return;
    }

    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    let visible = true;
    const update = () => {
        if (typeof el.pauseAnimations !== 'function') {
            return;
        }

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
        const observer = new IntersectionObserver(
            ([entry]) => {
                visible = entry?.isIntersecting ?? true;

                if (visible) {
                    revealed.value = true;
                }

                update();
            },
            { threshold: 0.12 },
        );
        observer.observe(el);
        cleanups.push(() => observer.disconnect());
    } else {
        revealed.value = true;
    }
});

onBeforeUnmount(() => cleanups.forEach((fn) => fn()));
</script>

<template>
    <figure
        class="schema"
        :class="{ 'is-revealed': revealed, 'has-open': open !== null }"
    >
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
                :class="[
                    `schema__edge schema__edge--${geometry.edge.tone ?? 'path'}`,
                    { 'is-lit': edgeLit(geometry.edge.from, geometry.edge.to) },
                ]"
                :style="{ '--i': nodeCount + i }"
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
            <g
                v-for="(box, bi) in boxes"
                :key="box.node.id"
                :class="[
                    `schema__lot schema__lot--${box.tone}`,
                    lotState(box.node.id),
                ]"
                :data-lot="box.node.id"
                :style="{ '--i': bi }"
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

        <!-- Targets over the lots: the links, and the cards -->
        <ul class="schema__hits">
            <li
                v-for="hit in hits"
                :key="hit.id"
                class="schema__hit"
                :class="{ 'is-open': open === hit.box.node.id }"
                :style="hit.style"
                @pointerenter="
                    (e) => e.pointerType === 'mouse' && show(hit.box.node.id)
                "
                @pointerleave="(e) => e.pointerType === 'mouse' && hideSoon()"
                @focusin="show(hit.box.node.id)"
                @focusout="hideSoon()"
            >
                <component
                    :is="
                        hit.asLink
                            ? isExternal(hit.box.node.href ?? '')
                                ? 'a'
                                : Link
                            : 'button'
                    "
                    v-bind="
                        hit.asLink
                            ? isExternal(hit.box.node.href ?? '')
                                ? {
                                      href: hit.box.node.href,
                                      target: '_blank',
                                      rel: 'noopener',
                                  }
                                : { href: hit.box.node.href }
                            : {
                                  type: 'button',
                                  'aria-expanded': open === hit.box.node.id,
                                  'aria-controls': hit.hasCard
                                      ? hit.id
                                      : undefined,
                              }
                    "
                    class="schema__target"
                    @click="!hit.asLink && toggle(hit.box.node.id)"
                >
                    <span class="schema__sr">{{
                        hit.box.lines.join(' ')
                    }}</span>
                </component>
                <div
                    v-if="hit.hasCard && hit.box.node.detail"
                    :id="hit.id"
                    class="schema__card"
                    :class="[
                        `schema__card--${hit.align}`,
                        { 'schema__card--below': hit.below },
                    ]"
                    @pointerenter="show(hit.box.node.id)"
                    @pointerleave="hideSoon()"
                >
                    <div class="schema__card-body">
                        <p class="schema__card-title">
                            {{ hit.box.node.label.replace(/\s*\n\s*/g, ' ')
                            }}<span
                                v-if="hit.box.node.detail.period"
                                class="schema__card-period"
                                >{{ hit.box.node.detail.period }}</span
                            >
                        </p>
                        <p
                            v-if="hit.box.node.detail.role"
                            class="schema__card-role"
                        >
                            {{ hit.box.node.detail.role }}
                        </p>
                        <ul
                            v-if="hit.box.node.detail.figures?.length"
                            class="schema__figures"
                        >
                            <li
                                v-for="figure in hit.box.node.detail.figures"
                                :key="figure.label"
                            >
                                <strong>{{ figure.value }}</strong>
                                <span>{{ figure.label }}</span>
                            </li>
                        </ul>
                        <ul v-if="hit.cardLinks.length" class="schema__links">
                            <li v-for="link in hit.cardLinks" :key="link.href">
                                <component
                                    :is="isExternal(link.href) ? 'a' : Link"
                                    :href="link.href"
                                    v-bind="
                                        isExternal(link.href)
                                            ? {
                                                  target: '_blank',
                                                  rel: link.nofollow
                                                      ? 'nofollow noopener noreferrer'
                                                      : 'noopener',
                                              }
                                            : {}
                                    "
                                    >{{ link.label
                                    }}<span
                                        v-if="isExternal(link.href)"
                                        aria-hidden="true"
                                    >
                                        ↗</span
                                    ></component
                                >
                            </li>
                        </ul>
                    </div>
                </div>
            </li>
        </ul>

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
    position: relative;
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

/* Pointing at a lot: it and what touches it stay lit, the rest of the plan steps back */
.schema__lot,
.schema__edge {
    transition: opacity 0.15s linear;
}

.has-open .schema__lot.is-dim {
    opacity: 0.3;
}

.schema__lot.is-open rect,
.schema__lot.is-near rect {
    stroke: var(--sw-path);
    stroke-width: var(--sw-hairline-strong);
}

.has-open .schema__edge:not(.is-lit) {
    opacity: 0.14;
}

.schema__edge.is-lit .schema__line {
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

/* The first time the plan scrolls into view: the lots, then the edges, come in one after the other (opacity only) */
@keyframes schema-in {
    from {
        opacity: 0;
    }
}

.is-revealed .schema__lot,
.is-revealed .schema__edge {
    animation: schema-in 0.4s linear backwards;
    animation-delay: calc(var(--i, 0) * 28ms);
}

/* The layer of targets over the drawing */
.schema__hits {
    position: absolute;
    inset: 0;
    margin: 0;
    padding: 0;
    list-style: none;
    pointer-events: none;
}

.schema__hit {
    position: absolute;
    pointer-events: auto;
}

.schema__target {
    position: absolute;
    inset: 0;
    display: block;
    width: 100%;
    height: 100%;
    margin: 0;
    padding: 0;
    border: 0;
    background: transparent;
    cursor: pointer;
}

.schema__target:focus-visible {
    outline: var(--sw-hairline-strong) solid var(--sw-path);
    outline-offset: 3px;
}

.schema__sr {
    position: absolute;
    width: 1px;
    height: 1px;
    overflow: hidden;
    clip-path: inset(50%);
    white-space: nowrap;
}

/* The card of a lot: above it (below, for the lots at the top of the sheet), aligned to the edge a lot is near */
.schema__card {
    position: absolute;
    bottom: 100%;
    z-index: 5;
    width: 17rem;
    max-width: 78vw;
    padding-bottom: 8px;
    visibility: hidden;
    opacity: 0;
    text-transform: none;
    letter-spacing: 0;
    transition:
        opacity 0.12s linear,
        visibility 0s linear 0.12s;
}

.schema__card--center {
    left: 50%;
    translate: -50% 0;
}

.schema__card--start {
    left: 0;
}

.schema__card--end {
    right: 0;
}

.schema__card--below {
    top: 100%;
    bottom: auto;
    padding-top: 8px;
    padding-bottom: 0;
}

.schema__hit.is-open .schema__card {
    visibility: visible;
    opacity: 1;
    transition-delay: 0s;
}

.schema__hit.is-open {
    z-index: 6;
}

.schema__card-body {
    display: grid;
    gap: 0.5rem;
    padding: 0.7rem 0.8rem 0.8rem;
    border: var(--sw-hairline-strong) solid var(--sw-text-primary);
    background: var(--sw-bg-surface);
    color: var(--sw-text-primary);
    font-family: var(--sw-font-body);
    font-size: 0.8125rem;
    line-height: 1.4;
}

.schema__card-body p,
.schema__card-body ul {
    margin: 0;
}

.schema__card-title {
    display: flex;
    flex-wrap: wrap;
    align-items: baseline;
    justify-content: space-between;
    gap: 0 0.75rem;
    font-weight: 600;
}

.schema__card-period {
    font-family: var(--sw-font-code);
    font-size: 0.6875rem;
    font-weight: 400;
    letter-spacing: 0.06em;
    color: var(--sw-text-muted);
}

.schema__card-role {
    color: var(--sw-text-secondary);
}

.schema__figures {
    display: flex;
    flex-wrap: wrap;
    gap: 0.25rem 1.25rem;
    padding: 0;
    list-style: none;
}

.schema__figures li {
    display: grid;
}

.schema__figures strong {
    font-family: var(--sw-font-code);
    font-size: 1.5rem;
    font-weight: 500;
    line-height: 1.1;
    letter-spacing: -0.02em;
}

.schema__figures span {
    font-family: var(--sw-font-code);
    font-size: 0.625rem;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--sw-text-muted);
}

.schema__links {
    display: flex;
    flex-wrap: wrap;
    gap: 0.15rem 1rem;
    padding: 0;
    list-style: none;
}

.schema__links a {
    color: var(--sw-path);
    text-underline-offset: 2px;
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
    .schema__compass-rose,
    .is-revealed .schema__lot,
    .is-revealed .schema__edge {
        animation: none;
    }

    .schema__lot,
    .schema__edge,
    .schema__card {
        transition: none;
    }
}

:global(html[data-motion='reduced']) .schema__line,
:global(html[data-motion='reduced']) .schema__compass-rose,
:global(html[data-motion='reduced']) .is-revealed .schema__lot,
:global(html[data-motion='reduced']) .is-revealed .schema__edge {
    animation: none;
}
</style>
