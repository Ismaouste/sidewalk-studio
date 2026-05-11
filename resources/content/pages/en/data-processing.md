---
seo_title: Data processing
seo_description: 'Information about contact messages, consent, and explicit opt-in for optional tools on Sidewalk Studio.'
hero:
    eyebrow: Data processing
    title: A simple page that explains what is stored and what remains opt-in.
    summary: Contact messages are not stored on the site. The form prepares a WhatsApp message instead, while optional tools stay blocked until explicit consent exists.
storage:
    eyebrow: Contact messages
    title: What is stored when you use the form
    points:
        - The public form does not persist name, email, company, or message in the site database.
        - It validates the fields, then opens a WhatsApp message prepared with the submitted context.
        - If you prefer email, you can write directly without using the form.
consent:
    eyebrow: Consent
    title: Analytics and third-party content remain opt-in
    points:
        - External embeds stay blocked until the media category is accepted.
        - Analytics is cookieless and opt-in. The site supports two drivers, operator's choice — self-hosted Umami (no cookies, no PII) or Vercel Web Analytics paired with Speed Insights (cookieless, page views and performance only).
        - No analytics script loads until the analytics category is accepted. No heatmap, no marketing pixel, no GTM.
        - Browsers in Do Not Track mode are honored even after acceptance.
        - The preferences button lets you reopen the consent modal and change that choice at any time.
operator:
    eyebrow: Operator
    title: Contact for privacy or deletion requests
    summary: For a deletion request or a data-processing question, write directly by email or use the direct contact message path.
---
