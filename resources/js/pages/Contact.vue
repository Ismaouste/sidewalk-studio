<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import ContentMetaRow from '@/components/design-system/ContentMetaRow.vue';
import LegendChip from '@/components/design-system/LegendChip.vue';
import SectionDivider from '@/components/design-system/SectionDivider.vue';
import SectionIntro from '@/components/design-system/SectionIntro.vue';
import SeoMeta from '@/components/SeoMeta.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import type { FlashProps, SeoPayload, SiteContact, SiteProps } from '@/types';

const page = usePage<{ site: SiteProps; flash: FlashProps }>();

const props = defineProps<{
    seo: SeoPayload;
    contact: SiteContact;
    hero: {
        eyebrow: string;
        title: string;
        summary: string;
    };
    form: {
        eyebrow: string;
        title: string;
        summary: string;
        name_label: string;
        name_placeholder: string;
        email_label: string;
        email_placeholder: string;
        company_label: string;
        company_placeholder: string;
        summary_label: string;
        summary_placeholder: string;
        summary_meta: string;
        primary_cta: string;
        secondary_cta: string;
    };
    details: {
        eyebrow: string;
        email_label: string;
        location_label: string;
        availability_label: string;
    };
    cvDownloads: Array<{
        label: string;
        href: string;
    }>;
    services: {
        eyebrow: string;
        items: string[];
    };
    recruiterShortcut: {
        eyebrow: string;
        summary: string;
    };
}>();

const serviceTones = ['dominant', 'green', 'coral'] as const;

const inquiry = useForm({
    name: '',
    email: '',
    company: '',
    summary: '',
});

const copy = computed(() =>
    page.props.site.locale === 'fr'
        ? {
              experienceCta: 'Lire le parcours',
              dividerLabel: 'Prendre contact',
              privacyChipLabel: 'Consentement et vie privée',
              baseChipLabel: `Base ${props.contact.location}`,
              baseLabel: 'Base',
              availabilityLabel: 'Disponibilité',
              subjectPrefix: 'Prise de contact Sidewalk Studio',
              bodyNameLabel: 'Nom',
              bodyEmailLabel: 'Email',
              bodyCompanyLabel: 'Entreprise ou produit',
              bodyBriefFallback: 'Brief projet :',
              servicePrefix: 'Sujet',
              recruiterFitLabel: 'Rôles cibles',
              recruiterFitRoles: [
                  'Lead developer Laravel',
                  'Tech lead e-commerce',
                  'Ingénieur full stack orienté produit',
              ],
              recruiterDecisionLabel: 'Pertinent pour',
              recruiterDecisionCopy:
                  "Recrutement, mise en relation rapide, revue d'architecture, reprise de plateforme ou mission freelance sur un produit déjà en charge.",
              cvLabel: 'CV',
          }
        : {
              experienceCta: 'Read experience',
              dividerLabel: 'Start a conversation',
              privacyChipLabel: 'Privacy-first engagements',
              baseChipLabel: `${props.contact.location} base`,
              baseLabel: 'Base',
              availabilityLabel: 'Availability',
              subjectPrefix: 'Sidewalk Studio inquiry',
              bodyNameLabel: 'Name',
              bodyEmailLabel: 'Email',
              bodyCompanyLabel: 'Company or product',
              bodyBriefFallback: 'Project brief:',
              servicePrefix: 'Fit',
              recruiterFitLabel: 'Best fits',
              recruiterFitRoles: [
                  'Laravel lead developer',
                  'E-commerce tech lead',
                  'Product-minded full-stack engineer',
              ],
              recruiterDecisionLabel: 'Useful for',
              recruiterDecisionCopy:
                  'Hiring, faster recruiter handoff, architecture review, freelance modernization, or an existing platform that already has delivery pressure.',
              cvLabel: 'CV',
          },
);

const inquiryMeta = computed(() => [
    {
        label: copy.value.baseLabel,
        value: props.contact.location,
    },
    {
        label: copy.value.availabilityLabel,
        value: props.contact.availability,
    },
]);

const statusMessage = computed(() => page.props.flash?.status ?? null);

