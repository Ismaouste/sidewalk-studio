<?php

namespace App\Content\Schema;

use RuntimeException;

/**
 * What each page holds, declared once per page key.
 *
 * The unit is the page key, not the route and not the component. `/projects`
 * renders from two page records merged — `projects.md` supplies the hero,
 * `experience.md` the twelve other keys — and `/experience` is a 301 to it. A
 * schema attached to routes could not describe that page; a schema attached to
 * components could not describe it either, since one component reads from both.
 *
 * There is no shared block vocabulary here, and that is the finding that chose
 * the approach. Across the eight pages there are 44 distinct top-level keys.
 * Three are shared by all of them — `hero`, `seo_title`, `seo_description` —
 * and the other 41 are each used by exactly one page. These are not instances
 * of a template; they are eight bespoke editorial layouts, and the layouts are
 * the portfolio. So the layouts stay in code and the slots become declared.
 *
 * What *is* shared is shape, not vocabulary: a hero is three fields in the
 * same order on all eight pages, and four widgets agree on four more. Those
 * are factored below, so the declaration reads as a design rather than as a
 * dump of everything the files happen to contain.
 *
 * Types are chosen semantically, not by measuring the English string. A
 * threshold on length flips between locales — an 85-character English line is
 * a 95-character French one — and would make the declaration a description of
 * one translation rather than of the content model.
 */
final class PageSchemas
{
    public const KEYS = [
        'home',
        'projects',
        'experience',
        'local',
        'contact',
        'sparkle',
        'colophon',
        'data-processing',
    ];

    /**
     * Which page keys each route composes. `/projects` is the only route that
     * merges two, and the reason the unit of declaration is the key.
     */
    public const ROUTE_COMPOSITION = [
        'home' => ['home'],
        'projects' => ['projects', 'experience'],
        'local' => ['local'],
        'contact' => ['contact'],
        'sparkle' => ['sparkle'],
        'colophon' => ['colophon'],
        'data-processing' => ['data-processing'],
    ];

    /**
     * The fields a page stores in columns rather than in its JSON payload.
     *
     * Declared once and read by both sides: the repository splits on it when
     * reassembling a save for validation, and the editor splits on it when
     * deciding which half of the form a field belongs to. Two copies of this
     * list would be two ways for the form to offer a field the save then
     * misplaces.
     */
    public const META_FIELDS = [
        'seo_title',
        'seo_description',
        'title',
        'description',
        'robots',
        'canonical_url',
        'open_graph_image',
    ];

    /**
     * The public path a page key is read at.
     *
     * Not the inverse of `ROUTE_COMPOSITION`, quite: `/projects` composes two
     * keys, so both `projects` and `experience` are previewed there. It is
     * also why `/experience` is a 301 rather than a page — the record it is
     * named after has no route of its own.
     */
    public static function routeFor(string $key): string
    {
        foreach (self::ROUTE_COMPOSITION as $route => $composedKeys) {
            if (in_array($key, $composedKeys, true)) {
                return $route === 'home' ? '/' : "/{$route}";
            }
        }

        throw new RuntimeException("No route composes the page key [{$key}].");
    }

    public static function for(string $key): ContentSchema
    {
        return match ($key) {
            'home' => self::home(),
            'projects' => self::projects(),
            'experience' => self::experience(),
            'local' => self::local(),
            'contact' => self::contact(),
            'sparkle' => self::sparkle(),
            'colophon' => self::colophon(),
            'data-processing' => self::dataProcessing(),
            default => throw new RuntimeException("No page schema declared for [{$key}]."),
        };
    }

    /**
     * @return array<string, ContentSchema>
     */
    public static function all(): array
    {
        $schemas = [];

        foreach (self::KEYS as $key) {
            $schemas[$key] = self::for($key);
        }

        return $schemas;
    }

