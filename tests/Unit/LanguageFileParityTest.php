<?php

namespace Tests\Unit;

use App\Support\PublicCopy;
use App\Support\PublicLocale;
use RuntimeException;
use Tests\TestCase;

/**
 * The runtime replacement for a compile-time guarantee.
 *
 * `resources/js/copy/` gets its parity from TypeScript: every French module
 * ends in `satisfies typeof import('../en/…').default`, so a key present in
 * one locale only is a build error at the file that drifted. The editorial
 * copy the server resolves cannot use that mechanism — it is PHP, read at
 * request time — and before this it had no guarantee at all: it lived as
 * `app()->getLocale() === 'fr' ? … : …` ternaries, where the two branches are
 * two independent expressions and nothing relates them.
 *
 * These tests are what makes the move out of the ternaries a fair trade
 * rather than a loss. The constraint in `plan.md` is explicit about it: where
 * a compile-time guarantee becomes a runtime one, the check exists and is
 * tested.
 */
class LanguageFileParityTest extends TestCase
{
    /**
     * @return array<int, string>
     */
    protected function managedFiles(): array
    {
        return ['public', 'site'];
    }

    public function test_every_language_file_has_the_same_key_tree_in_both_locales(): void
    {
        foreach ($this->managedFiles() as $file) {
            $reference = $this->flatten(
                require lang_path(PublicLocale::default()."/{$file}.php"),
            );

            foreach (PublicLocale::supported() as $locale) {
                if ($locale === PublicLocale::default()) {
                    continue;
                }

                $translated = $this->flatten(require lang_path("{$locale}/{$file}.php"));

                $this->assertSame(
                    [],
                    array_values(array_diff($reference, $translated)),
                    "Keys missing from lang/{$locale}/{$file}.php.",
                );

                $this->assertSame(
                    [],
                    array_values(array_diff($translated, $reference)),
                    "Keys present in lang/{$locale}/{$file}.php but not in the reference locale.",
                );
            }
        }
    }

    public function test_no_translated_value_is_left_empty(): void
    {
        foreach ($this->managedFiles() as $file) {
            foreach (PublicLocale::supported() as $locale) {
                $payload = require lang_path("{$locale}/{$file}.php");

                foreach ($this->leaves($payload) as $key => $value) {
                    $this->assertIsString(
                        $value,
                        "lang/{$locale}/{$file}.php key [{$key}] is not a string.",
                    );
                    $this->assertNotSame(
                        '',
                        trim($value),
                        "lang/{$locale}/{$file}.php key [{$key}] is empty.",
                    );
                }
            }
        }
    }

    /**
     * A French value identical to its English counterpart is usually an
     * untranslated placeholder rather than a deliberate choice, so the ones
     * that are deliberate have to say so here. Everything on this list is a
     * proper noun, a brand, or a word that is genuinely the same in both.
     */
    public function test_values_shared_between_locales_are_declared_rather_than_accidental(): void
    {
        $deliberatelyIdentical = [
            'navigation./',
            'navigation./journal',
            'navigation./contact',
            'breadcrumbs.journal',
            'breadcrumbs.labs',
            'breadcrumbs.sparkle',
            'sections.journal',
            'seo.labs.title',
            'shell.nav_menu_label',
            'shell.nav_fallback_label',
            'widgets.home_journal.eyebrow',
            'widgets.projects_notes.eyebrow',
        ];

        $english = $this->leaves(require lang_path('en/public.php'));
        $french = $this->leaves(require lang_path('fr/public.php'));

        $identical = array_keys(array_filter(
            $english,
            fn (string $value, string $key): bool => ($french[$key] ?? null) === $value,
            ARRAY_FILTER_USE_BOTH,
        ));

        $this->assertSame(
            [],
            array_values(array_diff($identical, $deliberatelyIdentical)),
            'These French values are identical to the English ones. Translate '
            .'them, or add them to the deliberate list with a reason.',
        );

        $this->assertSame(
            [],
            array_values(array_diff($deliberatelyIdentical, $identical)),
            'These keys are listed as deliberately identical but no longer are. '
            .'Remove them from the list.',
        );
    }

    public function test_public_copy_throws_on_a_missing_key_rather_than_rendering_it(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing public copy key [public.seo.nope.title]');

        PublicCopy::line('seo.nope.title', 'en');
    }

    public function test_public_copy_refuses_to_read_a_group_as_a_line(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('is a group, not a line');

        PublicCopy::line('seo.journal', 'en');
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<int, string>
     */
    protected function flatten(array $payload, string $prefix = ''): array
    {
        return array_keys($this->leaves($payload, $prefix));
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    protected function leaves(array $payload, string $prefix = ''): array
    {
        $leaves = [];

        foreach ($payload as $key => $value) {
            $path = $prefix === '' ? (string) $key : "{$prefix}.{$key}";

            if (is_array($value)) {
                $leaves += $this->leaves($value, $path);

                continue;
            }

            $leaves[$path] = $value;
        }

        return $leaves;
    }
}
