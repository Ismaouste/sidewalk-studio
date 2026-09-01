<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import ContentMetaRow from '@/components/design-system/ContentMetaRow.vue';
import LegendChip from '@/components/design-system/LegendChip.vue';
import SectionIntro from '@/components/design-system/SectionIntro.vue';
import SeoMeta from '@/components/SeoMeta.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import { copy as copyTree } from '@/copy';
import SiteLayout from '@/layouts/SiteLayout.vue';
import { capture } from '@/lib/analytics';
import type { FlashProps, SeoPayload, SiteContact, SiteProps } from '@/types';

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
        project_type_label: string;
        project_type_options: string[];
        budget_label: string;
        budget_options: string[];
        timeline_label: string;
        timeline_options: string[];
        primary_cta: string;
        secondary_cta: string;
    };
    details: {
        eyebrow: string;
        email_label: string;
        location_label: string;
        availability_label: string;
    };
    booking: {
        eyebrow: string;
        title: string;
        summary: string;
        cta_label: string;
        url: string;
    };
    cvDownloads: Array<{
        label: string;
        href: string;
    }>;
    services: {
        eyebrow: string;
        items: Array<{
            title: string;
            summary: string;
        }>;
    };
    recruiterShortcut: {
        eyebrow: string;
        summary: string;
    };
}>();
const page = usePage<{ site: SiteProps; flash: FlashProps }>();
const isStaticPreview = computed(() => page.props.site.runtime.staticPreview);

const serviceTones = ['dominant', 'green', 'coral'] as const;

const inquiry = useForm({
    name: '',
    email: '',
    company: '',
    summary: '',
    project_type: '',
    budget: '',
    timeline: '',
});

const copy = computed(() => copyTree[page.props.site.locale].pages.contact);

const inquiryMeta = computed(() => [
    {
        label: props.details.location_label,
        value: `📍 ${props.contact.location}`,
    },
    {
        label: copy.value.availabilityLabel,
        value: copy.value.availabilityText,
        tone: 'sun' as const,
    },
]);

const statusMessage = computed(() => page.props.flash?.status ?? null);
const whatsappHref = 'https://wa.me/33684907608';

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
        inquiry.project_type &&
            `${props.form.project_type_label}: ${inquiry.project_type}`,
        inquiry.budget && `${props.form.budget_label}: ${inquiry.budget}`,
        inquiry.timeline && `${props.form.timeline_label}: ${inquiry.timeline}`,
        '',
        inquiry.summary.trim() || copy.value.bodyBriefFallback,
    ].filter(Boolean);

    return `mailto:${props.contact.email}?subject=${encodeURIComponent(
        subjectBase,
    )}&body=${encodeURIComponent(lines.join('\n'))}`;
});

function submitInquiry(): void {
    if (typeof window !== 'undefined') {
        capture('lead_intent', { channel: 'email', funnel_stage: 'V3' });
        window.location.href = mailtoHref.value;
    }
}

function markWhatsappIntent(): void {
    capture('lead_intent', { channel: 'whatsapp', funnel_stage: 'V3' });
}

