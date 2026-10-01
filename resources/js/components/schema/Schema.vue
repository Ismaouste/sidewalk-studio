<script setup lang="ts">
/**
 * Schema: a drawing from data (specs/018-site-refresh), used for the timeline
 * on the home page.
 *
 * - Lots are boxes. What a lot *is* (`kind`) decides how it is drawn: a page of
 *   the site in ink, an external address in blue with an arrow, a notion or a
 *   technology dashed, and the three kinds of publication each with a
 *   pictogram on a yellow strip. Every publication is an internal link.
 * - Edges are the interfaces between lots: a smooth S-curve whose dashes flow
 *   (`stroke-dashoffset`), with an arrowhead that travels along it and turns
 *   with the curve (SMIL `animateMotion`, `rotate="auto"`).
 * - A time axis, period bars and leader lines put a lot on a scale; column
 *   headings name what each column holds.
 * - A lot that has an address or a card is a target in a layer laid over the
 *   drawing (the drawing itself stays a picture). Pointing at it, focusing it
 *   or tapping it opens its card and lights the lot with what it touches (its
 *   edges, its related lots, its bar); the rest of the drawing steps back.
 *
 * Motion is bounded: SMIL is paused when the drawing is out of view and when
 * reduced motion is on (the preference can change while the page is open); the
 * lots and edges come in once, in a short cascade of opacity, when the drawing
 * first scrolls into view. CSS motion stops under `prefers-reduced-motion` and
 * `data-motion="reduced"`. The drawing is `aria-hidden`; `describeSchema`
 * provides the same content as an ordered list, read by screen readers and
 * shown when CSS is missing.
 */
import { Link } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref, useId } from 'vue';
import {
    describeSchema,
    edgeGeometry,
    KIND_ICONS as ICONS,
    leaderPath,
    nodeBox,
} from './schema';
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
            kind: node.kind ?? 'page',
            tone: (node.id === props.data.here
                ? 'here'
                : (node.tone ?? 'ink')) as SchemaTone,
            icon: box.icon,
            iconPath: node.kind ? ICONS[node.kind] : undefined,
        };
    }),
);
const boxById = computed(
    () => new Map(boxes.value.map((box) => [box.node.id, box])),
);
const edges = computed(() => edgeGeometry(props.data));
const nodeCount = computed(() => props.data.nodes.length);

const bars = computed(() => props.data.bars ?? []);
const barById = computed(() => new Map(bars.value.map((bar) => [bar.id, bar])));
const leaders = computed(() =>
    (props.data.leaders ?? []).flatMap((leader) => {
        const bar = barById.value.get(leader.bar);
        const box = boxById.value.get(leader.node);

        if (!bar || !box) {
            return [];
        }

        return [
            {
                ...leader,
                d: leaderPath(
                    { x: bar.x + bar.w, y: (bar.y1 + bar.y2) / 2 },
                    box,
                ),
            },
        ];
    }),
);
const axisBounds = computed(() => {
    const ticks = props.data.axis?.ticks ?? [];

    return ticks.length > 0
        ? {
              top: Math.min(...ticks.map((t) => t.y)),
              bottom: Math.max(...ticks.map((t) => t.y)),
          }
        : undefined;
});
const equivalent = computed(() => describeSchema(props.data, props.arrow));

/** The lots the open one is joined to: by a drawn edge, or by a relation worth lighting without a line. */
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

    for (const [a, b] of props.data.relations ?? []) {
        if (a === open.value) {
            set.add(b);
        } else if (b === open.value) {
            set.add(a);
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
const place = (left: number, top: number, w: number, h: number) => ({
    left: `${(left / props.data.width) * 100}%`,
    top: `${(top / props.data.height) * 100}%`,
    width: `${(w / props.data.width) * 100}%`,
    height: `${(h / props.data.height) * 100}%`,
});

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
                                  : box.node.label.replace(/\s*\n\s*/g, ' '),
                              href: box.node.href,
                              nofollow: !!box.node.nofollow,
                          },
                      ]
                    : []),
            ];

            return {
                box,
                id: `${uid}-${box.node.id}`,
                style: place(box.left, box.top, box.width, box.height),
                // Cards stay on the sheet: aligned to the edge a lot is near, and below the lots at the top.
                align:
                    box.node.x / width < 0.2
                        ? 'start'
                        : box.node.x / width > 0.8
                          ? 'end'
                          : 'center',
                below: box.node.y / height < 0.14,
                hasCard: !!box.node.detail,
                // A lot with a card is a button (its own address is a link inside the card); one without is a plain link.
                asLink: !box.node.detail && !!box.node.href,
                cardLinks,
            };
        }),
);

