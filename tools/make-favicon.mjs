// Builds the favicon set from one drawing: an "IR" monogram on a 7 x 7 grid, ink and paper with a marker-yellow cell.
// Run: node tools/make-favicon.mjs   (writes public/favicon.svg, public/favicon.ico, public/apple-touch-icon.png)
//
// The monogram is made of whole cells only, so every size is drawn exactly, with no antialiasing to go soft at 16 px.
// No dependency: the PNGs are encoded here with zlib.

import { deflateSync } from 'node:zlib';
import { writeFileSync } from 'node:fs';

const INK = [0x12, 0x12, 0x12];
const PAPER = [0xf7, 0xf5, 0xef];
const MARK = [0xf2, 0xbe, 0x1a];

// The cells of the letters on a 7 x 7 grid: [column, row]. I is the stem on the left; R is a stem, a bowl and a stepped leg.
const I = [
    [1, 0],
    [1, 1],
    [1, 2],
    [1, 3],
    [1, 4],
    [1, 5],
    [1, 6],
];
const R = [
    [3, 0],
    [4, 0],
    [5, 0],
    [6, 0],
    [3, 1],
    [6, 1],
    [3, 2],
    [6, 2],
    [3, 3],
    [4, 3],
    [5, 3],
    [6, 3],
    [3, 4],
    [4, 4],
    [3, 5],
    [5, 5],
    [3, 6],
    [6, 6],
];
const LETTERS = [...I, ...R];
// The marker: the foot of the R, the one cell that is not paper.
const MARKED = (c, r) => c === 6 && r === 6;

const hex = (rgb) =>
    `#${rgb.map((n) => n.toString(16).padStart(2, '0')).join('')}`;

// ── SVG: theme-aware, 64 x 64, cells of 8 with a margin of 4 ────────────────────────────────────────────────────────────────────
function svg() {
    const cells = LETTERS.map(([c, r]) => {
        const cls = MARKED(c, r) ? 'm' : 'p';

        return `<rect class="${cls}" x="${4 + c * 8}" y="${4 + r * 8}" width="8" height="8"/>`;
    }).join('');

    return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
  <style>
    .b{fill:${hex(INK)}}.p{fill:${hex(PAPER)}}.m{fill:${hex(MARK)}}
    @media (prefers-color-scheme:dark){.b{fill:${hex(PAPER)}}.p{fill:${hex(INK)}}}
  </style>
  <rect class="b" width="64" height="64"/>${cells}
</svg>
`;
}

// ── PNG ─────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────
function crc32(buf) {
    let c;
    let crc = 0xffffffff;

    for (let n = 0; n < buf.length; n += 1) {
        c = (crc ^ buf[n]) & 0xff;

        for (let k = 0; k < 8; k += 1) {
            c = c & 1 ? 0xedb88320 ^ (c >>> 1) : c >>> 1;
        }

        crc = (crc >>> 8) ^ c;
    }

    return (crc ^ 0xffffffff) >>> 0;
}

function chunk(type, data) {
    const length = Buffer.alloc(4);
    length.writeUInt32BE(data.length);
    const body = Buffer.concat([Buffer.from(type), data]);
    const crc = Buffer.alloc(4);
    crc.writeUInt32BE(crc32(body));

    return Buffer.concat([length, body, crc]);
}

/** A PNG of `size` x `size`, the monogram drawn in whole pixels: the cell is `floor(size / 8)` pixels, centred. */
function png(size, { dark = false } = {}) {
    const cell = Math.max(1, Math.floor(size / 8));
    const offset = Math.floor((size - cell * 7) / 2);
    const bg = dark ? PAPER : INK;
    const fg = dark ? INK : PAPER;
    const pixels = Buffer.alloc(size * size * 4);

    for (let i = 0; i < size * size; i += 1) {
        pixels.set([...bg, 255], i * 4);
    }

    for (const [c, r] of LETTERS) {
        const colour = MARKED(c, r) ? MARK : fg;

        for (let y = 0; y < cell; y += 1) {
            for (let x = 0; x < cell; x += 1) {
                pixels.set(
                    [...colour, 255],
                    ((offset + r * cell + y) * size + offset + c * cell + x) *
                        4,
                );
            }
        }
    }

    const rows = Buffer.alloc((size * 4 + 1) * size);

    for (let y = 0; y < size; y += 1) {
        rows[y * (size * 4 + 1)] = 0;
        pixels.copy(
            rows,
            y * (size * 4 + 1) + 1,
            y * size * 4,
            (y + 1) * size * 4,
        );
    }

    const header = Buffer.alloc(13);
    header.writeUInt32BE(size, 0);
    header.writeUInt32BE(size, 4);
    header.set([8, 6, 0, 0, 0], 8);

    return Buffer.concat([
        Buffer.from([0x89, 0x50, 0x4e, 0x47, 0x0d, 0x0a, 0x1a, 0x0a]),
        chunk('IHDR', header),
        chunk('IDAT', deflateSync(rows)),
        chunk('IEND', Buffer.alloc(0)),
    ]);
}

/** An .ico holding PNG images of the given sizes. */
function ico(sizes) {
    const images = sizes.map((s) => png(s));
    const header = Buffer.alloc(6);
    header.writeUInt16LE(1, 2);
    header.writeUInt16LE(images.length, 4);

    let offset = 6 + images.length * 16;
    const entries = images.map((image, i) => {
        const entry = Buffer.alloc(16);
        entry[0] = sizes[i] % 256;
        entry[1] = sizes[i] % 256;
        entry.writeUInt16LE(1, 4);
        entry.writeUInt16LE(32, 6);
        entry.writeUInt32LE(image.length, 8);
        entry.writeUInt32LE(offset, 12);
        offset += image.length;

        return entry;
    });

    return Buffer.concat([header, ...entries, ...images]);
}

writeFileSync('public/favicon.svg', svg());
writeFileSync('public/favicon.ico', ico([16, 32, 48]));
writeFileSync('public/apple-touch-icon.png', png(180));
console.log(
    'favicon.svg, favicon.ico (16, 32, 48) and apple-touch-icon.png (180) written',
);
