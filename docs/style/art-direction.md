# Art Direction — the site plan

The site is drawn as a **site plan** (*plan de chantier*): its author is a
multi-skilled project lead who coordinates trades, and the pages are sheets of
a drawing. The decision and its reasons are in `specs/018-site-refresh/spec.md`
and in Principle 9 of the constitution.

## The sheet

- **Paper and ink.** A flat paper ground, near-black ink, five greys. Light is
  the default; the night theme is its mirror (clear on black).
- **Three primaries, by role, never as decoration.** Yellow marks, blue links and
  draws paths and dimensions, red says "you are here" or asks for attention.
  Never two primaries in one area of the screen.
- **Flat.** No blur, no gradient, no glass, no shadow. Surfaces are separated by
  hairlines. Corners are square (2px at most); a dot or a toggle knob is a
  separate shape (`--sw-radius-pill`).
- **A ruled grid** under the page (`AmbientGrid`), a few **flat stains** that
  drift slowly (`Stage`), and the **frame of the sheet**: registration crosses in
  the corners and a graduated ruler along the left edge with a red mark that
  follows the scroll (`SheetFrame`).
- **A title block** (*cartouche*) closes every page: project, sheet, scale,
  revision, author, place, and how to find the author (`Cartouche`).
- **Drawings carry the content.** Plans, lots, interfaces with travelling arrows,
  dimension lines, a compass and a graphic scale are drawn from data
  (`Schema`), and each has a text equivalent.

## Type

Two voices. **Switzer** (variable, self-hosted) for text and titles, weight
about 560. **DM Mono** in capitals for labels, legends, dimension text, the
cartouche and buttons.

## Voice of the copy

Short and factual. One idea per sentence. No rhetorical pairs ("not X but Y"),
no lists of three for effect, no metaphors about the craft. A page says what a
thing is and what was done, and links to it. Claims are limited to what a link
can confirm. `voice.md` holds the longer guidance.

## What not to reintroduce

Frosted panels, peach or violet washes, a serif display face, more than one
accent at a time, decorative shadows, animated glows, an illustrated sun.
