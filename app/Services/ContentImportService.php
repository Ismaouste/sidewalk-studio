<?php

namespace App\Services;

class ContentImportService
{
    public function __construct(
        protected ContentRepository $content,
        protected PageContentRepository $pages,
    ) {}

    public function importAll(): void
    {
        foreach (['en', 'fr'] as $locale) {
            $groups = $this->content->adminIndex($locale);

            foreach (['note', 'journal', 'case_study'] as $type) {
                foreach ($groups[$type] as $publication) {
                    if (($publication['source_driver'] ?? 'file') !== 'file') {
                        continue;
                    }

                    $this->content->importPublication($publication);
                }
            }

            foreach ($this->pages->adminList($locale) as $page) {
                if (($page['source_driver'] ?? 'file') !== 'file') {
                    continue;
                }

                $this->pages->savePage($page['page_key'], $page['locale'], $page);
            }
        }
    }
}
