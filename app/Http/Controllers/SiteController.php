<?php

namespace App\Http\Controllers;

use App\Content\Questionnaire\PoeticQuestions;
use App\Services\ContentRepository;
use App\Services\ExperienceEntryRepository;
use App\Services\PageContentRepository;
use App\Services\QuestionnaireRepository;
use App\Services\SiteSettingsService;
use App\Support\CareerAsset;
use App\Support\PublicCopy;
use App\Support\PublicLocale;
use App\Support\Seo;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SiteController extends Controller
{
    public function __construct(
        protected ContentRepository $content,
        protected PageContentRepository $pages,
        protected SiteSettingsService $siteSettings,
        protected ExperienceEntryRepository $experienceEntries,
        protected QuestionnaireRepository $questionnaire,
    ) {}

    /**
     * The three section families, from the rows when there are rows.
     *
     * The payload is not a legacy path to be removed later. Production ships
     * no SQLite and every database entry point here is guarded, so on Vercel
     * this falls through to exactly what the page has always served. The rows
     * win where they exist because that is where the dates are, and the dates
     * are what put the chronology in order without anyone maintaining it.
     *
     * @param  array<string, mixed>  $experience
     * @return array<string, array<int, array<string, mixed>>>
     */
    protected function experienceSections(array $experience): array
    {
        $locale = app()->getLocale();

        $sections = $this->experienceEntries->hasEntries($locale)
            ? $this->experienceEntries->sectionsFor($locale)
            : [
                'professional' => $experience['professional_sections'],
                'side_project' => $experience['side_project_sections'],
                'associative' => $experience['associative_sections'],
            ];

        return $this->withMarginalia($sections, $locale);
    }

    /**
     * The answered questions, laid beside the spreads.
     *
     * `EditorialSpread` has carried a marginalia slot since it was written —
     * an italic display quote over a micro-typographic caption — and nothing
     * has ever filled it. The declared schema would in fact refuse a
     * `marginalia` key in the page payload, because it is not declared there.
     * So the slot has been waiting for a source that is not the content: the
     * questionnaire is that source.
     *
     * The pairing is positional and the declaration owns the order, so the
     * first question lands beside the most recent position. Unanswered
     * questions are skipped rather than left as holes, and a spread with no
     * note renders exactly as it does today.
     *
     * @param  array<string, array<int, array<string, mixed>>>  $sections
     * @return array<string, array<int, array<string, mixed>>>
     */
    protected function withMarginalia(array $sections, string $locale): array
    {
        $notes = $this->questionnaire->marginaliaFor(
            PoeticQuestions::SURFACE_EXPERIENCE,
            $locale,
        );

        if ($notes === []) {
            return $sections;
        }

        foreach (array_values($sections['professional'] ?? []) as $index => $section) {
            if (! isset($notes[$index])) {
                break;
            }

            $sections['professional'][$index] = [
                ...$section,
                'marginalia' => [
                    'quote' => $notes[$index]['quote'],
                    'prompt' => $notes[$index]['prompt'],
                ],
            ];
        }

        return $sections;
    }

    public function home(): Response
    {
        $settings = $this->siteSettings->current();
        $page = $this->pages->get('home');
        $caseStudies = $this->content->published('case-studies', app()->getLocale(), false)->values();
        $seo = Seo::page(
            $page['seo_title'],
            $page['seo_description'],
            '/',
            $this->pageSeoOptions($page),
        );

        return Inertia::render('Home', [
            'seo' => $seo,
            'hero' => $page['hero'],
            'heroPanel' => $page['hero_panel'],
            'focusAreas' => $page['focus_areas'],
            'featuredCaseStudies' => $caseStudies->take(2)->values(),
            'journalWidget' => $this->publicationWidget([
                ...$this->widgetCopy('home_journal'),
                'ctaHref' => '/journal',
                'sections' => ['writing'],
                'tag' => 'notes-dev',
                'category' => 'journal',
                'limit' => 2,
            ]),
            'localTeaser' => $page['local_teaser'],
            'contactCta' => $page['contact_cta'],
            'cvDownloads' => $this->cvDownloads(),
        ])->withViewData(['seo' => $seo]);
    }

    /**
     * The address the record used to answer at.
     *
     * This redirect used to point the other way. `/experience` was the alias
     * and `/projects` was the page, which left the site calling one thing two
     * names — the menu said one, the URL and the crumb said the other. The
     * page took the name it was already being called by, so the old address
     * keeps its links working and says where they went.
     */
    /**
     * The locale comes from the route, not from the app.
     *
     * `localizedPath()` falls back to `app()->getLocale()`, which is resolved
     * from headers and cookies before it is resolved from the URL — so a
     * French reader following an old `/fr/projects` link was answered with
     * `/en/experience`, losing the one thing the URL was certain about. The
     * redirect this replaced had the same shape and the same fault.
     */
    public function projectsLegacy(string $locale): RedirectResponse
    {
        return redirect(PublicLocale::localizedPath('/experience', $locale), 301);
    }

    public function local(): Response
    {
        $page = $this->pages->get('local');
        $seo = Seo::page(
            $page['seo_title'],
            $page['seo_description'],
            '/local',
            $this->pageSeoOptions($page, [
                'breadcrumb' => [
                    ['name' => PublicLocale::homeLabel(app()->getLocale()), 'path' => '/'],
                    ['name' => PublicCopy::line('breadcrumbs.local'), 'path' => '/local'],
                ],
            ]),
        );

        return Inertia::render('Local', [
            'seo' => $seo,
            'hero' => $page['hero'],
            'nancy' => $page['nancy'],
            'signals' => $page['signals'],
            'journalSection' => $page['journal_section'],
            'journalItems' => $this->content->feed(['writing'], [
                'tag' => 'notes-dev',
                'category' => 'journal',
                'limit' => 3,
            ])->values(),
            'engagementsIntro' => $page['engagements_intro'],
            'engagements' => $page['engagements'],
            'notesSection' => $page['notes_section'],
            'notes' => $this->content->feed(['writing'], [
                'tag' => 'notes-dev',
            ])->values(),
        ])->withViewData(['seo' => $seo]);
    }

    public function sparkle(): Response
    {
        $settings = $this->siteSettings->current();
        $page = $this->pages->get('sparkle');
        $seo = Seo::page(
            $page['seo_title'],
            $page['seo_description'],
            '/sparkle',
            $this->pageSeoOptions($page, [
                'robots' => 'noindex,nofollow',
                'breadcrumb' => [
                    ['name' => PublicLocale::homeLabel(app()->getLocale()), 'path' => '/'],
                    ['name' => PublicCopy::line('breadcrumbs.sparkle'), 'path' => '/sparkle'],
                ],
            ]),
        );

        return Inertia::render('Sparkle', [
            'seo' => $seo,
            'hero' => $page['hero'],
            'project' => $page['project'],
            'controls' => $page['controls'],
            'cosmicNotes' => $page['cosmic_notes'],
            'sparkleFacts' => $page['sparkle_facts'],
            'repoUrl' => $page['repo_url'] ?: config('site.repository_url'),
            'githubProfileUrl' => $settings->socialLinks->githubUrl,
        ])->withViewData(['seo' => $seo]);
    }

    public function experience(): Response
    {
        $page = $this->pages->get('projects');
        $experience = $this->pages->get('experience');
        $sections = $this->experienceSections($experience);
        $seo = Seo::page(
            $page['seo_title'],
            $page['seo_description'],
            '/experience',
            $this->pageSeoOptions($page, [
                'schema_variant' => 'person_surface',
                'person' => [
                    'email' => $this->siteSettings->current()->contactDetails->email,
                    'job_title' => (string) config('site.author.job_title'),
                    'knows_about' => [
                        'E-commerce engineering',
                        'Product data management',
                        'GDPR compliance',
                        'Laravel',
                        'Docker Swarm',
                        'Technical SEO',
                    ],
                ],
                'breadcrumb' => [
                    ['name' => PublicLocale::homeLabel(app()->getLocale()), 'path' => '/'],
                    ['name' => PublicCopy::line('breadcrumbs.projects'), 'path' => '/experience'],
                ],
            ]),
        );

        return Inertia::render('Projects', [
            'seo' => $seo,
            'hero' => $page['hero'],
            'thesis' => $experience['thesis'],
            'positioning' => $experience['positioning'],
            'contexts' => $experience['contexts'],
            'stackGroups' => $experience['stack_groups'],
            'careerSnapshot' => $experience['career_snapshot'],
            'lookingFor' => $experience['looking_for'],
            'professionalSections' => $sections['professional'],
            'associativeSections' => $sections['associative'],
            'sideProjectSections' => $sections['side_project'],
            'trajectory' => $experience['trajectory'],
            'strengths' => $experience['strengths'],
            'focusAreas' => $experience['focus_areas'],
            'hobbies' => $experience['hobbies'] ?? [],
            'cvDownloads' => $this->cvDownloads(),
            'associativeNoteWidget' => [
                'eyebrow' => $experience['associative_note_widget']['eyebrow'],
                'title' => $experience['associative_note_widget']['title'],
                'description' => $experience['associative_note_widget']['description'],
                'ctaLabel' => $experience['associative_note_widget']['cta_label'],
                'ctaHref' => '',
                'items' => [
                    $this->content->findPublishedTranslation(
                        'writing',
                        'opensurvey-nonprofit-health-data',
                    ),
                ],
            ],
            'sideProjectsWidget' => [
                'eyebrow' => $experience['side_projects_widget']['eyebrow'],
                'title' => $experience['side_projects_widget']['title'],
                'description' => $experience['side_projects_widget']['description'],
                'ctaLabel' => $experience['side_projects_widget']['cta_label'],
                'ctaHref' => '/journal',
                'items' => [],
            ],
            'journalWidget' => $this->publicationWidget([
                ...$this->widgetCopy('projects_notes'),
                'ctaHref' => '/journal',
                'sections' => ['writing'],
                'tag' => 'notes-dev',
                'publication_type' => 'note',
                'limit' => 2,
                'exclude_translations' => $this->projectJournalExcludedTranslations(),
            ]),
            'referenceWidget' => $this->publicationWidget([
                ...$this->widgetCopy('projects_references'),
                'ctaHref' => '/case-studies',
                'sections' => ['case-studies'],
                'category' => 'work',
                'limit' => 2,
            ]),
        ])->withViewData(['seo' => $seo]);
    }

    public function labs(): Response
    {
        $seo = Seo::page(
            PublicCopy::line('seo.labs.title'),
            PublicCopy::line('seo.labs.description'),
            '/labs',
            [
                'breadcrumb' => [
                    ['name' => PublicLocale::homeLabel(app()->getLocale()), 'path' => '/'],
                    ['name' => PublicCopy::line('breadcrumbs.labs'), 'path' => '/labs'],
                ],
            ],
        );

        return Inertia::render('Labs', [
            'seo' => $seo,
            'labs' => config('site.labs'),
            'embedDemo' => [
                'title' => 'Consent-gated YouTube demo',
                'description' => 'This embed stays blocked until the media category is accepted. It proves the iframe orchestration path without loading analytics by default.',
                'service' => 'youtube',
                'id' => 'dQw4w9WgXcQ',
            ],
        ])->withViewData(['seo' => $seo]);
    }

    public function newsletterConfirmed(): Response
    {
        $seo = Seo::page(
            PublicCopy::line('seo.newsletter_confirmed.title'),
            PublicCopy::line('seo.newsletter_confirmed.description'),
            '/newsletter/confirmed',
            ['robots' => 'noindex,nofollow'],
        );

        return Inertia::render('NewsletterConfirmed', ['seo' => $seo])
            ->withViewData(['seo' => $seo]);
    }

    public function services(): Response
    {
        $page = $this->pages->get('services');
        $seo = Seo::page(
            $page['seo_title'],
            $page['seo_description'],
            '/services',
            $this->pageSeoOptions($page, [
                'breadcrumb' => [
                    ['name' => PublicLocale::homeLabel(app()->getLocale()), 'path' => '/'],
                    ['name' => PublicCopy::line('breadcrumbs.services'), 'path' => '/services'],
                ],
            ]),
        );

        return Inertia::render('Services', [
            'seo' => $seo,
            'hero' => $page['hero'],
            'offers' => $page['offers'],
            'modifiers' => $page['modifiers'],
            'engagement' => $page['engagement'],
            'legalNote' => $page['legal_note'],
            'contactCta' => $page['contact_cta'],
            'cvDownloads' => $this->cvDownloads(),
        ])->withViewData(['seo' => $seo]);
    }

    public function contact(): Response
    {
        $settings = $this->siteSettings->current();
        $page = $this->pages->get('contact');
        $seo = Seo::page(
            $page['seo_title'],
            $page['seo_description'],
            '/contact',
            $this->pageSeoOptions($page, [
                'robots' => 'noindex,follow',
                'breadcrumb' => [
                    ['name' => PublicLocale::homeLabel(app()->getLocale()), 'path' => '/'],
                    ['name' => 'Contact', 'path' => '/contact'],
                ],
            ]),
        );

        return Inertia::render('Contact', [
            'seo' => $seo,
            'contact' => $settings->contactDetails->toArray(),
            'hero' => $page['hero'],
            'form' => $page['form'],
            'details' => $page['details'],
            'cvDownloads' => $this->cvDownloads(),
            'services' => $page['services'],
            'recruiterShortcut' => $page['recruiter_shortcut'],
        ])->withViewData(['seo' => $seo]);
    }

    public function dataProcessing(): Response
    {
        $page = $this->pages->get('data-processing');
        $seo = Seo::page(
            $page['seo_title'],
            $page['seo_description'],
            '/data-processing',
            $this->pageSeoOptions($page, [
                'breadcrumb' => [
                    ['name' => PublicLocale::homeLabel(app()->getLocale()), 'path' => '/'],
                    ['name' => 'Data processing', 'path' => '/data-processing'],
                ],
                'robots' => 'noindex,nofollow',
            ]),
        );

        return Inertia::render('DataProcessing', [
            'seo' => $seo,
            'hero' => $page['hero'],
            'storage' => $page['storage'],
            'consent' => $page['consent'],
            'measurement' => $page['measurement'],
            'operator' => $page['operator'],
        ])->withViewData(['seo' => $seo]);
    }

    public function colophon(): Response
    {
        $page = $this->pages->get('colophon');
        $seo = Seo::page(
            $page['seo_title'],
            $page['seo_description'],
            '/colophon',
            $this->pageSeoOptions($page),
        );

        return Inertia::render('Colophon', [
            'seo' => $seo,
            'hero' => $page['hero'],
            'sections' => $page['sections'],
            'closing' => $page['closing'],
        ])->withViewData(['seo' => $seo]);
    }

    public function downloadCv(string $locale): BinaryFileResponse
    {
        abort_unless(in_array($locale, ['en', 'fr'], true), 404);

        abort_unless(CareerAsset::exists($locale), 404);

        return response()->download(
            CareerAsset::sourcePath($locale),
            CareerAsset::downloadName($locale),
            [
                'Content-Type' => 'application/pdf',
                'X-Robots-Tag' => 'noindex, nofollow',
            ],
        );
    }

    /**
     * The framing around a publication feed — everything the widget says that
     * is not one of the items. Spread into the widget options so the call site
     * keeps naming only what varies: which feed, and where the CTA points.
     *
     * @return array{eyebrow: string, title: string, description: string, ctaLabel: string}
     */
    protected function widgetCopy(string $key): array
    {
        $copy = PublicCopy::group("widgets.{$key}");

        return [
            'eyebrow' => $copy['eyebrow'],
            'title' => $copy['title'],
            'description' => $copy['description'],
            'ctaLabel' => $copy['cta_label'],
        ];
    }

    /**
     * @return array<int, array{label: string, href: string}>
     */
    protected function cvDownloads(): array
    {
        return [
            [
                'label' => 'CV EN / PDF',
                'href' => route('career.cv.download', 'en'),
            ],
            [
                'label' => 'CV FR / PDF',
                'href' => route('career.cv.download', 'fr'),
            ],
        ];
    }

    /**
     * @param  array{
     *   eyebrow: string,
     *   title: string,
     *   description: string,
     *   ctaLabel: string,
     *   ctaHref: string,
     *   sections: array<int, string>,
     *   tag?: string,
     *   category?: string,
     *   publication_type?: string,
     *   limit?: int,
     *   exclude_translations?: array<int, string>
     * }  $options
     * @return array<string, mixed>
     */
    protected function publicationWidget(array $options): array
    {
        $typeSettings = collect($this->content->publicationTypeSettings())
            ->keyBy('type');
        $presentation = ! empty($options['publication_type'])
            ? $typeSettings->get($options['publication_type'])
            : null;

        $ctaHref = $presentation['cta_target'] ?? $options['ctaHref'];
        $items = $this->content->feed($options['sections'], [
            'locale' => app()->getLocale(),
            'include_fallback' => false,
            'tag' => $options['tag'] ?? null,
            'category' => $options['category'] ?? null,
            'publication_type' => $options['publication_type'] ?? null,
        ])->values();

        if (! empty($options['exclude_translations'])) {
            $excluded = $options['exclude_translations'];

            $items = $items->reject(
                fn (array $item): bool => in_array($item['translation_key'], $excluded, true),
            )->values();
        }

        if (! empty($options['limit'])) {
            $items = $items->take((int) $options['limit'])->values();
        }

        return [
            'eyebrow' => $options['eyebrow'],
            'title' => $options['title'],
            'description' => $options['description'],
            'ctaLabel' => $options['ctaLabel'] !== ''
                ? $options['ctaLabel']
                : ($presentation['cta_label'] ?? ''),
            'ctaHref' => $ctaHref !== '' ? PublicLocale::localizedPath($ctaHref) : '',
            'accentColor' => $presentation['accent_color'] ?? null,
            'items' => $items,
        ];
    }

    /**
     * Two entries the projects page shows elsewhere on the same screen, so
     * the notes widget would repeat them.
     *
     * They used to be named twice, once per locale, because six of the eleven
     * journal entries have a different French slug and nothing in the data
     * paired them. `translation_key` does, so one list serves both languages
     * and cannot go half stale.
     *
     * @return array<int, string>
     */
    protected function projectJournalExcludedTranslations(): array
    {
        return ['ytmusic-liked-sorter', 'njp-volunteering-and-small-tools'];
    }

    /**
     * @param  array<string, mixed>  $page
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    protected function pageSeoOptions(array $page, array $overrides = []): array
    {
        $options = [
            'robots' => $page['robots'] ?: 'index,follow',
            'canonical_url' => $page['canonical_url'] ?: '',
            'image' => [
                'url' => $page['open_graph_image'] ?? '',
                'alt' => $page['title'] ?: $page['seo_title'],
            ],
        ];

        return array_replace_recursive($options, $overrides);
    }
}
