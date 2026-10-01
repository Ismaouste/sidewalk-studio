/**
 * The timeline on the home page: what I worked on, when, with what, and where
 * it is told. Three columns on a time axis: the projects and roles (placed by
 * their dates, with a period bar and a leader line), the technologies and
 * notions, and the pages of the site with the publications. Words come from the
 * UI copy (`copy/<locale>/pages/timeline.ts`); geometry and dates are here
 * because they are not a translation. Publications are never written here: they
 * are the ones the site has published, matched to a notion by their tags, so
 * a new article about ERP and PIM flows appears on its card by itself.
 *
 * Pure, so it can be tested.
 */
import type {
    SchemaBar,
    SchemaData,
    SchemaDetail,
    SchemaEdge,
    SchemaKind,
    SchemaLeader,
    SchemaNode,
    SchemaRelation,
    SchemaTick,
} from './schema';

/** A published publication, as the server hands it over. */
export interface Publication {
    slug: string;
    title: string;
    summary: string;
    url: string;
    kind: Extract<SchemaKind, 'article' | 'note' | 'case'>;
    /** `YYYY-MM-DD`. */
    date: string;
    tags: string[];
}

export interface TimelineItemCopy {
    label: string;
    note: string;
    detail?: SchemaDetail;
}

export interface TimelineCopy {
    title: string;
    columns: { notions: string; pages: string; projects: string };
    kickers: Record<string, string>;
    items: Record<string, TimelineItemCopy>;
}

type Localize = (href: string) => string;

// ── The scale: the years before 2020 are drawn tighter than the years since ────────────────────────────────────────────────────────
const TOP = 112;
const BEFORE = 34;
const SINCE = 80;
const KNEE = 2020;

/** A date (decimal year) as a vertical position: tight before the knee, wide after. */
export const yOf = (t: number): number =>
    t <= KNEE
        ? TOP + (t - 2016) * BEFORE
        : TOP + (KNEE - 2016) * BEFORE + (t - KNEE) * SINCE;

const COLUMN = { notions: 600, pages: 885, projects: 215 };
const WIDTH = {
    notions: 190,
    pages: 190,
    projects: 160,
    house: 150,
    programme: 140,
};

// ── Periods: decimal years, from the CV and the experience page ───────────────────────────────────────────────────────────────────
interface Period {
    id: string;
    lane: number;
    from: number;
    to: number;
    node: string;
    tone?: SchemaBar['tone'];
}

const LANES = [52, 72, 92, 112];

const PERIODS: Period[] = [
    { id: 'p-artem', lane: 0, from: 2016.75, to: 2017.55, node: 'artem' },
    { id: 'p-plm', lane: 0, from: 2018.0, to: 2019.0, node: 'plm' },
    { id: 'p-aremedia', lane: 0, from: 2020.4, to: 2021.1, node: 'aremedia' },
    { id: 'p-jewely', lane: 0, from: 2021.67, to: 2026.67, node: 'jewely' },
    { id: 'p-docker', lane: 1, from: 2024.0, to: 2026.67, node: 'docker' },
    { id: 'p-crown', lane: 2, from: 2025.0, to: 2026.0, node: 'crown' },
    {
        id: 'p-cms',
        lane: 2,
        from: 2026.0,
        to: 2026.67,
        node: 'cms',
        tone: 'mark',
    },
    {
        id: 'p-2026',
        lane: 3,
        from: 2026.0,
        to: 2026.75,
        node: 'astralmanach',
        tone: 'path',
    },
];

/** Where a period bar leads: a bar can lead to several lots (the projects of 2026). */
const LEADERS: SchemaLeader[] = [
    ...PERIODS.map((p) => ({ bar: p.id, node: p.node })),
    { bar: 'p-2026', node: 'uavv' },
    { bar: 'p-2026', node: 'atlas' },
    { bar: 'p-2026', node: 'florian' },
];

// ── Lots ────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────
type Column = 'notions' | 'pages' | 'projects' | 'house' | 'programme';

interface LotSpec {
    id: string;
    column: Column;
    kind: SchemaKind;
    x?: number;
    y: number;
    href?: string;
    nofollow?: boolean;
    /** Tags that bring publications onto the card of a notion. */
    tags?: string[];
}

