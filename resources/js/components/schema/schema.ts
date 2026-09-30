/**
 * The data model and the pure geometry of the Schema component
 * (specs/018-site-refresh). A schema is a small site plan: nodes (the lots),
 * edges (the interfaces between them), dimension lines, a compass and a
 * graphic scale. It is declared as data in the page content, so its labels
 * travel in French and English like every other string, and it draws the same
 * from a one-node diagram to a dozen lots.
 */

export type SchemaTone = 'ink' | 'mark' | 'path' | 'here';

export interface SchemaNode {
    id: string;
    /** One or two lines: separate them with `\n`. */
    label: string;
    /** Centre, in the schema's own units (the `width` × `height` box). */
    x: number;
    y: number;
    tone?: SchemaTone;
    /** An internal or external address; the node becomes a link. */
    href?: string;
    /** The short line read after the label in the text equivalent. */
    note?: string;
}

export interface SchemaEdge {
    from: string;
    to: string;
    /** What passes through this interface (a tiny label at mid-length). */
    label?: string;
    tone?: SchemaTone;
    /** Bend of the line, in units: 0 is straight. */
    bend?: number;
}

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
    /** A compass rose at this centre. */
    compass?: { x: number; y: number };
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
    width: number;
    height: number;
}

const CHAR_WIDTH = 7.4;
const LINE_HEIGHT = 16;
const PAD_X = 14;
const PAD_Y = 10;
const MIN_WIDTH = 92;

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

    return {
        node,
        lines: lines.length > 0 ? lines : [node.id],
        width: Math.max(
            MIN_WIDTH,
            Math.round(longest * CHAR_WIDTH + PAD_X * 2),
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

        const start = exit(a, b.node);
        const end = exit(b, a.node);
        const bend = edge.bend ?? 0;
        const mx = (start.x + end.x) / 2;
        const my = (start.y + end.y) / 2;
        const length = Math.hypot(end.x - start.x, end.y - start.y) || 1;
        // The control point is pushed along the normal of the segment.
        const cx = mx + (-(end.y - start.y) / length) * bend;
        const cy = my + ((end.x - start.x) / length) * bend;
        const f = (n: number) => Math.round(n * 10) / 10;
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