function markBookingIntent(): void {
    capture('lead_intent', { channel: 'booking', funnel_stage: 'V3' });
}
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section class="sw-section contact-page">
            <div class="contact-page__hero">
                <SectionIntro
                    :eyebrow="props.hero.eyebrow"
                    :title="props.hero.title"
                    :description="props.hero.summary"
                />

                <div class="contact-page__hero-toolbar">
                    <figure class="contact-page__portrait">
                        <img
                            src="/images/contact-avatar.webp"
                            :alt="copy.portraitAlt(page.props.site.name)"
                            class="contact-page__portrait-image"
                            width="336"
                            height="336"
                            loading="lazy"
                            decoding="async"
                        />
                    </figure>

                    <div class="contact-page__hero-toolbar-body">
                        <div class="contact-page__hero-actions">
                            <Button
                                :href="`mailto:${props.contact.email}`"
                                external
                                target="_blank"
                                rel="nofollow noopener noreferrer"
                            >
                                {{ copy.emailHeroCta }}
                            </Button>
                            <Button
                                class="contact-page__whatsapp-button"
                                variant="secondary"
                                :href="whatsappHref"
                                external
                                target="_blank"
                                rel="nofollow noopener noreferrer"
                                :aria-label="copy.whatsappLabel"
                                :title="copy.whatsappLabel"
                                @click="markWhatsappIntent"
                            >
                                <svg
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                    class="contact-page__whatsapp-icon"
                                >
                                    <path
                                        fill="currentColor"
                                        d="M12 3.2a8.8 8.8 0 0 0-7.5 13.4l-1 4.2 4.3-1A8.8 8.8 0 1 0 12 3.2Zm0 15.9a7.1 7.1 0 0 1-3.6-1l-.3-.2-2.5.6.6-2.4-.2-.4a7.1 7.1 0 1 1 6 3.4Zm3.9-5.3c-.2-.1-1.1-.5-1.3-.6s-.3-.1-.5.1-.5.6-.6.7-.2.2-.4.1a5.9 5.9 0 0 1-1.7-1.1 6.5 6.5 0 0 1-1.2-1.5c-.1-.2 0-.3.1-.4l.3-.3.2-.3.1-.4c0-.1-.5-1.2-.7-1.6s-.4-.3-.5-.3h-.4a.8.8 0 0 0-.6.3 2.5 2.5 0 0 0-.8 1.8c0 1.1.8 2.1.9 2.2s1.6 2.5 3.9 3.4c2.3 1 2.3.7 2.8.7s1.4-.5 1.6-1 .2-.9.1-1-.2-.1-.4-.2Z"
                                    />
                                </svg>
                            </Button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="contact-page__grid">
                <Panel
                    v-if="!isStaticPreview"
                    class="contact-page__form-panel"
                    tone="surface"
                >
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

                    <form
                        class="contact-page__form"
                        @submit.prevent="submitInquiry"
                    >
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

                        <label class="contact-page__field">
                            <span class="type-nav">
                                {{ props.form.project_type_label }}
                            </span>
                            <select
                                v-model="inquiry.project_type"
                                class="contact-page__input contact-page__input--select"
                                name="project_type"
                            >
                                <option value=""></option>
                                <option
                                    v-for="option in props.form
                                        .project_type_options"
                                    :key="option"
                                    :value="option"
                                >
                                    {{ option }}
                                </option>
                            </select>
                        </label>

                        <label class="contact-page__field">
                            <span class="type-nav">
                                {{ props.form.budget_label }}
                            </span>
                            <select
                                v-model="inquiry.budget"
                                class="contact-page__input contact-page__input--select"
                                name="budget"
                            >
                                <option value=""></option>
                                <option
                                    v-for="option in props.form.budget_options"
                                    :key="option"
                                    :value="option"
                                >
                                    {{ option }}
                                </option>
                            </select>
                        </label>

                        <label class="contact-page__field">
                            <span class="type-nav">
                                {{ props.form.timeline_label }}
                            </span>
                            <select
                                v-model="inquiry.timeline"
                                class="contact-page__input contact-page__input--select"
                                name="timeline"
                            >
                                <option value=""></option>
                                <option
                                    v-for="option in props.form
                                        .timeline_options"
                                    :key="option"
                                    :value="option"
                                >
                                    {{ option }}
                                </option>
                            </select>
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

                <Panel v-else class="contact-page__form-panel" tone="surface">
                    <div class="contact-page__form-intro">
                        <p class="type-eyebrow">
                            {{ copy.staticPreviewTitle }}
                        </p>
                        <h2 class="type-h2 contact-page__panel-title">
                            {{ props.form.title }}
                        </h2>
                        <p class="type-body-sm contact-page__panel-copy">
                            {{ copy.staticPreviewSummary }}
                        </p>
                    </div>

                    <div class="contact-page__form-actions">
                        <Button :href="`mailto:${props.contact.email}`">
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
                                    {{ copy.opportunitiesLabel }}
                                </dt>
                                <dd class="type-body-sm">
                                    {{ copy.opportunitiesText }}
                                </dd>
                            </div>
                        </dl>
                    </Panel>

                    <!--
                        Content-managed and self-hiding: the block appears the
                        day a booking URL is pasted into the admin, without a
                        deploy — and stays invisible until then.
                    -->
                    <Panel
                        v-if="props.booking.url"
                        class="contact-page__booking"
                        tone="grid"
                    >
                        <p class="type-eyebrow">{{ props.booking.eyebrow }}</p>
                        <h2 class="type-h3 contact-page__booking-title">
                            {{ props.booking.title }}
                        </h2>
                        <p class="type-body-sm contact-page__booking-summary">
                            {{ props.booking.summary }}
                        </p>
                        <div class="contact-page__booking-actions">
                            <Button
                                :href="props.booking.url"
                                target="_blank"
                                rel="noopener"
                                external
                                @click="markBookingIntent"
                            >
                                {{ props.booking.cta_label }}
                            </Button>
                        </div>
                    </Panel>

                    <Panel class="contact-page__services" tone="surface">
                        <p class="type-eyebrow">{{ props.services.eyebrow }}</p>

                        <ul class="contact-page__service-list">
                            <li
                                v-for="(service, index) in props.services.items"
                                :key="service.title"
                                class="contact-page__service-item"
                            >
                                <LegendChip
                                    :label="service.title"
                                    :tone="serviceTones[index] ?? 'violet'"
                                />
                                <p class="type-body contact-page__service-copy">
                                    {{ service.summary }}
                                </p>
                            </li>
                        </ul>
                    </Panel>

                    <Panel class="contact-page__services" tone="grid">
                        <p class="type-eyebrow">
                            {{ props.recruiterShortcut.eyebrow }}
                        </p>
                        <p
                            v-if="props.recruiterShortcut.summary"
                            class="type-body-sm contact-page__service-copy"
                        >
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

