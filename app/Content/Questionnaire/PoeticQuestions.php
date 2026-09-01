<?php

namespace App\Content\Questionnaire;

/**
 * The questions the site asks its owner.
 *
 * They are declared, not stored, for the same reason a page's slots are: a
 * question an operator could invent is a question the layout has nowhere to
 * put. Adding one here is a small reviewable change, and the admin grows a
 * card for it by itself.
 *
 * They are deliberately not interview questions. This site is the public
 * artifact for a profile that is neither a pure engineering portfolio nor a
 * pure design one, and a chronology of positions says only the first half of
 * that. These say the second — how the work is thought about — and they are
 * short because their answers land in a marginal note beside a spread, in a
 * micro-typographic caption that a long sentence would drown.
 *
 * The prompts live in `lang/{en,fr}/public.php` rather than here, because a
 * reader sees them: the caption under the answer is the question that
 * produced it, which is what turns a pull quote into a Q&A. `key` is what the
 * database stores and what a translation cannot drift away from.
 */
final class PoeticQuestions
{
    /**
     * Where an answer surfaces.
     *
     * One value so far. It is an enum-shaped string rather than a boolean
     * because the next surface — a note beside a case study, a line on the
     * colophon — should be an entry here rather than a second mechanism.
     */
    public const SURFACE_EXPERIENCE = 'experience';

    /**
     * In order, and the order is editorial: the first question is the one
     * that lands beside the current position.
     *
     * @return array<int, array{key: string, surface: string}>
     */
    public static function all(): array
    {
        return [
            ['key' => 'first_repair', 'surface' => self::SURFACE_EXPERIENCE],
            ['key' => 'changed_mind', 'surface' => self::SURFACE_EXPERIENCE],
            ['key' => 'owed_to_the_reader', 'surface' => self::SURFACE_EXPERIENCE],
            ['key' => 'what_a_system_says', 'surface' => self::SURFACE_EXPERIENCE],
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function keys(): array
    {
        return array_column(self::all(), 'key');
    }

    /**
     * @return array<int, array{key: string, surface: string}>
     */
    public static function surfacing(string $surface): array
    {
        return array_values(array_filter(
            self::all(),
            fn (array $question): bool => $question['surface'] === $surface,
        ));
    }

    public static function has(string $key): bool
    {
        return in_array($key, self::keys(), true);
    }

    /** The question as a reader sees it, in their language. */
    public static function prompt(string $key): string
    {
        return (string) __("public.questionnaire.{$key}.prompt");
    }

    /** The nudge an operator sees in the admin. English only, like the shell. */
    public static function hint(string $key): string
    {
        return (string) __("public.questionnaire.{$key}.hint", [], 'en');
    }
}
