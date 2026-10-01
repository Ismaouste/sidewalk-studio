/**
 * The plan of the work (specs/018-site-refresh): which lots exist, how they
 * connect, and where they sit on the sheet. Words come from the UI copy
 * (`copy/<locale>/pages/home.ts`, key `plan`), so French and English share one
 * geometry; geometry is here because it is not a translation.
 *
 * Three arrangements of the same plan: `compact` (a portrait strip for phones,
 * designed first), `wide` (a landscape sheet) and `ultra` (the same sheet with
 * the career branch on its left, for very wide screens only). Pure, so it can
 * be tested.
 */
import type {
    SchemaData,
    SchemaDetail,
    SchemaEdge,
    SchemaNode,
    SchemaTone,
} from './schema';

export type PlanLayoutName = 'wide' | 'compact' | 'ultra';

/** What the copy supplies for a lot. */
export interface PlanLotCopy {
    label: string;
    note: string;
    /** The card shown on hover, focus or tap. */
    detail?: SchemaDetail;
}

export interface PlanCopy {
    title: string;
    lots: Record<string, PlanLotCopy>;
    edges: Record<string, string>;
}

interface LotSpec {
    id: string;
    tone?: SchemaTone;
    /** An internal path (localized by the caller) or an absolute address; none for a lot without a public address. */
    href?: string;
    /** Absent: the lot is left off the landscape sheet. */
    wide?: [number, number];
    /** Absent: the lot is left off the phone strip (its parent's note already says it). */
    compact?: [number, number];
    /** Only on very wide screens. Absent: the lot sits where it does on `wide`, shifted right to make room for the career branch. */
    ultra?: [number, number];
}

/** How far the landscape sheet moves right to leave room for the career branch. */
const ULTRA_SHIFT = 500;

function placeOf(
    lot: LotSpec,
    layout: PlanLayoutName,
): [number, number] | undefined {
    if (layout === 'ultra') {
        return (
            lot.ultra ??
            (lot.wide ? [lot.wide[0] + ULTRA_SHIFT, lot.wide[1]] : undefined)
        );
    }

    return lot[layout];
}

/**
 * The lots: the pages of the site (Accueil, Réalisations, Études de cas,
 * Journal, Services, Contact, Labs) and what they hold (the projects and their
 * parts). Atlas Dépannage has no public address yet, so it has no link.
 */
export const LOTS: LotSpec[] = [
    { id: 'home', wide: [80, 350], compact: [180, 40] },
    // Pages of the site
    {
        id: 'work',
        tone: 'ink',
        href: '/work',
        wide: [250, 190],
        compact: [180, 120],
    },
    {
        id: 'cases',
        tone: 'ink',
        href: '/case-studies',
        wide: [250, 455],
        compact: [90, 560],
    },
    {
        id: 'journal',
        tone: 'ink',
        href: '/journal',
        wide: [250, 560],
        compact: [90, 700],
    },
    {
        id: 'services',
        tone: 'ink',
        href: '/services',
        wide: [250, 640],
        compact: [90, 780],
    },
    {
        id: 'contact',
        tone: 'ink',
        href: '/contact',
        wide: [470, 620],
        compact: [270, 780],
    },
    { id: 'labs', href: '/labs', wide: [470, 700], compact: [270, 860] },
    // The career branch, on the left of the very wide sheet
    { id: 'experience', tone: 'ink', href: '/experience', ultra: [100, 350] },
    { id: 'jewely', href: 'https://www.flippad.com', ultra: [100, 200] },
    { id: 'clientGodechot', ultra: [290, 60] },
    { id: 'clientCrown', ultra: [290, 140] },
    { id: 'clientJulian', ultra: [290, 220] },
    { id: 'clientAuberi', ultra: [290, 300] },
    { id: 'rolexCpo', ultra: [470, 60] },
    { id: 'rolexJulian', ultra: [470, 220] },
    { id: 'rolexBespoke', ultra: [290, 410] },
    { id: 'aremedia', href: 'https://aremedia.org', ultra: [100, 480] },
    { id: 'plm', href: 'https://se.parcourslemonde.org', ultra: [290, 540] },
    // astralmanach and its parts
    {
        id: 'astralmanach',
        tone: 'path',
        href: 'https://astralmanach.eu',
        wide: [470, 100],
        compact: [90, 310],
    },
    {
        id: 'lib',
        tone: 'path',
        href: 'https://www.npmjs.com/package/astralmanach',
        wide: [700, 40],
    },
    {
        id: 'api',
        tone: 'path',
        href: 'https://astralmanach.eu/api-publique',
        wide: [700, 100],
    },
    {
        id: 'tools',
        tone: 'path',
        href: 'https://astralmanach.eu',
        wide: [700, 160],
    },
    // Un art voulu voyant, florianrosinski.fr, the app
    {
        id: 'uavv',
        href: 'https://www.uavv.fr',
        wide: [470, 230],
        compact: [90, 220],
    },
    { id: 'obs', wide: [700, 230] },
    {
        id: 'florian',
        href: 'https://florianrosinski.fr',
        wide: [700, 300],
        compact: [270, 220],
    },
    { id: 'app', wide: [470, 320], compact: [270, 310] },
    // The four case studies
    { id: 'caseConsent', wide: [470, 380], compact: [270, 480] },
    { id: 'caseFlux', wide: [470, 430], compact: [270, 535] },
    { id: 'caseDeploy', wide: [470, 480], compact: [270, 590] },
    { id: 'caseTools', wide: [470, 530], compact: [270, 645] },
];

