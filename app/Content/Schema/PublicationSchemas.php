<?php

namespace App\Content\Schema;

use RuntimeException;

/**
 * What a publication holds, declared once per section.
 *
 * Publications did not need the approach debate that pages needed. All 22
 * journal entries share exactly one shape with fifteen fields, every one
 * present in every file; the case studies add four of their own and treat
 * four of the SEO extras as optional. They were already rows. What they
 * lacked was a declaration saying so, and a field pairing a translation with
 * its original.
 */
final class PublicationSchemas
{
    public const SECTIONS = ['writing', 'case-studies'];

    public static function for(string $section): ContentSchema
    {
        return match ($section) {
            'writing' => self::writing(),
            'case-studies' => self::caseStudies(),
            default => throw new RuntimeException(
                "No publication schema declared for section [{$section}]."
            ),
        };
    }

    /**
     * @return array<string, ContentSchema>
     */
    public static function all(): array
    {
        $schemas = [];

        foreach (self::SECTIONS as $section) {
            $schemas[$section] = self::for($section);
        }

        return $schemas;
    }

    public static function writing(): ContentSchema
    {
        return new ContentSchema('writing', 'Journal entry', self::sharedFields());
    }

    public static function caseStudies(): ContentSchema
    {
        return new ContentSchema('case-studies', 'Case study', [
            ...self::sharedFields(),
            Field::line('client', 'Client'),
            Field::line('role', 'Role'),
            Field::line('stack', 'Stack')->repeating(),
            Field::text('outcomes', 'Outcomes')->repeating(),
        ]);
    }

    /**
     * The fifteen fields every publication carries.
     *
     * Four of them are optional, and only because the case studies are
     * inconsistent about them today — `canonical`, `ogImage` and `schema` are
     * present in two files of eight, and `publication_type` in six. Declaring
     * them optional rather than backfilling thirty files is the honest
     * reading: they are SEO extras that a publication may or may not need,
     * and `publication_type` already has a derivation in the repository for
     * when it is absent.
     *
     * @return array<int, Field>
     */
    protected static function sharedFields(): array
    {
        return [
            Field::line('title', 'Title'),
            Field::slug('slug', 'Slug'),
            Field::text('summary', 'Summary'),
            Field::choice('status', ['draft', 'published'], 'Status'),
            Field::date('published_at', 'Published'),
            Field::date('updated_at', 'Updated'),
            Field::line('tags', 'Tags')->repeating(),
            Field::line('category', 'Category'),
            Field::line('seo_title', 'SEO title'),
            Field::text('seo_description', 'SEO description'),
            Field::line('accent_tone', 'Accent tone'),

            /**
             * The field that makes rows possible.
             *
             * Publication slugs are per-locale and mostly differ — 6 of 11
             * journal entries and 2 of 4 case studies have a different slug in
             * French — and until now nothing in the data linked a translation
             * to its original. Nothing needed to: each locale is a separate
             * directory, and the directory *was* the link. The moment
             * publications become rows in one table, "the French version of
             * this article" has no expression at all, and the admin cannot
             * pair them.
             *
             * So it is required, and it is required *before* the precedence
             * flip rather than alongside it. Two files sharing a
             * `translation_key` are the same publication in two languages.
             */
            Field::slug('translation_key', 'Translation key')
                ->withHelp('Shared by the two locale versions of one publication.'),

            Field::line('publication_type', 'Publication type')->optional(),
            Field::url('canonical', 'Canonical URL')->optional(),
            Field::url('ogImage', 'Social image')->optional(),
            Field::line('schema', 'Schema.org type')->optional(),
        ];
    }
}
