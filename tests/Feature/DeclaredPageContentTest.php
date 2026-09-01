<?php

namespace Tests\Feature;

use App\Content\Schema\PageSchemas;
use App\Services\PageContentRepository;
use App\Support\PublicLocale;
use RuntimeException;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use Symfony\Component\Yaml\Yaml;
use Tests\TestCase;

/**
 * The declaration, checked against the content it describes.
 *
 * These are the tests that replace a review-time checklist. The
 * `i18n-content-parity` skill used to ask a human to compare two files for
 * shape, which is a real discipline and caught real drift — but a person
 * comparing two Markdown files does not evaluate YAML scalar resolution
 * rules, and that is exactly what shipped a JSON blob to the body copy of
 * /fr/experience.
 */
class DeclaredPageContentTest extends TestCase
{
    public function test_every_page_file_validates_against_its_declaration(): void
    {
        $failures = [];

        foreach (PageSchemas::KEYS as $key) {
            $schema = PageSchemas::for($key);

            foreach (PublicLocale::supported() as $locale) {
                $path = resource_path("content/pages/{$locale}/{$key}.md");

                if (! file_exists($path)) {
                    continue;
                }

                foreach ($schema->violations(YamlFrontMatter::parseFile($path)->matter()) as $violation) {
                    $failures[] = "{$locale}/{$key}.md: {$violation}";
                }
            }
        }

        $this->assertSame([], $failures);
    }

    /**
     * Every page key has a declaration, and every declaration has a page key.
     * Without this, adding a Markdown file and forgetting its schema would
     * simply mean the file is never validated — the quietest possible way for
     * this whole mechanism to stop being true.
     */
    public function test_the_declarations_and_the_content_directory_describe_the_same_pages(): void
    {
        $onDisk = collect(glob(resource_path('content/pages/en/*.md')))
            ->map(fn (string $path): string => basename($path, '.md'))
            ->sort()
            ->values()
            ->all();

        $declared = collect(PageSchemas::KEYS)->sort()->values()->all();

        $this->assertSame($declared, $onDisk);
    }

    /**
     * The runtime replacement for the review-time parity checklist.
     *
     * Shape, not content: the two locales must agree on which keys exist, how
     * deep they nest, and how many items every list holds. What the strings
     * say is a translator's business; that there are three of them in one
     * language and two in the other is a defect.
     */
    public function test_both_locales_resolve_to_the_same_shape_for_every_page_key(): void
    {
        $reference = PublicLocale::default();

        foreach (PageSchemas::KEYS as $key) {
            $schema = PageSchemas::for($key);
            $referencePath = resource_path("content/pages/{$reference}/{$key}.md");
            $referenceShape = $schema->shapeOf(
                YamlFrontMatter::parseFile($referencePath)->matter(),
            );

            foreach (PublicLocale::supported() as $locale) {
                if ($locale === $reference) {
                    continue;
                }

                $path = resource_path("content/pages/{$locale}/{$key}.md");

                if (! file_exists($path)) {
                    continue;
                }

                $this->assertSame(
                    $referenceShape,
                    $schema->shapeOf(YamlFrontMatter::parseFile($path)->matter()),
                    "content/pages/{$locale}/{$key}.md has drifted from the "
                    ."{$reference} shape.",
                );
            }
        }
    }

    /**
     * A field that is filled in one language and blank in the other.
     *
     * Shape equality does not catch this: `''` and `'Mémos techniques'` are
     * both strings, so both locales have the same shape and one of them shows
     * the reader nothing. Blank in *both* is a different thing and stays
     * legal — `side_projects_widget` is declared and dormant on purpose,
     * waiting for its first side project.
     */
    public function test_no_field_is_filled_in_one_locale_and_blank_in_the_other(): void
    {
        $reference = PublicLocale::default();
        $failures = [];

        foreach (PageSchemas::KEYS as $key) {
            $referenceLeaves = $this->leaves(
                YamlFrontMatter::parseFile(
                    resource_path("content/pages/{$reference}/{$key}.md"),
                )->matter(),
            );

            foreach (PublicLocale::supported() as $locale) {
                if ($locale === $reference) {
                    continue;
                }

                $path = resource_path("content/pages/{$locale}/{$key}.md");

                if (! file_exists($path)) {
                    continue;
                }

                $leaves = $this->leaves(YamlFrontMatter::parseFile($path)->matter());

                foreach ($referenceLeaves as $leafPath => $value) {
                    $other = $leaves[$leafPath] ?? null;
                    $referenceIsBlank = is_string($value) && trim($value) === '';
                    $otherIsBlank = is_string($other) && trim((string) $other) === '';

                    if ($referenceIsBlank !== $otherIsBlank) {
                        $filled = $referenceIsBlank ? $locale : $reference;
                        $blank = $referenceIsBlank ? $reference : $locale;
                        $failures[] = "{$key}.{$leafPath} is filled in {$filled} and blank in {$blank}.";
                    }
                }
            }
        }

        $this->assertSame([], $failures);
    }

