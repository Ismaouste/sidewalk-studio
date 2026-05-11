#!/usr/bin/env python3
"""
One-off image pipeline for portrait assets.
Run: python3 scripts/encode-portrait.py public/images/contact-avatar.png

Produces AVIF + WebP + an optimized PNG fallback alongside the source.
Requires: Pillow, pillow-avif-plugin (pip install Pillow pillow-avif-plugin).
"""
from __future__ import annotations

import os
import sys
from pathlib import Path

from PIL import Image
import pillow_avif  # noqa: F401  (registers the AVIF plugin with Pillow)

MAX_SIDE = 960
AVIF_QUALITY = 55
WEBP_QUALITY = 80


def encode(source: Path) -> None:
    img = Image.open(source)
    print(f"original: {img.size} {img.mode} ({source.stat().st_size / 1024:.1f} KB)")

    ratio = MAX_SIDE / max(img.size)
    if ratio < 1:
        img = img.resize(
            (round(img.size[0] * ratio), round(img.size[1] * ratio)),
            Image.LANCZOS,
        )
        print(f"resized:  {img.size}")

    avif_path = source.with_suffix(".avif")
    webp_path = source.with_suffix(".webp")
    png_path = source

    img.save(avif_path, quality=AVIF_QUALITY, speed=4)
    img.save(webp_path, quality=WEBP_QUALITY, method=6)
    img.save(png_path, optimize=True)

    for path in (avif_path, webp_path, png_path):
        size = os.path.getsize(path)
        print(f"  {path.name}: {size / 1024:.1f} KB")


def main() -> int:
    if len(sys.argv) != 2:
        print("usage: encode-portrait.py <source.png>", file=sys.stderr)
        return 2
    source = Path(sys.argv[1])
    if not source.is_file():
        print(f"file not found: {source}", file=sys.stderr)
        return 1
    encode(source)
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