const barHits = computed(() =>
    bars.value
        .filter((bar) => bar.node)
        .map((bar) => ({
            bar,
            style: place(
                bar.x - 3,
                bar.y1,
                bar.w + 6,
                Math.max(10, bar.y2 - bar.y1),
            ),
        })),
);

let closing: ReturnType<typeof setTimeout> | undefined;

/** Cards that would run under the sticky header open below their lot instead. */
const flip = ref<Record<string, boolean>>({});

function placeCard(id: string, el: Element | null) {
    if (el) {
        flip.value[id] = el.getBoundingClientRect().top < 360;
    }
}

function show(id: string, event?: Event) {
    clearTimeout(closing);

    if (event?.currentTarget instanceof HTMLElement) {
        placeCard(id, event.currentTarget);
    }

    open.value = id;
}

function hideSoon() {
    clearTimeout(closing);
    // A short grace, so the pointer can cross from the lot to its card.
    closing = setTimeout(() => (open.value = null), 160);
}

function toggle(id: string, event?: Event) {
    clearTimeout(closing);

    if (event?.currentTarget instanceof HTMLElement) {
        placeCard(id, event.currentTarget.closest('.schema__hit'));
    }

    open.value = open.value === id ? null : id;
}

/** A lot with a card is a button: tapping it opens or closes the card. One that is only a link just goes. */
function onTarget(id: string, asLink: boolean, event: Event) {
    if (!asLink) {
        toggle(id, event);
    }
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
            { threshold: 0.08 },
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
            <!-- Column headings -->
            <text
                v-for="column in data.columns ?? []"
                :key="column.label"
                class="schema__column"
                :x="column.x"
                :y="column.y"
                :text-anchor="column.anchor ?? 'middle'"
            >
                {{ column.label }}
            </text>

            <!-- The time axis -->
            <g v-if="data.axis && axisBounds" class="schema__axis">
                <line
                    :x1="data.axis.x"
                    :y1="axisBounds.top"
                    :x2="data.axis.x"
                    :y2="axisBounds.bottom"
                />
                <g
                    v-for="tick in data.axis.ticks"
                    :key="`${tick.y}-${tick.label}`"
                >
                    <line
                        :x1="data.axis.x - (tick.major ? 5 : 3)"
                        :y1="tick.y"
                        :x2="data.axis.x"
                        :y2="tick.y"
                    />
                    <text
                        v-if="tick.label"
                        :x="data.axis.x - 9"
                        :y="tick.y + 3.5"
                        text-anchor="end"
                    >
                        {{ tick.label }}
                    </text>
                </g>
                <!-- Where the scale changes: the years above are drawn tighter -->
                <g v-if="data.axis.broken" class="schema__break">
                    <path
                        :d="`M${data.axis.x - 7} ${data.axis.broken - 2}l7 -3l7 3M${data.axis.x - 7} ${data.axis.broken + 3}l7 -3l7 3`"
                    />
                </g>
            </g>

            <!-- Period bars and their leaders -->
            <g
                v-for="bar in bars"
                :key="bar.id"
                class="schema__bar"
                :class="[
                    `schema__bar--${bar.tone ?? 'ink'}`,
                    { 'is-lit': bar.node && open === bar.node },
                ]"
            >
                <rect
                    :x="bar.x"
                    :y="bar.y1"
                    :width="bar.w"
                    :height="Math.max(3, bar.y2 - bar.y1)"
                />
            </g>
            <path
                v-for="leader in leaders"
                :key="`l-${leader.bar}`"
                class="schema__leader"
                :class="{ 'is-lit': open === leader.node }"
                :d="leader.d"
            />

            <!-- Interfaces between the lots -->
            <g
                v-for="(geometry, i) in edges"
                :key="`e${i}`"
                :class="[
                    `schema__edge schema__edge--${geometry.edge.tone ?? 'path'}`,
                    {
                        'is-lit': edgeLit(geometry.edge.from, geometry.edge.to),
                        'is-faint': geometry.edge.faint,
                    },
                ]"
                :style="{ '--i': nodeCount + i }"
            >
                <path class="schema__line" :d="geometry.d" />
                <path
                    v-if="!geometry.edge.faint"
                    class="schema__arrow"
                    d="M-6 -4L3 0L-6 4Z"
                >
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

            <!-- The lots -->
            <g
                v-for="(box, bi) in boxes"
                :key="box.node.id"
                :class="[
                    `schema__lot schema__lot--${box.tone} schema__lot--${box.kind}`,
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
                <template v-if="box.iconPath">
                    <rect
                        v-if="box.kind !== 'external'"
                        class="schema__strip"
                        :x="box.left"
                        :y="box.top"
                        :width="box.icon"
                        :height="box.height"
                    />
                    <path
                        class="schema__icon"
                        :d="box.iconPath"
                        :transform="`translate(${box.left + (box.icon - 12) / 2} ${box.node.y - 6})`"
                    />
                </template>
                <text
                    v-for="(line, li) in box.lines"
                    :key="li"
                    :x="box.node.x + box.icon / 2"
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

        <!-- Bars: pointing at a period lights its lot -->
        <ul class="schema__bar-hits" aria-hidden="true">
            <li
                v-for="hit in barHits"
                :key="hit.bar.id"
                class="schema__bar-hit"
                :style="hit.style"
                @pointerenter="
                    (e) => e.pointerType === 'mouse' && show(hit.bar.node!)
                "
                @pointerleave="(e) => e.pointerType === 'mouse' && hideSoon()"
            />
        </ul>

        <!-- Targets over the lots: the links, and the cards -->
        <ul class="schema__hits">
            <li
                v-for="hit in hits"
                :key="hit.id"
                class="schema__hit"
                :class="{ 'is-open': open === hit.box.node.id }"
                :style="hit.style"
                @pointerenter="
                    (e) => e.pointerType === 'mouse' && show(hit.box.node.id, e)
                "
                @pointerleave="(e) => e.pointerType === 'mouse' && hideSoon()"
                @focusin="(e) => show(hit.box.node.id, e)"
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
                                      rel: hit.box.node.nofollow
                                          ? 'nofollow noopener noreferrer'
                                          : 'noopener',
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
                    @click="onTarget(hit.box.node.id, hit.asLink, $event)"
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
                        {
                            'schema__card--below':
                                hit.below || flip[hit.box.node.id],
                        },
                    ]"
                    @pointerenter="show(hit.box.node.id)"
                    @pointerleave="hideSoon()"
                >
                    <div class="schema__card-body">
                        <p
                            v-if="hit.box.node.detail.kicker"
                            class="schema__card-kicker"
                        >
                            <svg
                                v-if="hit.box.iconPath"
                                class="schema__card-icon"
                                viewBox="0 0 12 12"
                                aria-hidden="true"
                            >
                                <path :d="hit.box.iconPath" />
                            </svg>
                            {{ hit.box.node.detail.kicker }}
                        </p>
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
                        <ul
                            v-if="hit.box.node.detail.usedIn?.length"
                            class="schema__used"
                        >
                            <li
                                v-for="name in hit.box.node.detail.usedIn"
                                :key="name"
                            >
                                {{ name }}
                            </li>
                        </ul>
                        <ul
                            v-if="hit.box.node.detail.related?.length"
                            class="schema__related"
                        >
                            <li
                                v-for="item in hit.box.node.detail.related"
                                :key="item.href"
                            >
                                <Link :href="item.href">
                                    <svg
                                        class="schema__related-icon"
                                        viewBox="0 0 12 12"
                                        aria-hidden="true"
                                    >
                                        <path :d="ICONS[item.kind] ?? ''" />
                                    </svg>
                                    <span>{{ item.label }}</span>
                                </Link>
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

