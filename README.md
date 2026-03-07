# Sidewalk Studio

A spec-driven engineering portfolio exploring modern web architecture, privacy-first analytics and elegant front-end craft.

Sidewalk Studio is both:

• a **portfolio website**
• a **living engineering playground**
• a **demonstration of spec-driven development**

The goal is to showcase engineering maturity through:

- modern web architecture
- privacy-first analytics
- SEO excellence
- reproducible CI/CD pipelines
- clear documentation

---

# Philosophy

Software should be:

• **Readable**  
• **Respectful of users** (privacy & accessibility)  
• **Technically elegant**

This repository demonstrates a full stack approach combining:

- Laravel
- Inertia.js
- Vue 3 + TypeScript
- TailwindCSS
- Spec-Driven Development
- CI/CD automation

---

# Key Features

### Spec-Driven Development

This project uses **Spec-Kit** to structure development through specifications, plans and tasks rather than ad-hoc coding. :contentReference[oaicite:0]{index=0}

Each feature is implemented through:


spec → plan → tasks → implementation


---

### Privacy-First Tracking

Analytics is implemented with a **strict GDPR approach**:

• CookieConsent (orestbida)  
• IframeManager (orestbida)  
• Script orchestration by consent category  

CookieConsent blocks tracking scripts until explicit user consent. :contentReference[oaicite:1]{index=1}

Supported analytics modes:

- **Matomo (privacy-first)**
- **PostHog (opt-in session analytics)**

---

### Case Studies

Engineering case studies are versioned directly in the repository using Markdown files.

Examples:

- SEO & structured data architecture
- Consent orchestration system
- Engineering CI/CD pipeline

---

# Stack

Backend


Laravel (latest stable)
PHP 8.3
MySQL


Frontend


Inertia.js
Vue 3
TypeScript
Vite
TailwindCSS


Dev tooling


Spec-Kit
Pest
ESLint
Prettier
GitHub Actions
Docker


---

# Repository Goals

This project aims to demonstrate:

• strong engineering practices  
• privacy-respecting web architecture  
• structured documentation  
• production-ready workflows  

It also serves as a **playground for reusable components and experiments**.

---

# Local development

```bash
git clone https://github.com/<username>/sidewalk-studio
cd sidewalk-studio

docker compose up -d
npm install
php artisan migrate
npm run dev
```

MIT License — reuse anything you like.
If it helped you build something cool, I'd love to hear about it.
