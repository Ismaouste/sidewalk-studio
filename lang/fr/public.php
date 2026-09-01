<?php

/**
 * French counterpart of `lang/en/public.php`. English is the reference shape:
 * `LanguageFileParityTest` reports a key missing here and a key that exists
 * only here, so the two locales cannot drift apart silently.
 */
return [
    'shell' => [
        'header_tagline' => 'Développeur e-commerce full stack. Data transverse, flux fiables.',
        'locale_switcher_label' => 'Langue',
        'nav_aria_label' => 'Navigation principale',
        'nav_menu_label' => 'Menu',
        'nav_fallback_label' => 'Navigation',
        'nav_current_label' => 'Actif',
        'nav_open_label' => 'Lire plus',
        'footer_note' => 'Développement web, donnée produit, connecteurs, outils internes et SEO technique pour des équipes qui ont déjà du réel à faire tourner.',
        'privacy_controls_label' => 'Réglages vie privée',
    ],

    'navigation' => [
        '/' => 'Hello',
        '/projects' => 'Expériences',
        '/journal' => 'Journal',
        '/contact' => 'Contact ✍🏽',
    ],

    'breadcrumbs' => [
        'case_studies' => 'Études de cas',
        'journal' => 'Journal',
        'labs' => 'Labs',
        'local' => 'Localisation',
        'projects' => 'Projets',
        'sparkle' => 'Sparkle',
    ],

    'sections' => [
        'case_studies' => 'Cas clients',
        'journal' => 'Journal',
    ],

    'seo' => [
        'journal' => [
            'title' => 'Blog technique e-commerce PHP',
            'description' => "Blog technique sur l'e-commerce PHP, Laravel, l'orchestration du consentement, la modélisation de contenu et les détails qui comptent vraiment en production.",
        ],
        'case_studies' => [
            'title' => 'Études de cas',
            'description' => "Études de cas sur les flux produit, l'auto-hébergement, le consentement, le SEO technique et d'autres systèmes web sous contrainte.",
        ],
        'labs' => [
            'title' => 'Labs',
            'description' => 'Terrains d’essai réservés au consentement, aux données structurées et aux expérimentations de design system à venir.',
        ],
    ],

    'contact_mail' => [
        'subject' => 'Prise de contact',
        'name' => 'Nom : ',
        'email' => 'Email : ',
        'company' => 'Entreprise ou produit : ',
    ],

    'experience' => [
        'since' => 'Depuis :year',
        'present' => 'aujourd’hui',
    ],

    'questionnaire' => [
        'first_repair' => [
            'prompt' => 'Que répare-t-on en premier, dans un système dont on hérite ?',
            'hint' => 'Court. La réponse tient dans une légende à côté d’une double page, pas dans un paragraphe.',
        ],
        'changed_mind' => [
            'prompt' => 'Qu’est-ce qui vous a fait changer d’avis cette année ?',
            'hint' => 'Une conviction, et ce qui l’a déplacée. Nommer ce sur quoi on avait tort est l’intérêt.',
        ],
        'owed_to_the_reader' => [
            'prompt' => 'Que doit un document à celui qui le lit ?',
            'hint' => 'Une obligation, dite simplement.',
        ],
        'what_a_system_says' => [
            'prompt' => 'Que devrait pouvoir dire un système une fois que vous l’avez quitté ?',
            'hint' => 'La phrase que vous voudriez voir écrite par la personne qui reprend.',
        ],
    ],

    'widgets' => [
        'home_journal' => [
            'eyebrow' => 'Journal',
            'title' => 'Articles, notes et détails techniques qui valent le détour.',
            'description' => 'Des textes plus construits et des mémos plus courts pour parler terrain, flux produit, SEO, données structurées, outils associatifs et détails de build.',
            'cta_label' => 'Découvrir le journal',
        ],
        'projects_notes' => [
            'eyebrow' => 'Notes',
            'title' => 'Mémos techniques, anecdotes utiles, détails qui comptent.',
            'description' => 'Des notes courtes pour parler schéma.org, données produit, catalogues, images, formats web et autres détails techniques qui finissent par faire une vraie différence.',
            'cta_label' => 'Consulter les notes',
        ],
        'projects_references' => [
            'eyebrow' => 'Références',
            'title' => 'Études de cas et notes pour aller plus loin.',
            'description' => 'Une archive pour entrer dans des cas plus précis : outils associatifs sous contrainte, circulation de la donnée entre ERP, PIM et e-commerce, formats web, sitemaps, robots.txt et qualité de livraison.',
            'cta_label' => 'Découvrir toutes les études de cas',
        ],
    ],
];
