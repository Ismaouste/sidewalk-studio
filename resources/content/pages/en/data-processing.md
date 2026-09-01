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
        - Any analytics or heatmap integration must remain disabled by default and appear only after explicit opt-in.
        - The preferences button lets you reopen the consent modal and change that choice at any time.
measurement:
    eyebrow: Measurement
    title: Three tiers, each with its own switch
    points:
        - 'Audience: a first-party, cookieless ping counts page views with a truncated IP folded into an identifier that changes every day. It stores nothing in your browser, honors Global Privacy Control, and you can switch it off below.'
        - 'Analytics: PostHog, hosted in the EU, loads only after the analytics category is accepted in the consent preferences - never before.'
        - 'Replay and heatmaps: the most invasive tier has its own switch below. Accepting analytics, or pressing "Accept all", never turns it on.'
operator:
    eyebrow: Operator
    title: Contact for privacy or deletion requests
    summary: For a deletion request or a data-processing question, write directly by email or use the direct contact message path.
---
