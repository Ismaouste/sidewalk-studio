<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Motion and contrast follow the operating system until the visitor overrides
 * them, and they do so before the first paint.
 *
 * Two defects sat behind this, and only the first had been written down.
 *
 * `useAccessibilityPreferences` resolved the stored motion value with
 *
 *     readStoredPreference(MOTION_STORAGE_KEY) === 'reduced' ? 'reduced' : 'full'
 *
 * which tests for one value and lets everything else fall to `full` --
 * including `null`. A visitor who had set reduced motion in their operating
 * system and never touched the site's own switch was therefore written
 * `data-motion="full"`, and the panel reported motion as on to someone whose
 * system said the opposite. Four rules key on that attribute with no
 * `prefers-reduced-motion` query beside them to rescue them: the consent
 * dialog's transitions, the contact tab's `::after` animation, the
 * breadcrumb's stuck state, and -- in JavaScript, where no media query can
 * reach -- the footer's scroll-to-top, which chose `behavior: 'smooth'`.
 *
 * The second defect is that fixing the composable alone would not have been
 * enough. It runs at hydration, so the attribute would land after the first
 * paint, which is after the animations it exists to prevent have started.
 * The theme already had an inline script in the document head for exactly
 * this reason; motion and contrast did not, and now share it.
 *
 * The precedence is the part worth pinning: an explicit stored choice wins in
 * *both* directions. Someone who turned motion back on despite their system
 * setting asked for it, and testing for a single value cannot tell that
 * deliberate opt-out from a visitor who has never expressed one.
 *
 * The suite has no browser, so what follows pins the shape of the fix rather
 * than the resolved value, in the manner of `PageTitleIsComposedOnceTest`.
 */
class AccessibilityPreferencesFollowTheSystemTest extends TestCase
{
    public function test_the_document_seeds_all_three_preferences_before_hydration(): void
    {
        $html = $this->get('/en')->assertOk()->getContent();

        $head = substr($html, 0, (int) strpos($html, '</head>'));

        // Whitespace-insensitive: the formatter is free to break any of these
        // calls across lines, and this test is about the boot script's
        // behaviour rather than its layout.
        $dense = preg_replace('/\s+/', '', $head) ?? '';

        foreach ([
            'data-theme',
            'data-motion',
            'data-contrast',
        ] as $attribute) {
            $this->assertStringContainsString(
                "setAttribute('{$attribute}',",
                $dense,
                "The boot script must set {$attribute} before the first paint."
            );
        }

        foreach ([
            '(prefers-color-scheme: dark)',
            '(prefers-reduced-motion: reduce)',
            '(prefers-contrast: more)',
        ] as $query) {
            $this->assertStringContainsString(
                $query,
                $head,
                "The boot script must fall back to {$query} when nothing is stored."
            );
        }

        foreach ([
            'sidewalk-theme',
            'sidewalk-accessibility-motion',
            'sidewalk-accessibility-contrast',
        ] as $key) {
            $this->assertStringContainsString($key, $head);
        }
    }

    public function test_the_composable_consults_the_system_rather_than_defaulting(): void
    {
        $source = file_get_contents(
            base_path('resources/js/composables/useAccessibilityPreferences.ts')
        );

        $this->assertIsString($source);

        foreach ([
            '(prefers-reduced-motion: reduce)',
            '(prefers-contrast: more)',
        ] as $query) {
            $this->assertStringContainsString(
                $query,
                $source,
                "An absent stored preference must fall through to {$query}."
            );
        }

        // The exact shape of the defect: a single-value test collapses `null`
        // and a deliberate opt-out into the same branch.
        foreach ([
            "=== 'reduced'\n            ? 'reduced'",
            "=== 'boost'\n            ? 'boost'",
        ] as $collapsed) {
            $this->assertStringNotContainsString(
                str_replace("\n", "\n", $collapsed),
                $source,
                'Resolve the preference against both of its values, not one.'
            );
        }
    }
}
