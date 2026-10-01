/**
 * The data model and the pure geometry of the Schema component
 * (specs/018-site-refresh). A schema is a small drawing: nodes (the lots),
 * edges (the interfaces between them), and, for a timeline, a time axis with
 * period bars, column headings and leader lines from a bar to its node. It is
 * declared as data, so its labels travel in French and English like every
 * other string, and it draws the same from a one-node diagram to a few dozen
 * lots.
 */

export type SchemaTone = 'ink' | 'mark' | 'path' | 'here';

/**
 * What a lot is, which decides how it is drawn and what its card shows: a page
 * of the site (ink), an external address (blue, with an arrow), a project, a
 * notion or a technology (dashed), a house of the client portfolio, and the
 * three kinds of publication (each with its own pictogram, all internal links).
 */
export type SchemaKind =
    | 'page'
    | 'external'
    | 'project'
    | 'notion'
    | 'house'
    | 'article'
    | 'note'
    | 'case';

/** Pictograms, drawn on a 12 × 12 grid, stroke only: one per kind that carries one. */
export const KIND_ICONS: Partial<Record<SchemaKind, string>> = {
    article: 'M2.5 1.5h5l2 2v7h-7zM4.2 5.2h3.6M4.2 7.2h3.6M4.2 9h2',
    case: 'M1.5 3.2h3l1 1.3h5v5.6h-9zM1.5 5.2h9',
    external: 'M4 2.2h5.8v5.8M9.8 2.2L2.6 9.4',
    note: 'M2 10l.7-2.6L8.1 2 10 3.9 4.6 9.3zM7 3.1l1.9 1.9',
};

/** The kinds that carry a pictogram in their box. */
export const ICON_KINDS: SchemaKind[] = ['article', 'note', 'case', 'external'];

/** What a lot says about itself on hover, focus or tap: a card. Every field is optional; nothing here is invented, it is copy. */
export interface SchemaDetail {
    /** What kind of lot this is, in words ("Case study", "Technology"): the small line above the title of the card. */
    kicker?: string;
    /** When, as written ("2025", "2021–2026"). */
    period?: string;
    /** What I did there, in a line. */
    role?: string;
    /** A few numbers, in large type: `value` is shown as is, `label` says what it counts. */
    figures?: { value: string; label: string }[];
    /** Where to read more. `nofollow` is for a client's site: it is mentioned, not endorsed. */
    links?: { label: string; href: string; nofollow?: boolean }[];
    /** Publications that tell this lot (read from the journal and the case studies, never written here). */
    related?: { kind: SchemaKind; label: string; href: string }[];
    /** The projects that used it, for a technology. */
    usedIn?: string[];
}

export interface SchemaNode {
    id: string;
    /** One or two lines: separate them with `\n`. */
    label: string;
    /** Centre, in the schema's own units (the `width` × `height` box). */
    x: number;
    y: number;
    tone?: SchemaTone;
    kind?: SchemaKind;
    /** A fixed box width, in units, so a column of lots lines up. */
    width?: number;
    /** An internal or external address; the node becomes a link. */
    href?: string;
    /** `nofollow` for a client's site. */
    nofollow?: boolean;
    /** The short line read after the label in the text equivalent. */
    note?: string;
    /** The card shown on hover, focus or tap. */
    detail?: SchemaDetail;
}

export interface SchemaEdge {
    from: string;
    to: string;
    /** What passes through this interface (a tiny label at mid-length). */
    label?: string;
    tone?: SchemaTone;
    /** Bend of the line, in units. Absent: a smooth S-curve that leaves and enters the boxes square on. 0: straight. */
    bend?: number;
    /** Which sides the curve leaves and enters by: left to right (`x`) or top to bottom (`y`). Absent: the axis the boxes are furthest apart on. */
    route?: 'x' | 'y';
    /** Drawn thin and pale: a relation worth seeing, not worth following. */
    faint?: boolean;
}

/** A period bar on the time axis. */
export interface SchemaBar {
    id: string;
    /** Left edge and width of the bar, in units. */
    x: number;
    w: number;
    /** From and to, as vertical positions (the caller has already put the dates on its scale). */
    y1: number;
    y2: number;
    /** The lot this period belongs to: pointing at the bar lights it. */
    node?: string;
    tone?: SchemaTone;
}

/** A line from a bar to its lot. */
export interface SchemaLeader {
    bar: string;
    node: string;
}

export interface SchemaTick {
    y: number;
    label: string;
    /** A year, rather than a mark of the scale. */
    major?: boolean;
}

export interface SchemaColumn {
    x: number;
    y: number;
    label: string;
    anchor?: 'start' | 'middle';
}

/** Lots that are related without a line being drawn: pointing at one lights the other. */
export type SchemaRelation = [string, string];