interface EdgeSpec {
    from: string;
    to: string;
    /** Key in the copy's `edges`, for the small label at mid-length. */
    label?: string;
    tone?: SchemaTone;
    /** Drawn on this arrangement only (an edge that would cross a lot on the phone strip is left off it); `wide` also covers `ultra`. */
    only?: PlanLayoutName;
}

/**
 * The interfaces. Only relations the public record supports: the library was
 * born inside the UAVV project, the portfolio shares its design, and the site's
 * pages lead to what they hold. The ink edges are the route through the pages.
 */
export const EDGES: EdgeSpec[] = [
    // The route through the pages
    { from: 'home', to: 'work', tone: 'ink' },
    { from: 'home', to: 'experience', tone: 'ink' },
    { from: 'home', to: 'cases', tone: 'ink', only: 'wide' },
    { from: 'cases', to: 'journal', tone: 'ink' },
    { from: 'journal', to: 'services', tone: 'ink' },
    { from: 'services', to: 'contact', tone: 'ink' },
    { from: 'services', to: 'labs', tone: 'ink' },
    // The projects under "Réalisations"
    { from: 'work', to: 'astralmanach', only: 'wide' },
    { from: 'work', to: 'uavv' },
    { from: 'work', to: 'app', only: 'wide' },
    { from: 'uavv', to: 'astralmanach', label: 'engine' },
    { from: 'uavv', to: 'florian', label: 'design' },
    { from: 'uavv', to: 'obs', only: 'wide' },
    // What astralmanach holds
    { from: 'astralmanach', to: 'lib', tone: 'path', only: 'wide' },
    { from: 'astralmanach', to: 'api', tone: 'path', only: 'wide' },
    { from: 'astralmanach', to: 'tools', tone: 'path', only: 'wide' },
    // The career: where the case studies come from
    { from: 'experience', to: 'jewely' },
    { from: 'jewely', to: 'rolexBespoke' },
    { from: 'clientGodechot', to: 'rolexCpo' },
    { from: 'clientJulian', to: 'rolexJulian' },
    { from: 'jewely', to: 'clientGodechot' },
    { from: 'jewely', to: 'clientCrown' },
    { from: 'jewely', to: 'clientJulian' },
    { from: 'jewely', to: 'clientAuberi' },
    { from: 'experience', to: 'aremedia' },
    { from: 'experience', to: 'plm' },
    // The case studies
    { from: 'cases', to: 'caseConsent' },
    { from: 'cases', to: 'caseFlux' },
    { from: 'cases', to: 'caseDeploy' },
    { from: 'cases', to: 'caseTools' },
];

const SHEET: Record<
    PlanLayoutName,
    {
        width: number;
        height: number;
        compass: [number, number];
        scale: [number, number];
    }
> = {
    wide: { width: 880, height: 740, compass: [830, 40], scale: [30, 710] },
    ultra: { width: 1380, height: 740, compass: [1330, 40], scale: [30, 710] },
    compact: { width: 360, height: 920, compass: [320, 40], scale: [20, 890] },
};

/** The plan as Schema data, for one arrangement. `localize` turns an internal path into the locale's address. */
export function buildPlan(
    copy: PlanCopy & { scale: string },
    layout: PlanLayoutName,
    localize: (href: string) => string,
): SchemaData {
    const sheet = SHEET[layout];
    const nodes: SchemaNode[] = LOTS.flatMap((lot) => {
        const at = placeOf(lot, layout);

        return at ? [{ lot, at }] : [];
    }).map(({ lot, at: [x, y] }) => {
        const text = copy.lots[lot.id];

        return {
            id: lot.id,
            label: text?.label ?? lot.id,
            note: text?.note || undefined,
            detail: text?.detail
                ? {
                      ...text.detail,
                      links: text.detail.links?.map((link) => ({
                          ...link,
                          href: link.href.startsWith('/')
                              ? localize(link.href)
                              : link.href,
                      })),
                  }
                : undefined,
            x,
            y,
            tone: lot.tone,
            href: lot.href
                ? lot.href.startsWith('/')
                    ? localize(lot.href)
                    : lot.href
                : undefined,
        };
    });
    const edges: SchemaEdge[] = EDGES.filter(
        (edge) =>
            edge.only === undefined ||
            edge.only === layout ||
            (edge.only === 'wide' && layout === 'ultra'),
    ).map((edge) => ({
        from: edge.from,
        to: edge.to,
        label: edge.label ? copy.edges[edge.label] : undefined,
        tone: edge.tone,
    }));

    return {
        title: copy.title,
        width: sheet.width,
        height: sheet.height,
        nodes,
        edges,
        here: 'home',
        // A portrait strip is 360 units wide: long labels break at spaces.
        wrap: layout === 'compact' ? 13 : undefined,
        compass: { x: sheet.compass[0], y: sheet.compass[1] },
        scale: { x: sheet.scale[0], y: sheet.scale[1], label: copy.scale },
    };
}
