<?php

namespace App\Content\Schema;

/**
 * What a declared field holds.
 *
 * The list is deliberately short. Every case here earns its place by making
 * either the save path or the generated editor behave differently — a type
 * that would validate exactly like `Line` and render exactly like `Line` is
 * not a type, it is a label, and labels belong in `Field::$label`.
 */
enum FieldType: string
{
    /** One line of plain text: a title, a label, an eyebrow. */
    case Line = 'line';

    /** A paragraph. Same validation as a line, a taller input. */
    case Text = 'text';

    /** Long prose. Only publications have it, and only as the body. */
    case Markdown = 'markdown';

    /** A URL-safe identifier. */
    case Slug = 'slug';

    /**
     * A calendar date. YAML resolves an unquoted ISO date to a Unix
     * timestamp, so the parsed value is an `int` far more often than it is a
     * string — see the note on `Field::violationsForScalar()`.
     */
    case Date = 'date';

    /** An absolute URL or a site-relative path. */
    case Url = 'url';

    /** One of a fixed set of values. */
    case Choice = 'choice';

    /** A nested object with its own declared fields. */
    case Group = 'group';
}