export interface SchemaDimension {
    from: [number, number];
    to: [number, number];
    label: string;
}

export interface SchemaData {
    /** Accessible name of the drawing. */
    title: string;
    width: number;
    height: number;
    nodes: SchemaNode[];
    edges?: SchemaEdge[];
    dimensions?: SchemaDimension[];
    /** The time axis: a vertical line at `x` with its ticks. `broken` marks where the scale changes (the years before it are drawn tighter). */
    axis?: { x: number; ticks: SchemaTick[]; broken?: number };
    bars?: SchemaBar[];
    leaders?: SchemaLeader[];
    columns?: SchemaColumn[];
    relations?: SchemaRelation[];
    /** A graphic scale bar whose left end sits here; `label` is its caption. */
    scale?: { x: number; y: number; label: string };
    /** The node that carries "you are here" (red). */
    here?: string;
    /** Wrap labels longer than this many characters at spaces (narrow sheets). */
    wrap?: number;
}

export interface NodeBox {
    node: SchemaNode;
    lines: string[];
    /** Room kept on the left of the text for the pictogram. */
    icon: number;
    width: number;
    height: number;
}

const CHAR_WIDTH = 7.4;
const LINE_HEIGHT = 16;
const PAD_X = 14;
const PAD_Y = 10;
const MIN_WIDTH = 92;
const ICON_PAD = 18;

/** Breaks a line at spaces so that no line is longer than `max` (a single long word stays whole). */
export function wrapLine(line: string, max: number): string[] {
    const out: string[] = [];
    let current = '';

    for (const word of line.split(/\s+/).filter(Boolean)) {
        if (current !== '' && current.length + 1 + word.length > max) {
            out.push(current);
            current = word;
        } else {
            current = current === '' ? word : `${current} ${word}`;
        }
    }

    if (current !== '') {
        out.push(current);
    }

    return out;
}

/** The box of a node, sized from its longest line so a long French label still fits. */
export function nodeBox(node: SchemaNode, wrap?: number): NodeBox {
    const lines = node.label
        .split('\n')
        .map((line) => line.trim())
        .filter(Boolean)
        .flatMap((line) => (wrap ? wrapLine(line, wrap) : [line]));
    const longest = lines.reduce((max, line) => Math.max(max, line.length), 0);

    const icon = node.kind && ICON_KINDS.includes(node.kind) ? ICON_PAD : 0;

    return {
        node,
        lines: lines.length > 0 ? lines : [node.id],
        icon,
        width:
            node.width ??
            Math.max(
                MIN_WIDTH,
                Math.round(longest * CHAR_WIDTH + PAD_X * 2 + icon),
            ),
        height: Math.max(1, lines.length) * LINE_HEIGHT + PAD_Y * 2,
    };
}

/** Where the segment from a box centre toward a point leaves the box. */
function exit(
    box: NodeBox,
    toward: { x: number; y: number },
): { x: number; y: number } {
    const dx = toward.x - box.node.x;
    const dy = toward.y - box.node.y;

    if (dx === 0 && dy === 0) {
        return { x: box.node.x, y: box.node.y };
    }

    const sx = dx === 0 ? Infinity : box.width / 2 / Math.abs(dx);
    const sy = dy === 0 ? Infinity : box.height / 2 / Math.abs(dy);
    const s = Math.min(sx, sy);

    return { x: box.node.x + dx * s, y: box.node.y + dy * s };
}

export interface Curve {
    start: { x: number; y: number };
    c1: { x: number; y: number };
    c2: { x: number; y: number };
    end: { x: number; y: number };
    /** The point at half the parameter, for the label. */
    mid: { x: number; y: number };
}

/**
 * A smooth S-curve (one cubic Bézier) between two boxes. It leaves the side of
 * the first box that faces the second and enters the side of the second that
 * faces the first, square on to both, so an arrow reads as a route between two
 * lots instead of a ruler line. The axis is the one the boxes are furthest
 * apart on; the handles are half the gap long, never shorter than 24 units.
 */