/* Column headings and the time axis */
.schema__column {
    fill: var(--sw-text-muted);
    font-size: 10px;
    letter-spacing: 0.14em;
}

.schema__axis line {
    stroke: var(--sw-text-primary);
    stroke-width: var(--sw-hairline);
}

.schema__axis text {
    fill: var(--sw-text-muted);
    font-size: 10px;
}

.schema__break path {
    fill: none;
    stroke: var(--sw-text-primary);
    stroke-width: var(--sw-hairline);
}

/* Period bars: the span of a lot on the scale */
.schema__bar rect {
    fill: var(--sw-text-primary);
    opacity: 0.85;
}

.schema__bar--path rect {
    fill: var(--sw-path);
}

.schema__bar--mark rect {
    fill: var(--sw-mark);
    stroke: var(--sw-ink);
    stroke-width: var(--sw-hairline);
}

.schema__bar.is-lit rect {
    opacity: 1;
    stroke: var(--sw-path);
    stroke-width: var(--sw-hairline-strong);
}

.schema__leader {
    fill: none;
    stroke: var(--sw-text-muted);
    stroke-width: var(--sw-hairline);
    stroke-dasharray: 2 3;
}

.schema__leader.is-lit {
    stroke: var(--sw-path);
    stroke-dasharray: none;
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

/* By kind: an external address is blue; a project is heavier; a notion is dashed; a publication has its pictogram on a yellow strip */
.schema__lot--external rect:first-child {
    stroke: var(--sw-path);
}

.schema__lot--external .schema__icon {
    stroke: var(--sw-path);
}

.schema__lot--project rect:first-child {
    stroke-width: var(--sw-hairline-strong);
}

.schema__lot--notion rect:first-child {
    stroke-dasharray: 3 3;
}

.schema__lot--house rect:first-child {
    fill: transparent;
}

.schema__strip {
    fill: var(--sw-mark) !important;
    stroke: var(--sw-ink) !important;
}

.schema__icon {
    fill: none;
    stroke: var(--sw-ink);
    stroke-width: 1.15;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.schema__here {
    fill: var(--sw-here);
}

/* Pointing at a lot: it and what touches it stay lit, the rest steps back */
.schema__lot,
.schema__edge,
.schema__bar,
.schema__leader {
    transition: opacity 0.15s linear;
}

.has-open .schema__lot.is-dim {
    opacity: 0.28;
}

.schema__lot.is-open rect:first-child,
.schema__lot.is-near rect:first-child {
    stroke: var(--sw-path);
    stroke-width: var(--sw-hairline-strong);
}

.has-open .schema__edge:not(.is-lit) {
    opacity: 0.12;
}

.has-open .schema__bar:not(.is-lit),
.has-open .schema__leader:not(.is-lit) {
    opacity: 0.25;
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

.schema__edge.is-faint .schema__line {
    stroke-dasharray: 2 4;
    opacity: 0.45;
    animation: none;
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

/* The first time the drawing scrolls into view: the lots, then the edges, come in one after the other (opacity only) */
@keyframes schema-in {
    from {
        opacity: 0;
    }
}

.is-revealed .schema__lot,
.is-revealed .schema__edge {
    animation: schema-in 0.4s linear backwards;
    animation-delay: calc(var(--i, 0) * 22ms);
}

/* The layers of targets over the drawing */
.schema__hits,
.schema__bar-hits {
    position: absolute;
    inset: 0;
    margin: 0;
    padding: 0;
    list-style: none;
    pointer-events: none;
}

.schema__hit,
.schema__bar-hit {
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
    width: 18rem;
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

.schema__card-kicker {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-family: var(--sw-font-code);
    font-size: 0.625rem;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--sw-text-muted);
}

.schema__card-icon,
.schema__related-icon {
    flex: none;
    width: 0.8rem;
    height: 0.8rem;
    fill: none;
    stroke: currentColor;
    stroke-width: 1.15;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.schema__card-title {
    display: flex;
    flex-wrap: wrap;
    align-items: baseline;
    justify-content: space-between;
    gap: 0 0.75rem;
    font-weight: 500;
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

.schema__used {
    display: flex;
    flex-wrap: wrap;
    gap: 0.2rem 0.4rem;
    padding: 0;
    list-style: none;
}

.schema__used li {
    padding: 0.05rem 0.45rem;
    border: var(--sw-hairline) solid var(--sw-text-primary);
    font-family: var(--sw-font-code);
    font-size: 0.625rem;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}

.schema__related {
    display: grid;
    gap: 0.25rem;
    padding: 0;
    list-style: none;
}

.schema__related a {
    display: flex;
    align-items: flex-start;
    gap: 0.45rem;
    color: var(--sw-text-primary);
    text-decoration: none;
}

.schema__related a span {
    text-decoration: underline;
    text-decoration-color: var(--sw-path);
    text-underline-offset: 2px;
}

.schema__related-icon {
    margin-top: 0.2rem;
    padding: 1px;
    background: var(--sw-mark);
    color: var(--sw-ink);
    box-sizing: content-box;
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
    .is-revealed .schema__lot,
    .is-revealed .schema__edge {
        animation: none;
    }

    .schema__lot,
    .schema__edge,
    .schema__bar,
    .schema__leader,
    .schema__card {
        transition: none;
    }
}

:global(html[data-motion='reduced']) .schema__line,
:global(html[data-motion='reduced']) .is-revealed .schema__lot,
:global(html[data-motion='reduced']) .is-revealed .schema__edge {
    animation: none;
}
</style>
