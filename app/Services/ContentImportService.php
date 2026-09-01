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
        protected ExperienceEntryRepository $experience,
    ) {}

    public function importAll(): void
    {
        $experiencePayloads = [];

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

                if ($pageKey === 'experience') {
                    $experiencePayloads[$locale] = $page['payload'] ?? [];
                }
            }
        }

        /**
         * The experience rows are filed from the same file-backed payload the
         * page was just seeded with, and for the same reason the rest of this
         * service insists on files: seeding from whichever source currently
         * wins would read the database and write it back, preserving exactly
         * the edits it exists to overwrite.
         *
         * It runs after the loop rather than inside it because the two
         * locales are paired by position, so both have to be in hand before
         * either can be given a translation key.
         */
        $this->experience->seedFromPayloads($experiencePayloads);
    }
}
