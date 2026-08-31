<?php

namespace App\Services;

use App\Content\Schema\PageSchemas;
use App\Support\PublicLocale;

/**
 * Seeds the database from the Markdown files.
 *
 * Every read here is explicitly file-backed, and that is the whole design
 * rather than an implementation detail. This service used to read
 * `adminIndex()` and `adminList()`, which return whichever source currently
 * wins — correct while Markdown always won, and silently wrong the moment the
 * database did. Re-seeding would then have read the database and written it
 * straight back, preserving exactly the edits it was meant to overwrite.
 *
 * The failure had no symptom: the seeder reported success, the row count was
 * right, and nothing was seeded. It was found by editing a page in the
 * database, re-running the seeder, and noticing the edit survive.
 */
class ContentImportService
{
    public function __construct(
        protected ContentRepository $content,
        protected PageContentRepository $pages,
    ) {}

    public function importAll(): void
    {
        foreach (PublicLocale::supported() as $locale) {
            foreach ($this->content->fileBackedItems($locale) as $publication) {
                $this->content->importPublication($publication);
            }

            foreach (PageSchemas::KEYS as $pageKey) {
                $page = $this->pages->seededPage($pageKey, $locale);

                if ($page === null) {
                    continue;
                }

                $this->pages->savePage($pageKey, $locale, $page);
            }
        }
    }
}