    public static function home(): ContentSchema
    {
        return new ContentSchema('home', 'Home', [
            ...self::meta(),
            self::hero(),
            Field::text('hero_panel', 'Hero panel')->repeating(),
            Field::group('focus_areas', [
                Field::line('label', 'Label'),
                Field::line('title', 'Title'),
                Field::text('summary', 'Summary'),
                Field::url('href', 'Link'),
                Field::line('cta', 'Call to action'),
                Field::line('tone', 'Tone'),
            ], 'Focus areas')->repeating(itemLabel: 'title'),
            Field::group('local_teaser', [
                Field::line('title', 'Title'),
                Field::text('summary', 'Summary'),
                Field::text('points', 'Points')->repeating(),
            ], 'Local teaser'),
            Field::group('contact_cta', [
                Field::line('title', 'Title'),
                Field::text('summary', 'Summary'),
            ], 'Contact call to action'),
        ]);
    }

    /**
     * Half of `/projects`. It supplies the hero and the two section framings;
     * `experience` supplies everything below them.
     */
    public static function projects(): ContentSchema
    {
        return new ContentSchema('projects', 'Projects', [
            ...self::meta(),
            self::hero(),
            Field::group('tracks_section', [
                Field::line('label', 'Label'),
                self::intro(),
                Field::group('items', [
                    Field::line('title', 'Title'),
                    Field::text('summary', 'Summary'),
                ], 'Tracks')->repeating(itemLabel: 'title'),
            ], 'Tracks section'),
            Field::group('case_studies_section', [
                Field::line('label', 'Label'),
                Field::line('eyebrow', 'Eyebrow'),
                Field::line('title', 'Title'),
                Field::text('summary', 'Summary'),
                Field::line('archive_cta', 'Archive link'),
            ], 'Case studies section'),
        ]);
    }

    /**
     * The heaviest page key by some distance — 64 leaf fields — and the reason
     * the generated editor defaults to a sectioned form rather than a wizard.
     * Walking 64 questions to fix one typo is worse than a form in every way.
     */
    public static function experience(): ContentSchema
    {
        return new ContentSchema('experience', 'Experience record', [
            ...self::meta(),
            self::hero(),
            Field::text('thesis', 'Thesis'),
            Field::text('positioning', 'Positioning')->repeating(),
            Field::text('contexts', 'Contexts')->repeating(),

            /**
             * The three section families are one shape used three times. Two
             * carry content today and `side_project_sections` is declared and
             * empty — a repeating field with no items is a valid state, and
             * declaring it is what lets an operator add the first one from the
             * admin without a developer touching the schema.
             */
            self::experienceSections('professional_sections', 'Professional sections'),
            self::widget('associative_note_widget', 'Associative note widget'),
            self::experienceSections('side_project_sections', 'Side project sections'),
            self::widget('side_projects_widget', 'Side projects widget'),
            self::experienceSections('associative_sections', 'Associative sections'),

            Field::group('trajectory', [
                Field::line('title', 'Title'),
                Field::text('summary', 'Summary'),
            ], 'Trajectory')->repeating(itemLabel: 'title'),
            Field::text('strengths', 'Strengths')->repeating(),
            Field::group('focus_areas', [
                Field::line('title', 'Title'),
                Field::text('summary', 'Summary'),
            ], 'Focus areas')->repeating(itemLabel: 'title'),
            Field::group('stack_groups', [
                Field::line('title', 'Title'),
                Field::line('items', 'Items')->repeating(),
            ], 'Stack groups')->repeating(itemLabel: 'title'),
            Field::group('career_snapshot', [
                Field::line('title', 'Title'),
                Field::text('summary', 'Summary'),
                Field::text('roles', 'Roles')->repeating(),
            ], 'Career snapshot'),
            Field::text('looking_for', 'Looking for'),
            Field::line('hobbies', 'Hobbies')->repeating(),
        ]);
    }

