<?php

namespace Tests\Feature;

use App\Models\Publication;
use App\Services\ContentRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class PublicationPersistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_saving_a_publication_persists_metadata_and_writes_the_markdown_body(): void
    {
        $repository = app(ContentRepository::class);
        $path = resource_path('content/writing/en/hybrid-admin-save-test.md');

        try {
            $saved = $repository->savePublication([
                'publication_type' => 'journal',
                'locale' => 'en',
                'title' => 'Hybrid Admin Save Test',
                'slug' => 'hybrid-admin-save-test',
                'summary' => 'A publication saved from admin should keep metadata in the database and the body in markdown.',
                'status' => 'published',
                'published_at' => '2026-03-09',
                'updated_at' => '2026-03-09',
                'tags' => ['testing', 'hybrid'],
                'seo_title' => 'Hybrid Admin Save Test',
                'seo_description' => 'This entry proves publication saves write markdown bodies and database metadata.',
                'robots' => 'index,follow',
                'canonical_url' => '',
                'featured_image' => '',
                'featured_image_alt' => '',
                'open_graph_image' => '',
                'featured_video' => '',
                'category' => 'journal',
                'accent_tone' => 'dominant',
                'source_path' => $path,
                'body_markdown' => "# Hybrid save\n\nThis body should live in the markdown file.",
                'metadata' => [
                    'client' => '',
                    'role' => '',
                    'stack' => [],
                    'outcomes' => [],
                ],
            ]);

            $this->assertSame($path, $saved['source_path']);
            $this->assertFileExists($path);
            $this->assertStringContainsString('This body should live in the markdown file.', File::get($path));

            $record = Publication::query()->where('slug', 'hybrid-admin-save-test')->firstOrFail();
            $this->assertSame('Hybrid Admin Save Test', $record->title);
            $this->assertSame('', $record->body_markdown);

            $normalized = $repository->adminFind('journal', 'en', 'hybrid-admin-save-test');
            $this->assertStringContainsString('This body should live in the markdown file.', $normalized['body_markdown']);
            $this->assertSame('published', $normalized['status']);
        } finally {
            File::delete($path);
        }
    }
}
