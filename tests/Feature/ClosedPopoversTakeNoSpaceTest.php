<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * A closed popover must not be laid out.
 *
 * The UA sheet gives every `[popover]` `display: none` until it is open. That
 * rule is UA-origin, so **any** author declaration of `display` on the panel's
 * own class beats it — and the panel keeps a full box while closed. It is
 * invisible, because these panels also animate from `opacity: 0`, so nothing
 * looks broken; it simply sits over the page and swallows every click inside
 * its rectangle.
 *
 * That is not a hypothetical. `.accessibility-panel__popover` declared
 * `display: grid` in its base rule, which left a 304x212 box over the footer
 * on every page: the "Accessibilité" and "Réglages vie privée" buttons were
 * both inside it and neither could be clicked. `.nav-tabs__panel` had the same
 * declaration, leaving a 373x190 box under the header on every page below
 * 960px.
 *
 * The fix is to declare `display` only where the panel is meant to occupy
 * space — `:popover-open`, or a media query that deliberately turns the sheet
 * into something else. This test pins the rule that makes the fix stay: the
 * base rule of a popover panel says nothing about `display`, so the UA's
 * `display: none` survives to do its job.
 */
class ClosedPopoversTakeNoSpaceTest extends TestCase
{
    /**
     * @return array<int, string>
     */
    protected function componentsWithPopovers(): array
    {
        $found = [];

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(resource_path('js'))
        );

        foreach ($files as $file) {
            if (! $file->isFile() || $file->getExtension() !== 'vue') {
                continue;
            }

            $source = (string) file_get_contents($file->getPathname());

            // The attribute on its own line is how a popover element is
            // written here; `popovertarget` on a trigger is not one.
            if (preg_match('/^\s*popover\s*$/m', $source) === 1) {
                $found[] = $file->getPathname();
            }
        }

        return $found;
    }

    public function test_a_popover_panel_never_declares_display_in_its_base_rule(): void
    {
        $components = $this->componentsWithPopovers();

        $this->assertNotEmpty(
            $components,
            'No popover components were found, so this test is not checking anything.',
        );

        foreach ($components as $path) {
            $source = (string) file_get_contents($path);
            $name = basename($path);

            /**
             * Top-level rules only: a selector starting at column zero. Rules
             * inside `@media` or `@starting-style` are indented by Prettier,
             * and those are exactly the places a deliberate override lives —
             * the desktop block in `NavTabs` turns the sheet into the tab row
             * on purpose.
             */
            preg_match_all(
                '/^(\.[a-z0-9_-]+__(?:popover|panel|sheet|menu))\s*\{([^}]*)\}/mi',
                $source,
                $matches,
                PREG_SET_ORDER,
            );

            $this->assertNotEmpty(
                $matches,
                "[{$name}] renders a popover but no base rule for its panel was found; the naming this test relies on has drifted.",
            );

            foreach ($matches as [, $selector, $body]) {
                $this->assertDoesNotMatchRegularExpression(
                    '/^\s*display\s*:/m',
                    $body,
                    "[{$name}] declares `display` in the base rule for [{$selector}]. "
                    ."That beats the UA's `display: none` for a closed popover, so the panel keeps its "
                    .'box while closed and swallows every click inside it. Declare `display` on '
                    .':popover-open, or inside the media query that deliberately changes what the panel is.',
                );
            }
        }
    }

    /**
     * The other half of the same rule: something has to give the panel a
     * display when it *is* open, or opening it would show nothing.
     */
    public function test_a_popover_panel_declares_its_display_when_open(): void
    {
        foreach ($this->componentsWithPopovers() as $path) {
            $source = (string) file_get_contents($path);
            $name = basename($path);

            $this->assertMatchesRegularExpression(
                '/:popover-open\s*\{[^}]*\bdisplay\s*:/s',
                $source,
                "[{$name}] never declares a `display` for its open popover, so opening it lays out nothing.",
            );
        }
    }
}