    public static function local(): ContentSchema
    {
        return new ContentSchema('local', 'Local', [
            ...self::meta(),
            self::hero(),
            Field::text('signals', 'Signals')->repeating(),
            Field::group('nancy', [
                Field::text('body', 'Body')->repeating(),
            ], 'Nancy'),
            self::intro('journal_section', 'Journal section'),
            self::intro('engagements_intro', 'Engagements intro'),
            Field::group('engagements', [
                Field::line('title', 'Title'),
                Field::text('summary', 'Summary'),
                Field::text('items', 'Items')->repeating(),
            ], 'Engagements')->repeating(itemLabel: 'title'),
            self::intro('notes_section', 'Notes section'),
        ]);
    }

    public static function contact(): ContentSchema
    {
        return new ContentSchema('contact', 'Contact', [
            ...self::meta(),
            self::hero(),
            Field::group('form', [
                Field::line('eyebrow', 'Eyebrow'),
                Field::line('title', 'Title'),
                Field::text('summary', 'Summary'),
                Field::line('name_label', 'Name label'),
                Field::line('name_placeholder', 'Name placeholder'),
                Field::line('email_label', 'Email label'),
                Field::line('email_placeholder', 'Email placeholder'),
                Field::line('company_label', 'Company label'),
                Field::line('company_placeholder', 'Company placeholder'),
                Field::line('summary_label', 'Message label'),
                Field::line('summary_placeholder', 'Message placeholder'),
                Field::text('summary_meta', 'Message hint'),
                Field::line('primary_cta', 'Primary button'),
                Field::line('secondary_cta', 'Secondary button'),
            ], 'Form'),
            Field::group('details', [
                Field::line('eyebrow', 'Eyebrow'),
                Field::line('email_label', 'Email label'),
                Field::line('location_label', 'Location label'),
                Field::line('availability_label', 'Availability label'),
            ], 'Details'),
            Field::group('services', [
                Field::line('eyebrow', 'Eyebrow'),
                Field::group('items', [
                    Field::line('title', 'Title'),
                    Field::text('summary', 'Summary'),
                ], 'Services')->repeating(itemLabel: 'title'),
            ], 'Services'),
            Field::group('recruiter_shortcut', [
                Field::line('eyebrow', 'Eyebrow'),
                Field::text('summary', 'Summary'),
            ], 'Recruiter shortcut'),
        ]);
    }

    public static function sparkle(): ContentSchema
    {
        return new ContentSchema('sparkle', 'Sparkle', [
            ...self::meta(),
            self::hero(),
            Field::group('project', [
                Field::line('title', 'Title'),
                Field::text('summary', 'Summary'),
                Field::text('points', 'Points')->repeating(),
            ], 'Project'),
            Field::group('controls', [
                Field::line('theme_button_on', 'Theme button, on'),
                Field::line('theme_button_off', 'Theme button, off'),
                Field::line('loader_button', 'Loader button'),
                Field::line('repo_button', 'Repository button'),
                Field::line('profile_button', 'Profile button'),
                Field::text('palette_note', 'Palette note'),
                Field::text('loader_note', 'Loader note'),
            ], 'Controls'),
            Field::url('repo_url', 'Repository URL'),
            Field::text('cosmic_notes', 'Cosmic notes')->repeating(),
            Field::group('sparkle_facts', [
                Field::line('label', 'Label'),
                Field::line('value', 'Value'),
            ], 'Sparkle facts')->repeating(itemLabel: 'label'),
        ]);
    }

    public static function colophon(): ContentSchema
    {
        return new ContentSchema('colophon', 'Colophon', [
            ...self::meta(),
            self::hero(),
            Field::group('sections', [
                Field::line('title', 'Title'),
                Field::line('eyebrow', 'Eyebrow'),
                Field::text('summary', 'Summary'),
                Field::line('cta_label', 'Link label'),
                Field::url('cta_href', 'Link'),
            ], 'Sections')->repeating(itemLabel: 'title'),
            Field::group('closing', [
                Field::line('eyebrow', 'Eyebrow'),
                Field::line('title', 'Title'),
                Field::text('summary', 'Summary'),
                Field::line('cta_label', 'Link label'),
                Field::url('cta_href', 'Link'),
            ], 'Closing'),
        ]);
    }