const mailtoHref = computed(() => {
    const subjectBase = inquiry.company.trim()
        ? `${copy.value.subjectPrefix}: ${inquiry.company.trim()}`
        : copy.value.subjectPrefix;

    const lines = [
        inquiry.name.trim() &&
            `${copy.value.bodyNameLabel}: ${inquiry.name.trim()}`,
        inquiry.email.trim() &&
            `${copy.value.bodyEmailLabel}: ${inquiry.email.trim()}`,
        inquiry.company.trim() &&
            `${copy.value.bodyCompanyLabel}: ${inquiry.company.trim()}`,
        '',
        inquiry.summary.trim() || copy.value.bodyBriefFallback,
    ].filter(Boolean);

    return `mailto:${props.contact.email}?subject=${encodeURIComponent(
        subjectBase,
    )}&body=${encodeURIComponent(lines.join('\n'))}`;
});

function submitInquiry(): void {
    inquiry.post('/contact', {
        preserveScroll: true,
    });
}
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section class="sw-section contact-page">
            <SectionIntro
                :eyebrow="props.hero.eyebrow"
                :title="props.hero.title"
                :description="props.hero.summary"
            >
                <template #actions>
                    <Button :href="`mailto:${props.contact.email}`">
                        {{ props.form.secondary_cta }}
                    </Button>
                    <Button href="/experience" variant="secondary">
                        {{ copy.experienceCta }}
                    </Button>
                </template>

                <LegendChip :label="copy.privacyChipLabel" tone="green" />
                <LegendChip :label="copy.baseChipLabel" tone="sun" />
            </SectionIntro>

            <SectionDivider :label="copy.dividerLabel" />

            <div class="contact-page__grid">
                <Panel class="contact-page__form-panel" tone="surface">
                    <p
                        v-if="statusMessage"
                        class="type-body-sm contact-page__status"
                    >
                        {{ statusMessage }}
                    </p>

                    <div class="contact-page__form-intro">
                        <p class="type-eyebrow">{{ props.form.eyebrow }}</p>
                        <h2 class="type-h2 contact-page__panel-title">
                            {{ props.form.title }}
                        </h2>
                        <p class="type-body-sm contact-page__panel-copy">
                            {{ props.form.summary }}
                        </p>
                    </div>

                    <form class="contact-page__form" @submit.prevent="submitInquiry">
                        <label class="contact-page__field">
                            <span class="type-nav">
                                {{ props.form.name_label }}
                            </span>
                            <input
                                v-model="inquiry.name"
                                class="contact-page__input"
                                type="text"
                                name="name"
                                autocomplete="name"
                                :placeholder="props.form.name_placeholder"
                            />
                            <span
                                v-if="inquiry.errors.name"
                                class="type-meta contact-page__error"
                            >
                                {{ inquiry.errors.name }}
                            </span>
                        </label>

                        <label class="contact-page__field">
                            <span class="type-nav">
                                {{ props.form.email_label }}
                            </span>
                            <input
                                v-model="inquiry.email"
                                class="contact-page__input"
                                type="email"
                                name="email"
                                autocomplete="email"
                                :placeholder="props.form.email_placeholder"
                            />
                            <span
                                v-if="inquiry.errors.email"
                                class="type-meta contact-page__error"
                            >
                                {{ inquiry.errors.email }}
                            </span>
                        </label>

                        <label class="contact-page__field">
                            <span class="type-nav">
                                {{ props.form.company_label }}
                            </span>
                            <input
                                v-model="inquiry.company"
                                class="contact-page__input"
                                type="text"
                                name="company"
                                autocomplete="organization"
                                :placeholder="props.form.company_placeholder"
                            />
                            <span
                                v-if="inquiry.errors.company"
                                class="type-meta contact-page__error"
                            >
                                {{ inquiry.errors.company }}
                            </span>
                        </label>

                        <label
                            class="contact-page__field contact-page__field--full"
                        >
                            <span class="type-nav">
                                {{ props.form.summary_label }}
                            </span>
                            <textarea
                                v-model="inquiry.summary"
                                class="contact-page__input contact-page__input--textarea"
                                name="summary"
                                rows="6"
                                :placeholder="props.form.summary_placeholder"
                            />
                            <span
                                v-if="inquiry.errors.summary"
                                class="type-meta contact-page__error"
                            >
                                {{ inquiry.errors.summary }}
                            </span>
                            <span class="type-meta contact-page__field-meta">
                                {{ props.form.summary_meta }}
                            </span>
                        </label>
                    </form>

                    <div class="contact-page__form-actions">
                        <Button
                            type="submit"
                            :disabled="inquiry.processing"
                            @click="submitInquiry"
                        >
                            {{ props.form.primary_cta }}
                        </Button>
                        <Button
                            :href="`mailto:${props.contact.email}`"
                            variant="secondary"
                        >
                            {{ props.form.secondary_cta }}
                        </Button>
                    </div>
                </Panel>

                <div class="contact-page__aside">
                    <Panel class="contact-page__details" tone="grid">
                        <p class="type-eyebrow">{{ props.details.eyebrow }}</p>
                        <ContentMetaRow :items="inquiryMeta" />

                        <dl class="contact-page__detail-list">
                            <div class="contact-page__detail">
                                <dt class="type-nav">
                                    {{ props.details.email_label }}
                                </dt>
                                <dd class="type-body-sm">
                                    <a
                                        class="contact-page__detail-link"
                                        :href="`mailto:${props.contact.email}`"
                                    >
                                        {{ props.contact.email }}
                                    </a>
                                </dd>
                            </div>

                            <div class="contact-page__detail">
                                <dt class="type-nav">
                                    {{ props.details.location_label }}
                                </dt>
                                <dd class="type-body-sm">
                                    {{ props.contact.location }}
                                </dd>
                            </div>

                            <div class="contact-page__detail">
                                <dt class="type-nav">
                                    {{ props.details.availability_label }}
                                </dt>
                                <dd class="type-body-sm">
                                    {{ props.contact.availability }}
                                </dd>
                            </div>
                        </dl>
                    </Panel>

                    <Panel class="contact-page__services" tone="surface">
                        <p class="type-eyebrow">{{ props.services.eyebrow }}</p>

                        <ul class="contact-page__service-list">
                            <li
                                v-for="(service, index) in props.services.items"
                                :key="service"
                                class="contact-page__service-item"
                            >
                                <LegendChip
                                    :label="`${copy.servicePrefix} 0${index + 1}`"
                                    :tone="serviceTones[index] ?? 'violet'"
                                />
                                <p class="type-body contact-page__service-copy">
                                    {{ service }}
                                </p>
                            </li>
                        </ul>
                    </Panel>

                    <Panel class="contact-page__services" tone="grid">
                        <p class="type-eyebrow">
                            {{ props.recruiterShortcut.eyebrow }}
                        </p>
                        <p class="type-body-sm contact-page__service-copy">
                            {{ props.recruiterShortcut.summary }}
                        </p>

                        <div class="contact-page__fit-block">
                            <p class="type-nav">{{ copy.recruiterFitLabel }}</p>
                            <div class="contact-page__fit-items">
                                <span
                                    v-for="role in copy.recruiterFitRoles"
                                    :key="role"
                                    class="type-meta contact-page__fit-item"
                                >
                                    {{ role }}
                                </span>
                            </div>
                        </div>

                        <div class="contact-page__fit-block">
                            <p class="type-nav">
                                {{ copy.recruiterDecisionLabel }}
                            </p>
                            <p class="type-body-sm contact-page__service-copy">
                                {{ copy.recruiterDecisionCopy }}
                            </p>
                        </div>

                        <div class="contact-page__form-actions">
                            <Button
                                v-for="download in props.cvDownloads"
                                :key="download.href"
                                :href="download.href"
                            >
                                {{ download.label }}
                            </Button>
                            <Button
                                :href="`mailto:${props.contact.email}`"
                                variant="secondary"
                            >
                                {{ props.form.secondary_cta }}
                            </Button>
                        </div>
                    </Panel>
                </div>
            </div>
        </section>
    </SiteLayout>