export const LOTS: LotSpec[] = [
    // Projects and roles, by date
    { id: 'artem', column: 'projects', kind: 'project', y: yOf(2017.15) },
    {
        id: 'plm',
        column: 'projects',
        kind: 'external',
        y: yOf(2018.5),
        href: 'https://se.parcourslemonde.org',
    },
    {
        id: 'aremedia',
        column: 'projects',
        kind: 'external',
        y: yOf(2020.75),
        href: 'https://aremedia.org',
    },
    {
        id: 'jewely',
        column: 'projects',
        kind: 'external',
        y: yOf(2021.9),
        href: 'https://www.flippad.com',
    },
    {
        id: 'crown',
        column: 'projects',
        kind: 'external',
        y: yOf(2025.5),
        href: 'https://www.crown-dp.com/fr',
        nofollow: true,
    },
    { id: 'cms', column: 'projects', kind: 'project', y: yOf(2026.3) },
    { id: 'atlas', column: 'projects', kind: 'project', y: 790 },
    {
        id: 'uavv',
        column: 'projects',
        kind: 'external',
        y: 842,
        href: 'https://www.uavv.fr',
    },
    {
        id: 'astralmanach',
        column: 'projects',
        kind: 'external',
        y: 890,
        href: 'https://astralmanach.eu',
    },
    {
        id: 'florian',
        column: 'projects',
        kind: 'external',
        y: 938,
        href: 'https://florianrosinski.fr',
    },
    // The Jewely client houses and the Rolex programmes, in the span of the Jewely bar
    {
        id: 'godechot',
        column: 'house',
        kind: 'house',
        y: yOf(2021.9) + 64,
        href: 'https://www.godechot-pauliet.com/en/',
        nofollow: true,
    },
    { id: 'julian', column: 'house', kind: 'house', y: yOf(2021.9) + 112 },
    { id: 'auberi', column: 'house', kind: 'house', y: yOf(2021.9) + 160 },
    {
        id: 'rolexBespoke',
        column: 'programme',
        kind: 'project',
        y: yOf(2021.9),
    },
    {
        id: 'rolexCpo',
        column: 'programme',
        kind: 'project',
        y: yOf(2021.9) + 64,
    },
    {
        id: 'rolexJulian',
        column: 'programme',
        kind: 'project',
        y: yOf(2021.9) + 112,
    },
    // Technologies and notions
    {
        id: 'erpPim',
        column: 'notions',
        kind: 'notion',
        y: 300,
        tags: ['pim', 'erp'],
    },
    {
        id: 'python',
        column: 'notions',
        kind: 'notion',
        y: 350,
        tags: ['scraping'],
    },
    {
        id: 'catalogs',
        column: 'notions',
        kind: 'notion',
        y: 400,
        tags: ['merchant-center', 'catalog'],
    },
    {
        id: 'cmsCore',
        column: 'notions',
        kind: 'notion',
        y: 450,
        tags: ['laravel', 'cms'],
    },
    {
        id: 'consent',
        column: 'notions',
        kind: 'notion',
        y: 500,
        tags: ['consent', 'privacy'],
    },
    {
        id: 'docker',
        column: 'notions',
        kind: 'notion',
        y: 560,
        tags: ['deployment', 'reliability'],
    },
    {
        id: 'seo',
        column: 'notions',
        kind: 'notion',
        y: 620,
        tags: ['seo', 'structured-data', 'schema-org', 'sitemap'],
    },
    {
        id: 'sensitive',
        column: 'notions',
        kind: 'notion',
        y: 680,
        tags: ['nonprofit', 'health', 'self-hosting'],
    },
    { id: 'nextts', column: 'notions', kind: 'notion', y: 760 },
    { id: 'astro', column: 'notions', kind: 'notion', y: 810 },
    // Pages of the site
    { id: 'work', column: 'pages', kind: 'page', y: 134, href: '/work' },
    {
        id: 'experience',
        column: 'pages',
        kind: 'page',
        y: 174,
        href: '/experience',
    },
    {
        id: 'services',
        column: 'pages',
        kind: 'page',
        y: 214,
        href: '/services',
    },
    {
        id: 'cases',
        column: 'pages',
        kind: 'page',
        y: 254,
        href: '/case-studies',
    },
    { id: 'journal', column: 'pages', kind: 'page', y: 294, href: '/journal' },
    { id: 'contact', column: 'pages', kind: 'page', y: 334, href: '/contact' },
];