.contact-page__hero {
    display: grid;
    align-items: start;
    gap: var(--sw-space-sm);
    min-width: 0;
}

.contact-page__hero-toolbar {
    display: flex;
    align-items: center;
    gap: var(--sw-space-xs);
    flex-wrap: wrap;
    min-width: 0;
}

.contact-page__hero-toolbar-body {
    display: flex;
    flex: 1 1 18rem;
    align-items: center;
    flex-wrap: wrap;
    gap: var(--sw-space-2xs);
    min-width: 0;
    max-width: 100%;
}

.contact-page__hero-actions {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-2xs);
    align-items: center;
}

.contact-page__portrait {
    flex: none;
    margin: 0;
}

/*
 * Sizing only: this is `Button` in its secondary variant, so surface, ink,
 * hover and reduced-motion behaviour all come from the primitive. It used to
 * be a hand-rolled `<a>` painted with a literal WhatsApp green — the one
 * surface on the site blind to the theme, carrying a white glyph at 1.9:1,
 * and sitting outside the reduced-motion transform allowlist that its twin
 * `.sw-button` is inside. The glyph is what makes the affordance
 * recognizable; the green was never doing that work on its own.
 */
.contact-page__whatsapp-button {
    padding-inline: var(--sw-space-2xs);
}

.contact-page__whatsapp-icon {
    width: 1.1rem;
    height: 1.1rem;
}

.contact-page__portrait-image {
    display: block;
    width: 56px;
    height: 56px;
    border-radius: var(--sw-radius-pill);
    object-fit: cover;
}

.contact-page__grid {
    display: grid;
    gap: var(--sw-space-sm);
    align-items: start;
    grid-template-columns: minmax(0, 1.1fr) minmax(18rem, 0.9fr);
    min-width: 0;
}

.contact-page__aside {
    display: grid;
    gap: var(--sw-space-sm);
    min-width: 0;
}

.contact-page__form-panel,
.contact-page__services,
.contact-page__details {
    display: grid;
    gap: var(--sw-space-sm);
    min-width: 0;
    overflow-x: clip;
    padding: clamp(18px, 2.8vw, var(--sw-space-sm));
}

.contact-page__booking {
    display: grid;
    gap: var(--sw-space-xs);
    min-width: 0;
    padding: clamp(18px, 2.8vw, var(--sw-space-sm));
}

.contact-page__booking-title,
.contact-page__booking-summary {
    margin: 0;
}