    public static function dataProcessing(): ContentSchema
    {
        return new ContentSchema('data-processing', 'Data processing', [
            ...self::meta(),
            self::hero(),
            Field::group('storage', [
                Field::line('eyebrow', 'Eyebrow'),
                Field::line('title', 'Title'),
                Field::text('points', 'Points')->repeating(),
            ], 'Storage'),
            Field::group('consent', [
                Field::line('eyebrow', 'Eyebrow'),
                Field::line('title', 'Title'),
                Field::text('points', 'Points')->repeating(),
            ], 'Consent'),
            Field::group('operator', [
                Field::line('eyebrow', 'Eyebrow'),
                Field::line('title', 'Title'),
                Field::text('summary', 'Summary'),
            ], 'Operator'),
        ]);
    }

    /**
     * The metadata every page carries.
     *
     * Only the two SEO fields are required, which is not a softening — it is
     * what the repository already does. `title`, `description`, `robots`,
     * `canonical_url` and `open_graph_image` all have defaults there, and
     * exactly one page (`sparkle`) overrides `robots` today. Declaring them
     * required would fail seven valid files to describe an intention nobody
     * has.
     *
     * @return array<int, Field>
     */
    protected static function meta(): array
    {
        return [
            Field::line('seo_title', 'SEO title'),
            Field::text('seo_description', 'SEO description'),
            Field::line('title', 'Title')->optional(),
            Field::text('description', 'Description')->optional(),
            Field::line('robots', 'Robots')->optional()
                ->withHelp('Defaults to index,follow.'),
            Field::url('canonical_url', 'Canonical URL')->optional(),
            Field::url('open_graph_image', 'Social image')->optional(),
        ];
    }

    /**
     * Three fields in the same order on all eight pages. One of the three keys
     * every page shares.
     */
    protected static function hero(): Field
    {
        return Field::group('hero', [
            Field::line('eyebrow', 'Eyebrow'),
            Field::line('title', 'Title'),
            Field::text('summary', 'Summary'),
        ], 'Hero');
    }

    /** The same three fields, introducing a section rather than a page. */
    protected static function intro(string $name = 'intro', string $label = 'Intro'): Field
    {
        return Field::group($name, [
            Field::line('eyebrow', 'Eyebrow'),
            Field::line('title', 'Title'),
            Field::text('summary', 'Summary'),
        ], $label);
    }

    /** The framing around a publication feed, as the page states it. */
    protected static function widget(string $name, string $label): Field
    {
        return Field::group($name, [
            Field::line('eyebrow', 'Eyebrow'),
            Field::line('title', 'Title'),
            Field::text('description', 'Description'),
            Field::line('cta_label', 'Link label'),
        ], $label);
    }

    /**
     * One shape, used by the three section families on the experience record.
     * The heaviest repeating group on the site: three items times five fields
     * plus nested `detail_groups`. Every item has a `title`, which is what the
     * editor collapses each one into.
     */
    protected static function experienceSections(string $name, string $label): Field
    {
        return Field::group($name, [
            Field::line('title', 'Title'),
            Field::line('eyebrow', 'Eyebrow'),
            Field::text('summary', 'Summary'),
            Field::text('paragraphs', 'Paragraphs')->repeating(),
            Field::group('detail_groups', [
                Field::line('title', 'Title'),
                Field::line('pills', 'Pills')->repeating()->optional(),
                Field::text('items', 'Items')->repeating(),
            ], 'Detail groups')->repeating(itemLabel: 'title'),
        ], $label)->repeating(itemLabel: 'title');
    }
}
