<?php

namespace Tests\Feature;

use App\Support\PublicLocale;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Nobody's name is written into the code.
 *
 * `plan.md` splits the "agnostic" idea in two and keeps only the half that
 * can be verified. Grepping for "any sentence a visitor could read" would
 * return the whole copy tree and prove nothing, because the copy tree *is*
 * visitor sentences and legitimately lives in the repository. Grepping for
 * the owner's identity is a different question with a real answer: the site
 * belongs to somebody, and there should be exactly two places that say so.
 *
 * Those two places are:
 *
 * - `lang/{locale}/site.php` — the settings defaults, which an operator edits
 *   from /admin/language-files without a developer;
 * - `database/seeders/data/site-settings.json` — what a fresh install seeds.
 *
 * Everything under `app/`, `config/`, `routes/`, `resources/js` and
 * `resources/views` is the machine. A fork replaces the two files above and
 * the content directory, and gets its own site.
 *
 * What is deliberately *not* searched, and why:
 *
 * - `resources/content/` is the owner's own writing. A fork replaces it
 *   wholesale; policing proper nouns inside an article would be absurd.
 * - `docs/` is documentation *about* the person, including the CV itself.
 * - `tests/` assert that the configured identity renders, which means naming
 *   it. This file reads the name from the settings rather than spelling it,
 *   so it keeps working for whoever forks the repository.
 */
class SiteIsAgnosticTest extends TestCase
{
    /**
     * The one identity-shaped string left in the machine. It is the address
     * of this project, not of its author — a fork points `SITE_REPOSITORY_URL`
     * at its own remote — and it is declared here rather than quietly skipped.
     */
    protected const DECLARED_EXCEPTIONS = [
        'config/site.php' => ['repository_url'],
    ];

    /**
     * @return array<int, string>
     */
    protected function identityTokens(): array
    {
        $tokens = [];

        foreach (PublicLocale::supported() as $locale) {
            $settings = (require lang_path("{$locale}/site.php"))['settings'];
            $name = $settings['site_identity']['name'];
            $email = $settings['contact_details']['email'];

            $tokens[] = $name;
            $tokens[] = Str::slug($name);
            $tokens[] = Str::afterLast($name, ' ');
            $tokens[] = $email;
            $tokens[] = Str::before($email, '@');
        }

        return array_values(array_unique(array_filter(
            $tokens,
            fn (string $token): bool => mb_strlen($token) > 3,
        )));
    }

    /**
     * @return array<int, string>
     */
    protected function machineFiles(): array
    {
        $roots = [
            base_path('app'),
            base_path('config'),
            base_path('routes'),
            base_path('database/migrations'),
            resource_path('js'),
            resource_path('views'),
            resource_path('css'),
        ];

        $files = [];

        foreach ($roots as $root) {
            if (! is_dir($root)) {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS),
            );

            foreach ($iterator as $file) {
                if (! $file->isFile()) {
                    continue;
                }

                if (in_array($file->getExtension(), ['php', 'vue', 'ts', 'js', 'css', 'json'], true)) {
                    $files[] = $file->getPathname();
                }
            }
        }

        // Wayfinder regenerates these from the route table on every build.
        return array_values(array_filter(
            $files,
            fn (string $path): bool => ! Str::contains(
                str_replace('\\', '/', $path),
                ['/resources/js/actions/', '/resources/js/routes/', '/resources/js/wayfinder/'],
            ),
        ));
    }

    public function test_the_owners_identity_appears_nowhere_in_the_application_code(): void
    {
        $tokens = $this->identityTokens();

        $this->assertNotEmpty(
            $tokens,
            'No identity to search for — the settings defaults are empty.',
        );

        $offenders = [];

        foreach ($this->machineFiles() as $path) {
            $relative = str_replace('\\', '/', Str::after($path, base_path().DIRECTORY_SEPARATOR));
            $contents = (string) file_get_contents($path);
            $allowed = self::DECLARED_EXCEPTIONS[$relative] ?? [];

            foreach ($tokens as $token) {
                if (! Str::contains($contents, $token, ignoreCase: true)) {
                    continue;
                }

                foreach ($allowed as $allowedKey) {
                    if (Str::contains($contents, $allowedKey)) {
                        continue 2;
                    }
                }

                $offenders[] = "{$relative} contains [{$token}]";
            }
        }

        $this->assertSame(
            [],
            $offenders,
            "The owner's identity belongs in lang/{locale}/site.php and in the "
            .'seed, not in the application code. Read it from the settings, or '
            .'declare the exception with its reason.',
        );
    }

    /**
     * The other half of the same guarantee: the settings must actually carry
     * an identity, or the test above passes for the wrong reason.
     */
    public function test_the_settings_defaults_do_carry_an_identity(): void
    {
        foreach (PublicLocale::supported() as $locale) {
            $settings = (require lang_path("{$locale}/site.php"))['settings'];

            $this->assertNotSame(
                '',
                trim($settings['site_identity']['name']),
                "lang/{$locale}/site.php carries no site name.",
            );
            $this->assertStringContainsString(
                '@',
                $settings['contact_details']['email'],
                "lang/{$locale}/site.php carries no contact email.",
            );
        }
    }
}