.contact-page__booking-summary {
    color: var(--sw-text-secondary);
}

.contact-page__booking-actions {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-xs);
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
    overflow-wrap: anywhere;
    word-break: break-word;
}

.contact-page__form {
    display: grid;
    gap: var(--sw-space-xs);
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.contact-page__field {
    display: grid;
    gap: var(--sw-space-3xs);
}

.contact-page__field--full {
    grid-column: 1 / -1;
}

.contact-page__field-meta {
    color: var(--sw-text-muted);
}

.contact-page__status {
    margin: 0;
    border: 1px solid
        color-mix(in srgb, var(--sw-accent-green) 24%, transparent);
    border-radius: var(--sw-radius-md);
    background: color-mix(in srgb, var(--sw-accent-green) 8%, transparent);
    padding: 0.9rem 1rem;
    color: color-mix(
        in srgb,
        var(--sw-accent-green) 88%,
        var(--sw-text-primary)
    );
}

.contact-page__error {
    color: var(--sw-accent-coral);
}

.contact-page__input {
    min-height: 3rem;
    width: 100%;
    border: 1px solid color-mix(in srgb, var(--sw-border) 88%, transparent);
    border-radius: var(--sw-radius-md);
    background: color-mix(in srgb, var(--sw-bg-elevated) 88%, transparent);
    padding: 0.9rem 1rem;
    font: inherit;
    color: var(--sw-text-primary);
    transition:
        border-color var(--sw-motion-fast),
        background-color var(--sw-motion-fast),
        transform var(--sw-motion-fast);
}

.contact-page__input::placeholder {
    color: var(--sw-text-muted);
}

/*
 * A native select in the input's clothes. `appearance: none` drops the UA
 * arrow, so a token-colored chevron is drawn with two gradients on top of
 * the themed field background. The `:hover` selector below carries the
 * extra `.contact-page__field` ancestor because the shared input hover
 * writes the `background` shorthand, which would otherwise erase the arrow
 * the moment the pointer arrives.
 */
.contact-page__input--select,
.contact-page__field .contact-page__input--select:hover {
    appearance: none;
    cursor: pointer;
    background-image:
        linear-gradient(45deg, transparent 50%, var(--sw-text-secondary) 50%),
        linear-gradient(135deg, var(--sw-text-secondary) 50%, transparent 50%);
    background-position:
        calc(100% - 1.25rem) 50%,
        calc(100% - 0.95rem) 50%;
    background-size:
        0.3rem 0.3rem,
        0.3rem 0.3rem;
    background-repeat: no-repeat;
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
    overflow-wrap: anywhere;
    word-break: break-word;
}

.contact-page__detail-link {
    color: var(--sw-accent-dominant);
    text-decoration: underline;
    text-underline-offset: 0.2em;
    overflow-wrap: anywhere;
    word-break: break-word;
}

.contact-page__fit-items {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-3xs);
}

.contact-page__fit-item {
    display: inline-flex;
    align-items: center;
    color: var(--sw-text-secondary);
    overflow-wrap: anywhere;
    word-break: break-word;
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
    box-shadow: 0 0 0 3px
        color-mix(in srgb, var(--sw-border-focus) 18%, transparent);
    outline: none;
}

@media (max-width: 960px) {
    .contact-page__grid {
        grid-template-columns: minmax(0, 1fr);
    }
}

@media (max-width: 640px) {
    .contact-page {
        gap: var(--sw-space-3xs);
    }

    .contact-page__hero {
        gap: var(--sw-space-3xs);
    }

    .contact-page__portrait-image {
        width: 44px;
        height: 44px;
    }

    .contact-page__hero-toolbar {
        flex-direction: row;
        flex-wrap: wrap;
        align-items: center;
        gap: var(--sw-space-3xs);
    }

    .contact-page__hero-toolbar-body {
        flex: 1 1 auto;
        width: auto;
        min-width: 0;
    }

    .contact-page__hero-actions {
        flex: 1 1 auto;
        gap: var(--sw-space-3xs);
    }

    .contact-page__form {
        grid-template-columns: minmax(0, 1fr);
    }

    .contact-page__form-actions {
        display: grid;
    }
}
</style>