</template>

<style scoped>
.contact-page {
    display: grid;
    gap: var(--sw-space-sm);
}

.contact-page__grid {
    display: grid;
    gap: var(--sw-space-sm);
    align-items: start;
    grid-template-columns: minmax(0, 1.1fr) minmax(18rem, 0.9fr);
}

.contact-page__aside {
    display: grid;
    gap: var(--sw-space-sm);
}

.contact-page__form-panel,
.contact-page__services,
.contact-page__details {
    display: grid;
    gap: var(--sw-space-sm);
    padding: clamp(18px, 2.8vw, var(--sw-space-sm));
}

.contact-page__form-intro {
    display: grid;
    gap: var(--sw-space-3xs);
}

.contact-page__panel-title,
.contact-page__panel-copy {
    margin: 0;
}

.contact-page__panel-title {
    color: var(--sw-text-primary);
}

.contact-page__panel-copy {
    color: var(--sw-text-secondary);
}

.contact-page__form {
    display: grid;
    gap: var(--sw-space-xs);
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.contact-page__field {
    display: grid;
    gap: 10px;
}

.contact-page__field--full {
    grid-column: 1 / -1;
}

.contact-page__field-meta {
    color: var(--sw-text-muted);
}

.contact-page__status {
    margin: 0;
    border: 1px solid color-mix(in srgb, var(--sw-accent-green) 24%, transparent);
    border-radius: calc(var(--sw-radius-md) + 2px);
    background: color-mix(in srgb, var(--sw-accent-green) 8%, transparent);
    padding: 0.9rem 1rem;
    color: color-mix(in srgb, var(--sw-accent-green) 88%, var(--sw-text-primary));
}

.contact-page__error {
    color: var(--sw-accent-coral);
}

.contact-page__input {
    min-height: 3rem;
    width: 100%;
    border: 1px solid color-mix(in srgb, var(--sw-border) 88%, transparent);
    border-radius: calc(var(--sw-radius-md) + 2px);
    background: color-mix(in srgb, var(--sw-bg-elevated) 88%, transparent);
    padding: 0.9rem 1rem;
    font: inherit;
    color: var(--sw-text-primary);
    box-shadow: var(--sw-shadow-sm);
    transition:
        border-color var(--sw-motion-fast),
        background-color var(--sw-motion-fast),
        box-shadow var(--sw-motion-fast),
        transform var(--sw-motion-fast);
}

.contact-page__input::placeholder {
    color: var(--sw-text-muted);
}

.contact-page__input--textarea {
    min-height: 9.5rem;
    resize: vertical;
    line-height: 1.55;
}

.contact-page__form-actions {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-xs);
}