export function smoothCurve(a: NodeBox, b: NodeBox, route?: 'x' | 'y'): Curve {
    const dx = b.node.x - a.node.x;
    const dy = b.node.y - a.node.y;
    // A box wider than it is tall is read on its own proportions: 1 unit of height weighs more than 1 of width.
    const horizontal = route
        ? route === 'x'
        : Math.abs(dx) * 0.6 >= Math.abs(dy);
    const sign = (n: number) => (n < 0 ? -1 : 1);

    let start: { x: number; y: number };
    let end: { x: number; y: number };
    let c1: { x: number; y: number };
    let c2: { x: number; y: number };

    if (horizontal) {
        const s = sign(dx);

        start = { x: a.node.x + (s * a.width) / 2, y: a.node.y };
        end = { x: b.node.x - (s * b.width) / 2, y: b.node.y };

        const k = Math.max(24, Math.abs(end.x - start.x) / 2);

        c1 = { x: start.x + s * k, y: start.y };
        c2 = { x: end.x - s * k, y: end.y };
    } else {
        const s = sign(dy);

        start = { x: a.node.x, y: a.node.y + (s * a.height) / 2 };
        end = { x: b.node.x, y: b.node.y - (s * b.height) / 2 };

        const k = Math.max(24, Math.abs(end.y - start.y) / 2);

        c1 = { x: start.x, y: start.y + s * k };
        c2 = { x: end.x, y: end.y - s * k };
    }

    return {
        start,
        c1,
        c2,
        end,
        mid: {
            x: (start.x + 3 * c1.x + 3 * c2.x + end.x) / 8,
            y: (start.y + 3 * c1.y + 3 * c2.y + end.y) / 8,
        },
    };
}

/** The path of a leader: from the right edge of a period bar to the left side of its lot, one gentle S. */
export function leaderPath(
    from: { x: number; y: number },
    box: NodeBox,
): string {
    const end = { x: box.node.x - box.width / 2, y: box.node.y };
    const k = Math.max(14, (end.x - from.x) / 2);
    const f = (n: number) => Math.round(n * 10) / 10;

    return `M${f(from.x)} ${f(from.y)}C${f(from.x + k)} ${f(from.y)} ${f(end.x - k)} ${f(end.y)} ${f(end.x)} ${f(end.y)}`;
}

export interface EdgeGeometry {
    edge: SchemaEdge;
    /** SVG path from the edge of one box to the edge of the other. */
    d: string;
    /** Mid-point, for the label. */
    mid: { x: number; y: number };
}

/** The geometry of every edge whose two ends exist; an edge to a missing node is dropped, never thrown. */
export function edgeGeometry(data: SchemaData): EdgeGeometry[] {
    const boxes = new Map(
        data.nodes.map((node) => [node.id, nodeBox(node, data.wrap)]),
    );
    const out: EdgeGeometry[] = [];

    for (const edge of data.edges ?? []) {
        const a = boxes.get(edge.from);
        const b = boxes.get(edge.to);

        if (!a || !b || a === b) {
            continue;
        }

        const f = (n: number) => Math.round(n * 10) / 10;

        if (edge.bend === undefined) {
            const curve = smoothCurve(a, b, edge.route);

            out.push({
                edge,
                d: `M${f(curve.start.x)} ${f(curve.start.y)}C${f(curve.c1.x)} ${f(curve.c1.y)} ${f(curve.c2.x)} ${f(curve.c2.y)} ${f(curve.end.x)} ${f(curve.end.y)}`,
                mid: curve.mid,
            });

            continue;
        }

        const start = exit(a, b.node);
        const end = exit(b, a.node);
        const bend = edge.bend;
        const mx = (start.x + end.x) / 2;
        const my = (start.y + end.y) / 2;
        const length = Math.hypot(end.x - start.x, end.y - start.y) || 1;
        // The control point is pushed along the normal of the segment.
        const cx = mx + (-(end.y - start.y) / length) * bend;
        const cy = my + ((end.x - start.x) / length) * bend;
        const d =
            bend === 0
                ? `M${f(start.x)} ${f(start.y)}L${f(end.x)} ${f(end.y)}`
                : `M${f(start.x)} ${f(start.y)}Q${f(cx)} ${f(cy)} ${f(end.x)} ${f(end.y)}`;

        out.push({
            edge,
            d,
            mid:
                bend === 0
                    ? { x: mx, y: my }
                    : {
                          x: (start.x + 2 * cx + end.x) / 4,
                          y: (start.y + 2 * cy + end.y) / 4,
                      },
        });
    }

    return out;
}

/** The text equivalent: one line per lot, then one per interface. The drawing itself is `aria-hidden`. */
export function describeSchema(data: SchemaData, arrow = '→'): string[] {
    const name = new Map(
        data.nodes.map((node) => [
            node.id,
            node.label.replace(/\s*\n\s*/g, ' '),
        ]),
    );
    const lines = data.nodes.map((node) =>
        node.note
            ? `${name.get(node.id)} — ${node.note}`
            : (name.get(node.id) ?? node.id),
    );

    for (const edge of data.edges ?? []) {
        if (name.has(edge.from) && name.has(edge.to)) {
            lines.push(
                `${name.get(edge.from)} ${arrow} ${name.get(edge.to)}${edge.label ? ` (${edge.label})` : ''}`,
            );
        }
    }

    for (const dimension of data.dimensions ?? []) {
        lines.push(dimension.label);
    }

    return lines;
}
