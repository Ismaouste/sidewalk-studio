<?php

/**
 * Editorial copy for the public surface that is resolved on the server.
 *
 * The TypeScript copy tree under `resources/js/copy/` holds everything Vue
 * renders, and its `satisfies` clauses give it a compile-time parity
 * guarantee. It cannot hold these strings: SEO titles, schema.org breadcrumb
 * and section names, and the shared shell copy are written into the response
 * before any component runs.
 *
 * Until now they lived as `app()->getLocale() === 'fr' ? … : …` ternaries
 * inside controllers and `PublicLocale`, which is the one editorial surface on
 * this site with no parity guarantee at all — neither the compile-time one the
 * copy tree has, nor the review-time one the Markdown content has.
 *
 * `LanguageFileParityTest` is the replacement: it asserts that this file and
 * its French counterpart carry the same key tree, and fails naming the key
 * that drifted. `LanguageFileService` also lists both files, so `/admin`
 * edits this copy without a developer.
 */
return [
    /**
     * Labels the shell shares with every page through Inertia.
     */
    'shell' => [
        'header_tagline' => 'Full-stack e-commerce. Cross-functional data, reliable flows.',
        'locale_switcher_label' => 'Language',
        'nav_aria_label' => 'Primary navigation',
        'nav_menu_label' => 'Menu',
        'nav_fallback_label' => 'Navigation',
        'nav_current_label' => 'Current',
        'nav_open_label' => 'Read more',
        'footer_note' => 'Web engineering for product data, integrations, internal tools, and technical SEO in teams already running real operations.',
        'privacy_controls_label' => 'Privacy controls',
    ],

    /**
     * Navigation labels, keyed by the locale-stripped path the entry points
     * at. `config('site.navigation')` owns the routing table; this owns what
     * each entry is called.
     */
    'navigation' => [
        '/' => 'Hello',
        '/services' => 'Services',
        '/case-studies' => 'Case studies',
        '/journal' => 'Journal',
        '/contact' => 'Contact ✍🏽',
    ],

    /**
     * Breadcrumb segment names. The front page is not here: it takes its name
     * from the navigation, so a crumb cannot disagree with the menu pointing
     * at it.
     */
    'breadcrumbs' => [
        'case_studies' => 'Case Studies',
        'journal' => 'Journal',
        'labs' => 'Labs',
        'local' => 'Local',
        'projects' => 'Experience',
        'services' => 'Services',
        'sparkle' => 'Sparkle',
    ],

    /**
     * schema.org `articleSection` values.
     */
    'sections' => [
        'case_studies' => 'Case Studies',
        'journal' => 'Journal',
    ],

    /**
     * Titles and descriptions for the routes with no Markdown page behind
     * them. Every other page takes its metadata from its own content file.
     */
    'seo' => [
        'journal' => [
            'title' => 'Journal',
            'description' => 'Technical notes on ecommerce PHP, Laravel, consent orchestration, content modeling, and the details that matter in production.',
        ],
        'case_studies' => [
            'title' => 'Case Studies',
            'description' => 'Case studies about product-data flows, self-hosting, consent, technical SEO, and other constrained web systems.',
        ],
        'labs' => [
            'title' => 'Labs',
            'description' => 'Sandbox areas reserved for consent, structured data, and later design-system experiments.',
        ],
    ],

    /**
     * The mailto: the contact form opens. The visitor reads it in their own
     * mail client before sending it, so it is copy like any other — and it
     * was the last pair of locale-branched strings left in a controller.
     */
    'contact_mail' => [
        'subject' => 'Inquiry',
        'name' => 'Name: ',
        'email' => 'Email: ',
        'company' => 'Company or product: ',
        'project_type' => 'Project type: ',
        'budget' => 'Budget band: ',
        'timeline' => 'Timeline: ',
    ],

    /**
     * How a date range reads when the record computes it rather than being
     * told. An entry with no end date is the one being lived, and it is the
     * reason the chronology stops needing an edit every January.
     */
    'experience' => [
        'since' => 'Since :year',
        'present' => 'present',
    ],

    /**
     * The questions the site asks its owner, and the nudge shown beside each
     * one in the admin. A reader sees the prompt: it becomes the caption
     * under the answer, which is what makes a marginal note read as a Q&A
     * rather than as a pull quote from nowhere.
     */
    'questionnaire' => [
        'first_repair' => [
            'prompt' => 'What do you repair first, in a system you inherit?',
            'hint' => 'Short. It sits in a caption beside a spread, not in a paragraph.',
        ],
        'changed_mind' => [
            'prompt' => 'What changed your mind this year?',
            'hint' => 'One belief, and what moved it. Naming the thing you were wrong about is the point.',
        ],
        'owed_to_the_reader' => [
            'prompt' => 'What does a document owe the person reading it?',
            'hint' => 'One obligation, stated plainly.',
        ],
        'what_a_system_says' => [
            'prompt' => 'What should a system be able to say once you have left it?',
            'hint' => 'The sentence you would want the next maintainer to be able to write.',
        ],
    ],

    /**
     * Publication widgets, keyed by the page they sit on and the feed they
     * pull. The items come from the content repository; this is the framing
     * around them.
     */
    'widgets' => [
        'home_journal' => [
            'eyebrow' => 'Journal',
            'title' => 'Articles, notes, and technical details worth opening.',
            'description' => 'Longer articles and shorter memos about product flows, structured data, SEO, nonprofit tooling, and build details.',
            'cta_label' => 'Discover the journal',
        ],
        'projects_notes' => [
            'eyebrow' => 'Notes',
            'title' => 'Technical memos, useful anecdotes, and details that matter.',
            'description' => 'Short notes about schema.org, product data, catalogs, images, web formats, and the technical details that end up making a real difference.',
            'cta_label' => 'Browse notes',
        ],
        'projects_references' => [
            'eyebrow' => 'References',
            'title' => 'Case studies and notes to go deeper.',
            'description' => 'An archive for more precise cases: constrained nonprofit tooling, data flow between ERP, PIM, and commerce, web formats, sitemaps, robots.txt, and delivery quality.',
            'cta_label' => 'Browse all case studies',
        ],
    ],
];
