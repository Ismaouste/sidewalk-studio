/**
 * The plan of the work (specs/018-site-refresh): which lots exist, how they
 * connect, and where they sit on the sheet. Words come from the UI copy
 * (`copy/<locale>/pages/home.ts`, key `plan`), so French and English share one
 * geometry; geometry is here because it is not a translation.
 *
 * Two arrangements of the same plan: `wide` (a landscape sheet) and `compact`
 * (a portrait strip for phones, designed first). Pure, so it can be tested.
 */
import type { SchemaData, SchemaEdge, SchemaNode, SchemaTone } from './schema';

export type PlanLayoutName = 'wide' | 'compact';

/** What the copy supplies for a lot. */
export interface PlanLotCopy {
    label: string;
    note: string;
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
    wide: [number, number];
    compact: [number, number];
}

/** The lots. Atlas Dépannage has no public address yet, so it has no link. */
export const LOTS: LotSpec[] = [
    { id: 'home', wide: [100, 200], compact: [180, 50] },
    {
        id: 'astralmanach',
        tone: 'path',
        href: 'https://astralmanach.eu',
        wide: [400, 80],
        compact: [275, 160],
    },
    {
        id: 'uavv',
        href: 'https://www.uavv.fr',
        wide: [400, 200],
        compact: [85, 160],
    },
    {
        id: 'florian',
        href: 'https://florianrosinski.fr',
        wide: [700, 200],
        compact: [85, 290],
    },
    { id: 'app', wide: [700, 80], compact: [275, 290] },
    {
        id: 'services',
        href: '/services',
        wide: [250, 350],
        compact: [180, 400],
    },
    {
        id: 'cases',
        href: '/case-studies',
        wide: [480, 350],
        compact: [180, 470],
    },
    { id: 'contact', href: '/contact', wide: [710, 350], compact: [180, 540] },
];

interface EdgeSpec {
    from: string;
    to: string;
    /** Key in the copy's `edges`, for the small label at mid-length. */
    label?: string;
    tone?: SchemaTone;
}

/** The interfaces. Only relations the public record supports: the library was born inside the UAVV project, and the portfolio shares its design. */
export const EDGES: EdgeSpec[] = [
    { from: 'home', to: 'astralmanach' },
    { from: 'home', to: 'uavv' },
    { from: 'uavv', to: 'astralmanach', label: 'engine' },
    { from: 'uavv', to: 'florian', label: 'design' },
    { from: 'home', to: 'services', tone: 'ink' },
    { from: 'services', to: 'cases', tone: 'ink' },
    { from: 'cases', to: 'contact', tone: 'ink' },
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
    wide: { width: 880, height: 450, compass: [830, 40], scale: [30, 415] },
    compact: { width: 360, height: 620, compass: [320, 40], scale: [20, 590] },
};

/** The plan as Schema data, for one arrangement. `localize` turns an internal path into the locale's address. */
export function buildPlan(
    copy: PlanCopy & { scale: string },
    layout: PlanLayoutName,
    localize: (href: string) => string,
): SchemaData {
    const sheet = SHEET[layout];
    const nodes: SchemaNode[] = LOTS.map((lot) => {
        const text = copy.lots[lot.id];
        const [x, y] = lot[layout];

        return {
            id: lot.id,
            label: text?.label ?? lot.id,
            note: text?.note || undefined,
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
    const edges: SchemaEdge[] = EDGES.map((edge) => ({
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