/** Which notions a project touches: pointing at one lights the other, with no line drawn. */
const USES: [string, string][] = [
    ['jewely', 'erpPim'],
    ['jewely', 'python'],
    ['jewely', 'catalogs'],
    ['jewely', 'cmsCore'],
    ['jewely', 'docker'],
    ['jewely', 'consent'],
    ['jewely', 'seo'],
    ['cms', 'cmsCore'],
    ['aremedia', 'sensitive'],
    ['astralmanach', 'nextts'],
    ['astralmanach', 'astro'],
    ['uavv', 'nextts'],
    ['florian', 'nextts'],
];

/** The lines that stay drawn between lots of the first column: Rolex Bespoke hangs from Jewely, each Rolex space from its house. */
const HOUSE_EDGES: [string, string][] = [
    ['jewely', 'rolexBespoke'],
    ['godechot', 'rolexCpo'],
    ['julian', 'rolexJulian'],
];

const columnX = (column: Column): number =>
    column === 'house'
        ? COLUMN.projects
        : column === 'programme'
          ? 395
          : COLUMN[column];

const boxWidth = (column: Column): number => WIDTH[column];

/** A label cut to what a box can hold; the card says it in full. */
const clip = (text: string, max: number): string =>
    text.length <= max ? text : `${text.slice(0, max - 1).trimEnd()}…`;

export interface ListEntry {
    id: string;
    label: string;
    note: string;
    period?: string;
    href?: string;
    nofollow?: boolean;
    notions: { id: string; label: string }[];
    related: { kind: SchemaKind; label: string; href: string }[];
}

export interface Timeline {
    schema: SchemaData;
    /** The same content as a list, for a phone. */
    entries: ListEntry[];
}

