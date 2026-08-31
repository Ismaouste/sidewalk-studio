<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * The active navigation entry is resolved server-side and shared through
 * Inertia. These tests pin the matching rule, because the client no longer
 * has its own copy of it to fall back on.
 */
class PublicNavigationStateTest extends TestCase
{
    public function test_home_is_active_only_on_home(): void
    {
        $this->assertSame(['/'], $this->activePaths('/en'));
    }

    public function test_a_section_stays_active_on_its_articles(): void
    {
        $this->assertSame(['/journal'], $this->activePaths('/en/journal'));
        $this->assertSame(
            ['/journal'],
            $this->activePaths('/en/journal/content-systems-routing-and-metadata'),
        );
    }

    public function test_a_page_outside_the_navigation_lights_nothing_up(): void
    {
        $this->assertSame([], $this->activePaths('/en/colophon'));
    }

    public function test_the_rule_is_locale_independent(): void
    {
        $this->assertSame(['/journal'], $this->activePaths('/fr/journal'));
        $this->assertSame(['/projects'], $this->activePaths('/fr/projects'));
    }

    public function test_hrefs_stay_localized_while_paths_stay_bare(): void
    {
        $entries = collect($this->navigationFor('/fr/projects'))
            ->keyBy('path');

        $this->assertSame('/fr', $entries['/']['href']);
        $this->assertSame('/fr/journal', $entries['/journal']['href']);
        $this->assertSame('Journal', $entries['/journal']['label']);
    }

    /**
     * @return array<int, string>
     */
    private function activePaths(string $url): array
    {
        return collect($this->navigationFor($url))
            ->where('active', true)
            ->pluck('path')
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{label: string, href: string, path: string, active: bool}>
     */
    private function navigationFor(string $url): array
    {
        $response = $this->get($url);
        $response->assertOk();

        return $response->viewData('page')['props']['site']['navigation'];
    }
}
