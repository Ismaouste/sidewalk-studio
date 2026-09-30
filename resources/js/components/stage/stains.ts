/**
 * The stains of the Stage (specs/018-site-refresh): a few flat organic shapes
 * that drift slowly behind the page. Pure and seeded, so the layout is the
 * same for a whole session and can be tested without a browser.
 *
 * Rules the layout keeps:
 * - about 70 % of the stains are grey (ink), about 30 % carry a primary;
 * - two primary stains never sit in neighbouring cells, so never two primaries
 *   in the same area of the screen;
 * - the path of each blob is a smooth closed curve, never a gradient or blur.
 */

export type StainTone = 'grey' | 'mark' | 'path' | 'here';

export interface Stain {
    tone: StainTone;
    /** Left / top of the stain's box, in vw / vh of the viewport. */
    x: number;
    y: number;
    /** Size of the box, in vmax. */
    size: number;
    /** SVG path in a 0–100 box. */
    path: string;
    /** Drift: translation in vw / vh, rotation in degrees, one way and back. */
    dx: number;
    dy: number;
    rot: number;
    /** Seconds for one way; negative delay desynchronises the stains. */
    duration: number;
    delay: number;
}

/** A small seeded generator (mulberry32): same seed, same sequence. */
export function rng(seed: number): () => number {
    let a = seed >>> 0;

    return () => {
        a = (a + 0x6d2b79f5) >>> 0;
        let t = a;
        t = Math.imul(t ^ (t >>> 15), t | 1);
        t ^= t + Math.imul(t ^ (t >>> 7), t | 61);

        return ((t ^ (t >>> 14)) >>> 0) / 4294967296;
    };
}

/** A smooth closed blob: `points` radii around the centre, joined by Catmull-Rom curves. */
export function blobPath(random: () => number, points = 8): string {
    const pts: [number, number][] = [];

    for (let i = 0; i < points; i += 1) {
        const angle = (i / points) * Math.PI * 2;
        const radius = 30 + random() * 18;
        pts.push([50 + Math.cos(angle) * radius, 50 + Math.sin(angle) * radius]);
    }

    const at = (i: number) => pts[(i + points) % points];
    const f = (n: number) => n.toFixed(1);
    let d = `M${f(at(0)[0])} ${f(at(0)[1])}`;

    for (let i = 0; i < points; i += 1) {
        const p0 = at(i - 1);
        const p1 = at(i);
        const p2 = at(i + 1);
        const p3 = at(i + 2);
        const c1: [number, number] = [p1[0] + (p2[0] - p0[0]) / 6, p1[1] + (p2[1] - p0[1]) / 6];
        const c2: [number, number] = [p2[0] - (p3[0] - p1[0]) / 6, p2[1] - (p3[1] - p1[1]) / 6];
        d += `C${f(c1[0])} ${f(c1[1])} ${f(c2[0])} ${f(c2[1])} ${f(p2[0])} ${f(p2[1])}`;
    }

    return `${d}Z`;
}

const PRIMARIES: StainTone[] = ['mark', 'path', 'here'];

function shuffled<T>(items: T[], random: () => number): T[] {
    const out = [...items];

    for (let i = out.length - 1; i > 0; i -= 1) {
        const j = Math.floor(random() * (i + 1));
        [out[i], out[j]] = [out[j], out[i]];
    }

    return out;
}

/** The layout for a session: `count` stains on a 3 × 3 grid of cells. */
export function layoutStains(seed: number, count = 7): Stain[] {
    const random = rng(seed);
    const cells = shuffled([0, 1, 2, 3, 4, 5, 6, 7, 8], random).slice(0, Math.min(count, 9));
    const cellXY = (c: number) => ({ cx: c % 3, cy: Math.floor(c / 3) });
    const neighbours = (a: number, b: number) =>
        Math.max(Math.abs(cellXY(a).cx - cellXY(b).cx), Math.abs(cellXY(a).cy - cellXY(b).cy)) < 2;

    // Two primaries (about 30 %), in cells that do not touch.
    const primaryCells: number[] = [];

    for (const c of cells) {
        if (primaryCells.length >= Math.round(count * 0.3)) {
            break;
        }

        if (primaryCells.every((p) => !neighbours(p, c))) {
            primaryCells.push(c);
        }
    }

    const tones = shuffled(PRIMARIES, random);

    return cells.map((cell) => {
        const { cx, cy } = cellXY(cell);
        const primaryIndex = primaryCells.indexOf(cell);
        const size = 34 + random() * 30;

        return {
            tone: primaryIndex >= 0 ? tones[primaryIndex % tones.length] : 'grey',
            x: cx * 34 - 12 + random() * 14,
            y: cy * 34 - 12 + random() * 14,
            size,
            path: blobPath(random),
            dx: (random() - 0.5) * 26,
            dy: (random() - 0.5) * 22,
            rot: (random() - 0.5) * 50,
            duration: 60 + random() * 60,
            delay: -random() * 120,
        };
    });
}