/** The timeline, as Schema data and as a list. `localize` turns an internal path into the locale's address. */
export function buildTimeline(
    copy: TimelineCopy,
    publications: Publication[],
    localize: Localize,
): Timeline {
    const text = (id: string): TimelineItemCopy =>
        copy.items[id] ?? { label: id, note: '' };
    const lotById = new Map(LOTS.map((lot) => [lot.id, lot]));

    // Which publications each notion tells, by tag
    const tellers = (lot: LotSpec): Publication[] =>
        lot.tags
            ? publications
                  .filter((p) => p.tags.some((tag) => lot.tags!.includes(tag)))
                  .slice(0, 4)
            : [];

    // The publications, ordered by where their notions sit, so the edges cross as little as they can
    const averageY = (p: Publication): number => {
        const ys = LOTS.filter((l) => l.tags && tellers(l).includes(p)).map(
            (l) => l.y,
        );

        return ys.length > 0
            ? ys.reduce((a, b) => a + b, 0) / ys.length
            : Number.POSITIVE_INFINITY;
    };
    const ordered = [...publications].sort(
        (a, b) => averageY(a) - averageY(b) || b.date.localeCompare(a.date),
    );
    const pubY = new Map(ordered.map((p, i) => [p.slug, 400 + i * 44]));

    const publicationNodes: SchemaNode[] = ordered.map((p) => ({
        id: `pub:${p.slug}`,
        label: clip(p.title, 19),
        x: COLUMN.pages,
        y: pubY.get(p.slug)!,
        width: WIDTH.pages,
        kind: p.kind,
        href: p.url,
        note: p.summary,
        detail: {
            kicker: copy.kickers[p.kind],
            period: p.date.slice(0, 4),
            // The card says the title in full when the box had to cut it
            role: p.title.length > 19 ? `${p.title}. ${p.summary}` : p.summary,
        },
    }));

    const nodes: SchemaNode[] = LOTS.map((lot) => {
        const t = text(lot.id);
        const related = [
            ...tellers(lot),
            ...(lot.id === 'cases'
                ? publications.filter((p) => p.kind === 'case')
                : []),
            ...(lot.id === 'journal'
                ? publications.filter((p) => p.kind !== 'case')
                : []),
        ]
            .slice(0, 4)
            .map((p) => ({ kind: p.kind, label: p.title, href: p.url }));
        const usedIn = USES.filter(([, n]) => n === lot.id).map(([p]) =>
            text(p).label.replace(/\s*\n\s*/g, ' '),
        );
        const hasCard = !!t.detail || related.length > 0 || usedIn.length > 0;

        return {
            id: lot.id,
            label: t.label,
            note: t.note || undefined,
            x: lot.x ?? columnX(lot.column),
            y: lot.y,
            width: boxWidth(lot.column),
            kind: lot.kind,
            href: lot.href
                ? lot.href.startsWith('/')
                    ? localize(lot.href)
                    : lot.href
                : undefined,
            nofollow: lot.nofollow,
            detail: hasCard
                ? {
                      kicker: copy.kickers[lot.kind],
                      ...t.detail,
                      ...(related.length > 0 ? { related } : {}),
                      ...(usedIn.length > 0 ? { usedIn } : {}),
                      links: t.detail?.links?.map((link) => ({
                          ...link,
                          href: link.href.startsWith('/')
                              ? localize(link.href)
                              : link.href,
                      })),
                  }
                : undefined,
        };
    });

    // Edges that stay drawn
    const edges: SchemaEdge[] = [
        ...HOUSE_EDGES.map(([from, to]) => ({ from, to, route: 'x' as const })),
        ...LOTS.filter((l) => l.tags).flatMap((lot) =>
            tellers(lot).map((p) => ({
                from: lot.id,
                to: `pub:${p.slug}`,
                route: 'x' as const,
            })),
        ),
    ];
    // The houses are inside the Jewely bar: pointing at Jewely lights them, with no line drawn
    const relations: SchemaRelation[] = [
        ...USES,
        ...[
            'godechot',
            'julian',
            'auberi',
            'rolexBespoke',
            'rolexCpo',
            'rolexJulian',
        ].map((id): SchemaRelation => ['jewely', id]),
        ...(['cases', 'journal'] as const).flatMap((page) =>
            publications
                .filter((p) =>
                    page === 'cases' ? p.kind === 'case' : p.kind !== 'case',
                )
                .map((p): SchemaRelation => [page, `pub:${p.slug}`]),
        ),
    ];

    // The scale
    const ticks: SchemaTick[] = [];

    for (let year = 2016; year <= 2026; year += 1) {
        ticks.push({ y: yOf(year), label: String(year), major: true });
    }

    const bars: SchemaBar[] = PERIODS.map((p) => ({
        id: p.id,
        x: LANES[p.lane]!,
        w: 12,
        y1: yOf(p.from),
        y2: yOf(p.to),
        node: p.node,
        tone: p.tone,
    }));

    const schema: SchemaData = {
        title: copy.title,
        width: 1000,
        height: 1000,
        nodes: [...nodes, ...publicationNodes],
        edges,
        relations,
        axis: {
            x: 36,
            ticks,
            broken: yOf(KNEE) - 14,
        },
        bars,
        leaders: LEADERS.filter((l) => lotById.has(l.node)),
        columns: [
            { x: COLUMN.projects, y: 52, label: copy.columns.projects },
            { x: COLUMN.notions, y: 52, label: copy.columns.notions },
            { x: COLUMN.pages, y: 52, label: copy.columns.pages },
        ],
    };

    // The list for a phone: the projects and roles by date, with the notions they use and what tells them
    const entries: ListEntry[] = LOTS.filter(
        (l) => l.column === 'projects' || l.column === 'house',
    ).map((lot) => {
        const t = text(lot.id);
        const notions = USES.filter(([p]) => p === lot.id).map(([, n]) => ({
            id: n,
            label: text(n).label.replace(/\s*\n\s*/g, ' '),
        }));
        const related = USES.filter(([p]) => p === lot.id).flatMap(([, n]) =>
            tellers(lotById.get(n)!).map((p) => ({
                kind: p.kind,
                label: p.title,
                href: p.url,
            })),
        );
        const seen = new Set<string>();

        return {
            id: lot.id,
            label: t.label.replace(/\s*\n\s*/g, ' '),
            note: t.note,
            period: t.detail?.period,
            href: lot.href,
            nofollow: lot.nofollow,
            notions,
            related: related.filter((r) =>
                seen.has(r.href) ? false : (seen.add(r.href), true),
            ),
        };
    });

    return { schema, entries };
}
