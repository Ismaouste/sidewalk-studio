<script setup lang="ts">
import { computed, reactive } from 'vue';
import ContentMetaRow from '@/components/design-system/ContentMetaRow.vue';
import LegendChip from '@/components/design-system/LegendChip.vue';
import SectionDivider from '@/components/design-system/SectionDivider.vue';
import SectionIntro from '@/components/design-system/SectionIntro.vue';
import SeoMeta from '@/components/SeoMeta.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import type { SeoPayload, SiteContact } from '@/types';

const props = defineProps<{
    seo: SeoPayload;
    contact: SiteContact;
    cvDownloads: Array<{
        label: string;
        href: string;
    }>;
    services: string[];
}>();

const serviceTones = ['dominant', 'green', 'coral'] as const;

const inquiry = reactive({
    name: '',
    email: '',
    company: '',
    summary: '',
});

const inquiryMeta = computed(() => [
    {
        label: 'Base',
        value: props.contact.location,
    },
    {
        label: 'Availability',
        value: props.contact.availability,
    },
]);

const mailtoHref = computed(() => {
    const subjectBase = inquiry.company.trim()
        ? `Sidewalk Studio inquiry: ${inquiry.company.trim()}`
        : 'Sidewalk Studio inquiry';

    const lines = [
        inquiry.name.trim() && `Name: ${inquiry.name.trim()}`,
        inquiry.email.trim() && `Email: ${inquiry.email.trim()}`,
        inquiry.company.trim() &&
            `Company or product: ${inquiry.company.trim()}`,
        '',
        inquiry.summary.trim() || 'Project brief:',
    ].filter(Boolean);

    return `mailto:${props.contact.email}?subject=${encodeURIComponent(
        subjectBase,
    )}&body=${encodeURIComponent(lines.join('\n'))}`;
});
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section class="sw-section contact-page">
            <SectionIntro
                eyebrow="Contact"
                title="Privacy, SEO, and Laravel modernization conversations."
                :description="props.contact.availability"
            >
                <template #actions>
                    <Button :href="`mailto:${props.contact.email}`">
                        Email directly
                    </Button>
                    <Button href="/experience" variant="secondary">
                        Read experience
                    </Button>
                </template>

                <LegendChip label="Privacy-first engagements" tone="green" />
                <LegendChip
                    :label="`${props.contact.location} base`"
                    tone="sun"
                />
            </SectionIntro>

            <SectionDivider label="Start a conversation" />

            <div class="contact-page__grid">
                <Panel class="contact-page__form-panel" tone="surface">
                    <div class="contact-page__form-intro">
                        <p class="type-eyebrow">Draft your note</p>
                        <h2 class="type-h2 contact-page__panel-title">
                            Share the context before you open your mail client.
                        </h2>
                        <p class="type-body-sm contact-page__panel-copy">
                            This keeps the page backend-free while still giving
                            you a cleaner way to structure the first message.
                        </p>
                    </div>

                    <form class="contact-page__form" @submit.prevent>
                        <label class="contact-page__field">
                            <span class="type-nav">Name</span>
                            <input
                                v-model="inquiry.name"
                                class="contact-page__input"
                                type="text"
                                name="name"
                                autocomplete="name"
                                placeholder="Your name"
                            />
                        </label>

                        <label class="contact-page__field">
                            <span class="type-nav">Email</span>
                            <input
                                v-model="inquiry.email"
                                class="contact-page__input"
                                type="email"
                                name="email"
                                autocomplete="email"
                                placeholder="you@example.com"
                            />
                        </label>

                        <label class="contact-page__field">
                            <span class="type-nav">Company or product</span>
                            <input
                                v-model="inquiry.company"
                                class="contact-page__input"
                                type="text"
                                name="company"
                                autocomplete="organization"
                                placeholder="Brand, product, or team"
                            />
                        </label>

                        <label
                            class="contact-page__field contact-page__field--full"
                        >
                            <span class="type-nav">Project brief</span>
                            <textarea
                                v-model="inquiry.summary"
                                class="contact-page__input contact-page__input--textarea"
                                name="summary"
                                rows="6"
                                placeholder="Scope, constraints, timeline, and what is currently painful."
                            />
                            <span class="type-meta contact-page__field-meta">
                                Keep it short. The button below opens a drafted
                                email with these details.
                            </span>
                        </label>
                    </form>

                    <div class="contact-page__form-actions">
                        <Button :href="mailtoHref">Compose email</Button>
                        <Button
                            :href="`mailto:${props.contact.email}`"
                            variant="secondary"
                        >
                            Email directly
                        </Button>
                    </div>
                </Panel>

                <div class="contact-page__aside">
                    <Panel class="contact-page__details" tone="grid">
                        <p class="type-eyebrow">Details</p>
                        <ContentMetaRow :items="inquiryMeta" />

                        <dl class="contact-page__detail-list">
                            <div class="contact-page__detail">
                                <dt class="type-nav">Email</dt>
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
                                <dt class="type-nav">Location</dt>
                                <dd class="type-body-sm">
                                    {{ props.contact.location }}
                                </dd>
                            </div>

                            <div class="contact-page__detail">
                                <dt class="type-nav">Availability</dt>
                                <dd class="type-body-sm">
                                    {{ props.contact.availability }}
                                </dd>
                            </div>
                        </dl>
                    </Panel>

                    <Panel class="contact-page__services" tone="surface">
                        <p class="type-eyebrow">Where I can help</p>

                        <ul class="contact-page__service-list">
                            <li
                                v-for="(service, index) in props.services"
                                :key="service"
                                class="contact-page__service-item"
                            >
                                <LegendChip
                                    :label="`Focus 0${index + 1}`"
                                    :tone="serviceTones[index] ?? 'violet'"
                                />
                                <p class="type-body contact-page__service-copy">
                                    {{ service }}
                                </p>
                            </li>
                        </ul>
                    </Panel>

                    <Panel class="contact-page__services" tone="grid">
                        <p class="type-eyebrow">Recruiter shortcut</p>
                        <p class="type-body-sm contact-page__service-copy">
                            If you need a faster handoff than a first call, the
                            current English and French CVs are available here.
                        </p>

                        <div class="contact-page__form-actions">
                            <Button
                                v-for="download in props.cvDownloads"
                                :key="download.href"
                                :href="download.href"
                            >
                                {{ download.label }}
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