.contact-page__fit-block {
    display: grid;
    gap: var(--sw-space-3xs);
}

.contact-page__service-list,
.contact-page__detail-list {
    display: grid;
    gap: var(--sw-space-xs);
    margin: 0;
    padding: 0;
}

.contact-page__service-item,
.contact-page__detail {
    display: grid;
    gap: var(--sw-space-3xs);
    border-top: 1px solid var(--sw-border);
    padding-top: var(--sw-space-xs);
}

.contact-page__service-item:first-child,
.contact-page__detail:first-child {
    border-top: 0;
    padding-top: 0;
}

.contact-page__service-copy,
.contact-page__detail dd {
    margin: 0;
    color: var(--sw-text-secondary);
}

.contact-page__detail-link {
    color: var(--sw-accent-dominant);
    text-decoration: underline;
    text-underline-offset: 0.2em;
}

.contact-page__fit-items {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-3xs);
}

.contact-page__fit-item {
    display: inline-flex;
    align-items: center;
    min-height: 1.75rem;
    border-radius: var(--sw-radius-full);
    background: color-mix(in srgb, var(--sw-bg-elevated) 72%, transparent);
    padding-inline: var(--sw-space-2xs);
}

@media (hover: hover) {
    .contact-page__input:hover {
        border-color: color-mix(
            in srgb,
            var(--sw-accent-dominant) 24%,
            var(--sw-border)
        );
        background: color-mix(in srgb, var(--sw-bg-elevated) 94%, transparent);
    }
}

.contact-page__input:focus-visible {
    border-color: var(--sw-border-focus);
    box-shadow:
        0 0 0 3px color-mix(in srgb, var(--sw-border-focus) 18%, transparent),
        var(--sw-shadow-md);
    outline: none;
}

@media (max-width: 960px) {
    .contact-page__grid {
        grid-template-columns: minmax(0, 1fr);
    }
}

@media (max-width: 640px) {
    .contact-page {
        gap: var(--sw-space-xs);
    }

    .contact-page__form {
        grid-template-columns: minmax(0, 1fr);
    }

    .contact-page__form-actions {
        display: grid;
    }
}
</style>
