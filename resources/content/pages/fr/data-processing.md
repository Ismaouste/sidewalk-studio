---
seo_title: Traitement des données
seo_description: "Informations sur les messages de contact, le consentement et l'activation explicite des outils optionnels sur Sidewalk Studio."
hero:
    eyebrow: Traitement des données
    title: Une page simple pour expliquer ce qui est stocké et ce qui reste opt-in.
    summary: Les messages de contact ne sont pas conservés côté site. Le formulaire prépare un message WhatsApp et les outils optionnels restent bloqués tant qu'un consentement explicite n'existe pas.
storage:
    eyebrow: Messages de contact
    title: Ce qui est conservé quand tu utilises le formulaire
    points:
        - Le formulaire public n'enregistre pas le nom, l'email, l'entreprise ou le message dans la base du site.
        - Il valide les champs puis ouvre un message WhatsApp prérempli avec le contexte fourni.
        - Si tu préfères un email direct, tu peux écrire sans passer par le formulaire.
consent:
    eyebrow: Consentement
    title: Analytics et contenus tiers restent opt-in
    points:
        - Les embeds externes restent bloqués tant que la catégorie media n'est pas acceptée.
        - L'analytics est cookie-less et opt-in. Le site supporte deux drivers, au choix de l'opérateur: Umami auto-hébergé (sans cookie, sans PII) ou Vercel Web Analytics couplé à Speed Insights (cookieless, perf et pages-vues uniquement).
        - Aucun script analytics n'est chargé tant que la catégorie analytics n'a pas été acceptée. Aucune carte de chaleur, aucun pixel marketing, aucun GTM.
        - Le navigateur en mode Do Not Track est respecté même après acceptation.
        - Le bouton de préférences permet de rouvrir la modale de consentement et de modifier ce choix à tout moment.
operator:
    eyebrow: Opérateur
    title: Contact pour une demande RGPD ou suppression
    summary: Pour une demande de suppression ou une question sur le traitement des données, écris directement par email ou utilise le contact direct par message.
---