    /**
     * The regression test for the defect that opened this spec.
     *
     * `fr/experience.md` held an unquoted YAML scalar containing a
     * colon-space. YAML resolved it to a one-key mapping,
     * `EditorialSpread.vue` declares `paragraphs: string[]`, and Vue
     * serialized the object into the body copy of /fr/experience. Two of the
     * forty-four keys a page can hold were checked at the time, and neither
     * was this one.
     *
     * This reconstructs that exact line and asserts the declaration rejects
     * it, naming the path. If this test ever passes with an empty violation
     * list, the mechanism has stopped working.
     */
    public function test_the_declaration_rejects_the_defect_that_shipped_to_production(): void
    {
        $frontmatter = $this->frontmatterOf(resource_path('content/pages/fr/experience.md'));

        $reintroduced = str_replace(
            '- "Côté commerce, j\'ai conçu et tenu les connecteurs entre l\'ERP, '
            .'le PIM et les catalogues marchand : création',
            '- Côté commerce, j\'ai conçu et tenu les connecteurs entre l\'ERP, '
            .'le PIM et les catalogues marchand: création',
            $frontmatter,
        );

        $this->assertNotSame(
            $frontmatter,
            $reintroduced,
            'The line this test reconstructs has changed. Update the test, or '
            .'the regression it guards is no longer being guarded.',
        );

        $violations = PageSchemas::for('experience')->violations(Yaml::parse($reintroduced));

        $this->assertContains(
            '[professional_sections.0.paragraphs.3] should be a text, got a '
            ."mapping (Côté commerce, j'ai conçu et tenu les connecteurs entre "
            ."l'ERP, le PIM et les catalogues marchand).",
            $violations,
        );
    }

    /**
     * A misspelt key is not a missing key, and the difference matters to
     * whoever has to fix it. Reporting only what is declared would say
     * `[hero] is required and missing` about a file that plainly has a hero —
     * spelt `heroo` — and send the operator looking in the wrong place.
     */
    public function test_an_undeclared_key_is_reported_rather_than_ignored(): void
    {
        $violations = PageSchemas::for('home')->violations([
            'seo_title' => 'Title',
            'seo_description' => 'Description',
            'heroo' => ['eyebrow' => 'a', 'title' => 'b', 'summary' => 'c'],
            'hero_panel' => [],
            'focus_areas' => [],
            'local_teaser' => ['title' => 'a', 'summary' => 'b', 'points' => []],
            'contact_cta' => ['title' => 'a', 'summary' => 'b'],
        ]);

        $this->assertContains('[hero] is required and missing.', $violations);
        $this->assertContains('[heroo] is not declared in the [home] schema.', $violations);
    }

    /**
     * The repository refuses the file rather than passing it through, so the
     * failure lands at the save and in CI instead of on the reader's screen.
     */
    public function test_the_repository_refuses_a_page_that_does_not_match_its_declaration(): void
    {
        $path = resource_path('content/pages/en/home.md');
        $original = file_get_contents($path);

        file_put_contents($path, str_replace(
            'seo_title:',
            "undeclared_key: surprise\nseo_title:",
            $original,
        ));

        try {
            app(PageContentRepository::class)->get('home', 'en');
            $this->fail('Expected an undeclared key to raise a runtime exception.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString(
                '[undeclared_key] is not declared in the [home] schema.',
                $exception->getMessage(),
            );
        } finally {
            file_put_contents($path, $original);
        }
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

    protected function frontmatterOf(string $path): string
    {
        preg_match('/^---\R(.*?)\R---\R?/s', (string) file_get_contents($path), $matches);

        return $matches[1];
    }
}
