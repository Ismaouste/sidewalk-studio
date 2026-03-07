<?php

namespace Tests\Feature;

use App\Services\ContentRepository;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Tests\TestCase;

class ContentRepositoryTest extends TestCase
{
    public function test_repository_returns_published_writing_entries(): void
    {
        $items = app(ContentRepository::class)->published('writing');

        $this->assertCount(2, $items);
        $this->assertContains('content-systems-routing-and-metadata', $items->pluck('slug')->all());
        $this->assertTrue($items->every(fn (array $item) => $item['status'] === 'published'));
    }

    public function test_repository_returns_case_study_details(): void
    {
        $item = app(ContentRepository::class)->findPublished('case-studies', 'repo-bootstrap-foundation');

        $this->assertSame('Sidewalk Studio', $item['client']);
        $this->assertContains('Laravel 12', $item['stack']);
        $this->assertStringContainsString('The first iteration of Sidewalk Studio started from a mismatch', $item['body_html']);
    }

    public function test_repository_raises_a_test_visible_failure_for_invalid_frontmatter(): void
    {
        $path = resource_path('content/writing/invalid-frontmatter.md');

        File::put($path, <<<'MD'
---
title: Invalid frontmatter entry
slug: invalid-frontmatter-entry
summary: This file intentionally misses updated_at.
status: published
published_at: 2026-03-07
tags:
    - test
seo_title: Invalid frontmatter entry
seo_description: This file is used to prove invalid frontmatter fails fast.
---

This entry should never be parsed successfully.
MD);

        try {
            app(ContentRepository::class)->all('writing');
            $this->fail('Expected invalid frontmatter to raise a runtime exception.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString(
                'Missing required frontmatter field [updated_at]',
                $exception->getMessage(),
            );
        } finally {
            File::delete($path);
        }
    }
}
