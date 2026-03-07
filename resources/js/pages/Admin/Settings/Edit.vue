<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted } from 'vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { FlashProps, SiteSettingsPayload } from '@/types';

const props = defineProps<{ settings: SiteSettingsPayload }>();
const page = usePage<{ flash: FlashProps }>();

const fieldLabels: Record<string, string> = {
    'site_identity.name': 'Site name',
    'site_identity.tagline': 'Tagline',
    'site_identity.description': 'Site description',
    'contact_details.email': 'Public email',
    'contact_details.location': 'Location',
    'contact_details.availability': 'Availability',
    'social_links.github_url': 'GitHub URL',
    'social_links.linkedin_url': 'LinkedIn URL',
    'seo_defaults.title_suffix': 'SEO title suffix',
    'seo_defaults.default_description': 'Default SEO description',
    'seo_defaults.default_robots': 'Default robots value',
    'consent_copy.preferences_title': 'Preferences title',
    'consent_copy.preferences_description': 'Preferences description',
    'consent_copy.media_notice_title': 'Media notice title',
    'consent_copy.media_notice_description': 'Media notice description',
};

function createFormState(settings: SiteSettingsPayload): SiteSettingsPayload {
    return {
        site_identity: { ...settings.site_identity },
        contact_details: { ...settings.contact_details },
        social_links: { ...settings.social_links },
        seo_defaults: { ...settings.seo_defaults },
        consent_copy: { ...settings.consent_copy },
        feature_toggles: { ...settings.feature_toggles },
    };
}

const form = useForm<SiteSettingsPayload>(createFormState(props.settings));
const hasChanges = computed(() => form.isDirty);
const status = computed(() => page.props.flash?.status ?? null);
const errorSummary = computed(() =>
    Object.keys(form.errors).map((field) => ({
        field,
        label: fieldLabels[field] ?? field.replaceAll('.', ' '),
        message: errorFor(field) ?? 'Validation error',
    })),
);
const saveLabel = computed(() => {
    if (form.processing) {
        return 'Saving settings...';
    }

    return 'Save settings';
});

function submit() {
    form.put('/admin/settings', { preserveScroll: true });
}

function resetForm() {
    form.defaults(createFormState(props.settings));
    form.reset();
    form.clearErrors();
}

function errorFor(field: string) {
    return form.errors[field as keyof typeof form.errors];
}

function beforeUnload(event: BeforeUnloadEvent) {
    if (!hasChanges.value) {
        return;
    }

    event.preventDefault();
    event.returnValue = '';
}

onMounted(() => {
    window.addEventListener('beforeunload', beforeUnload);
});

onBeforeUnmount(() => {
    window.removeEventListener('beforeunload', beforeUnload);
});
</script>

<template>
    <AdminLayout>
        <Head title="Admin Settings" />

        <div class="admin-settings">
            <header class="admin-settings__header">
                <div>
                    <p class="type-eyebrow">Site settings</p>
                    <h1 class="type-h1 admin-settings__title">
                        Edit the bounded runtime configuration.
                    </h1>
                    <p class="type-body admin-settings__copy">
                        This screen writes to the singleton
                        <code>site_settings</code>
                        aggregate. Secrets and provider credentials still stay
                        in
                        <code>.env</code>.
                    </p>
                </div>

                <div class="admin-settings__actions">
                    <span
                        v-if="status"
                        class="type-meta admin-settings__status"
                    >
                        {{ status }}
                    </span>
                    <span
                        v-else-if="hasChanges"
                        class="type-meta admin-settings__status admin-settings__status--draft"
                    >
                        Unsaved changes
                    </span>
                    <span
                        v-if="errorSummary.length"
                        class="type-meta admin-settings__status admin-settings__status--error"
                    >
                        {{ errorSummary.length }}
                        {{
                            errorSummary.length === 1
                                ? 'field needs attention'
                                : 'fields need attention'
                        }}
                    </span>
                    <div class="admin-settings__action-row">
                        <Button
                            type="button"
                            variant="secondary"
                            :disabled="form.processing || !hasChanges"
                            @click="resetForm"
                        >
                            Reset changes
                        </Button>
                        <Button
                            type="submit"
                            :disabled="form.processing || !hasChanges"
                            @click="submit"
                        >
                            {{ saveLabel }}
                        </Button>
                    </div>
                </div>
            </header>

            <form class="admin-settings__grid" @submit.prevent="submit">
                <Panel class="admin-settings__workflow" tone="elevated">
                    <div class="admin-settings__workflow-copy">
                        <p class="type-eyebrow">Operator workflow</p>
                        <h2 class="type-h3">
                            Save bounded runtime updates with care.
                        </h2>
                        <p class="type-body-sm admin-settings__workflow-note">
                            Public reads refresh immediately after save. Use
                            reset to discard local edits and keep secrets in
                            <code>.env</code>.
                        </p>
                    </div>
                    <div class="admin-settings__workflow-pills">
                        <span class="type-meta admin-settings__pill">
                            <strong>Source of truth:</strong>
                            <code>site_settings</code>
                        </span>
                        <span class="type-meta admin-settings__pill">
                            <strong>Scope:</strong> non-secret runtime
                            configuration
                        </span>
                    </div>
                </Panel>

                <Panel
                    v-if="errorSummary.length"
                    class="admin-settings__summary"
                    tone="surface"
                >
                    <p class="type-eyebrow">Validation summary</p>
                    <ul class="admin-settings__summary-list" role="alert">
                        <li
                            v-for="entry in errorSummary"
                            :key="entry.field"
                            class="admin-settings__summary-item"
                        >
                            <strong>{{ entry.label }}:</strong>
                            <span>{{ entry.message }}</span>
                        </li>
                    </ul>
                </Panel>

                <Panel class="admin-settings__summary" tone="grid">
                    <p class="type-eyebrow">Section guide</p>
                    <div class="admin-settings__guide-grid">
                        <a
                            class="admin-settings__guide-link"
                            href="#site-identity"
                            >Site identity</a
                        >
                        <a
                            class="admin-settings__guide-link"
                            href="#contact-details"
                            >Contact details</a
                        >
                        <a
                            class="admin-settings__guide-link"
                            href="#social-links"
                            >Social links</a
                        >
                        <a
                            class="admin-settings__guide-link"
                            href="#seo-defaults"
                            >SEO defaults</a
                        >
                        <a
                            class="admin-settings__guide-link"
                            href="#consent-copy"
                            >Consent copy</a
                        >
                        <a
                            class="admin-settings__guide-link"
                            href="#feature-toggles"
                            >Feature toggles</a
                        >
                    </div>
                </Panel>

                <Panel
                    id="site-identity"
                    class="admin-settings__panel"
                    tone="surface"
                >
                    <div class="admin-settings__panel-intro">
                        <p class="type-eyebrow">Site identity</p>
                        <h2 class="type-h3">
                            Name, tagline, and long description
                        </h2>
                        <p class="type-body-sm admin-settings__panel-copy">
                            These values shape the public shell and default
                            metadata.
                        </p>
                    </div>
                    <label class="admin-settings__field">
                        <span class="type-nav">Name</span>
                        <input
                            v-model="form.site_identity.name"
                            class="admin-settings__input"
                            type="text"
                        />
                        <span
                            v-if="errorFor('site_identity.name')"
                            class="type-meta admin-settings__error"
                            >{{ errorFor('site_identity.name') }}</span
                        >
                    </label>
                    <label class="admin-settings__field">
                        <span class="type-nav">Tagline</span>
                        <input
                            v-model="form.site_identity.tagline"
                            class="admin-settings__input"
                            type="text"
                        />
                        <span
                            v-if="errorFor('site_identity.tagline')"
                            class="type-meta admin-settings__error"
                            >{{ errorFor('site_identity.tagline') }}</span
                        >
                    </label>
                    <label class="admin-settings__field">
                        <span class="type-nav">Description</span>
                        <textarea
                            v-model="form.site_identity.description"
                            class="admin-settings__input admin-settings__input--textarea"
                            rows="5"
                        />
                        <span
                            v-if="errorFor('site_identity.description')"
                            class="type-meta admin-settings__error"
                            >{{ errorFor('site_identity.description') }}</span
                        >
                    </label>
                </Panel>

                <Panel
                    id="contact-details"
                    class="admin-settings__panel"
                    tone="grid"
                >
                    <div class="admin-settings__panel-intro">
                        <p class="type-eyebrow">Contact details</p>
                        <h2 class="type-h3">Public contact and availability</h2>
                        <p class="type-body-sm admin-settings__panel-copy">
                            Treat this as operator-approved public contact copy.
                        </p>
                    </div>
                    <label class="admin-settings__field">
                        <span class="type-nav">Email</span>
                        <input
                            v-model="form.contact_details.email"
                            class="admin-settings__input"
                            type="email"
                        />
                        <span
                            v-if="errorFor('contact_details.email')"
                            class="type-meta admin-settings__error"
                            >{{ errorFor('contact_details.email') }}</span
                        >
                    </label>
                    <label class="admin-settings__field">
                        <span class="type-nav">Location</span>
                        <input
                            v-model="form.contact_details.location"
                            class="admin-settings__input"
                            type="text"
                        />
                        <span
                            v-if="errorFor('contact_details.location')"
                            class="type-meta admin-settings__error"
                            >{{ errorFor('contact_details.location') }}</span
                        >
                    </label>
                    <label class="admin-settings__field">
                        <span class="type-nav">Availability</span>
                        <textarea
                            v-model="form.contact_details.availability"
                            class="admin-settings__input admin-settings__input--textarea"
                            rows="5"
                        />
                        <span
                            v-if="errorFor('contact_details.availability')"
                            class="type-meta admin-settings__error"
                            >{{
                                errorFor('contact_details.availability')
                            }}</span
                        >
                    </label>
                </Panel>

                <Panel
                    id="social-links"
                    class="admin-settings__panel"
                    tone="surface"
                >
                    <div class="admin-settings__panel-intro">
                        <p class="type-eyebrow">Social links</p>
                        <h2 class="type-h3">Public profile URLs</h2>
                        <p class="type-body-sm admin-settings__panel-copy">
                            Empty values stay safely hidden on the public site.
                        </p>
                    </div>
                    <label class="admin-settings__field">
                        <span class="type-nav">GitHub URL</span>
                        <input
                            v-model="form.social_links.github_url"
                            class="admin-settings__input"
                            type="url"
                        />
                        <span
                            v-if="errorFor('social_links.github_url')"
                            class="type-meta admin-settings__error"
                            >{{ errorFor('social_links.github_url') }}</span
                        >
                    </label>
                    <label class="admin-settings__field">
                        <span class="type-nav">LinkedIn URL</span>
                        <input
                            v-model="form.social_links.linkedin_url"
                            class="admin-settings__input"
                            type="url"
                        />
                        <span
                            v-if="errorFor('social_links.linkedin_url')"
                            class="type-meta admin-settings__error"
                            >{{ errorFor('social_links.linkedin_url') }}</span
                        >
                    </label>
                </Panel>

                <Panel
                    id="seo-defaults"
                    class="admin-settings__panel"
                    tone="grid"
                >
                    <div class="admin-settings__panel-intro">
                        <p class="type-eyebrow">SEO defaults</p>
                        <h2 class="type-h3">Fallback metadata</h2>
                        <p class="type-body-sm admin-settings__panel-copy">
                            Used only when a stronger page or collection payload
                            is missing.
                        </p>
                    </div>
                    <label class="admin-settings__field">
                        <span class="type-nav">Title suffix</span>
                        <input
                            v-model="form.seo_defaults.title_suffix"
                            class="admin-settings__input"
                            type="text"
                        />
                        <span
                            v-if="errorFor('seo_defaults.title_suffix')"
                            class="type-meta admin-settings__error"
                            >{{ errorFor('seo_defaults.title_suffix') }}</span
                        >
                    </label>
                    <label class="admin-settings__field">
                        <span class="type-nav">Default description</span>
                        <textarea
                            v-model="form.seo_defaults.default_description"
                            class="admin-settings__input admin-settings__input--textarea"
                            rows="5"
                        />
                        <span
                            v-if="errorFor('seo_defaults.default_description')"
                            class="type-meta admin-settings__error"
                            >{{
                                errorFor('seo_defaults.default_description')
                            }}</span
                        >
                    </label>
                    <label class="admin-settings__field">
                        <span class="type-nav">Robots</span>
                        <input
                            v-model="form.seo_defaults.default_robots"
                            class="admin-settings__input"
                            type="text"
                        />
                        <span
                            v-if="errorFor('seo_defaults.default_robots')"
                            class="type-meta admin-settings__error"
                            >{{ errorFor('seo_defaults.default_robots') }}</span
                        >
                    </label>
                </Panel>

                <Panel
                    id="consent-copy"
                    class="admin-settings__panel"
                    tone="surface"
                >
                    <div class="admin-settings__panel-intro">
                        <p class="type-eyebrow">Consent copy</p>
                        <h2 class="type-h3">Preferences and media prompts</h2>
                        <p class="type-body-sm admin-settings__panel-copy">
                            Keep this copy plain and operational.
                        </p>
                    </div>
                    <label class="admin-settings__field">
                        <span class="type-nav">Preferences title</span>
                        <input
                            v-model="form.consent_copy.preferences_title"
                            class="admin-settings__input"
                            type="text"
                        />
                        <span
                            v-if="errorFor('consent_copy.preferences_title')"
                            class="type-meta admin-settings__error"
                            >{{
                                errorFor('consent_copy.preferences_title')
                            }}</span
                        >
                    </label>
                    <label class="admin-settings__field">
                        <span class="type-nav">Preferences description</span>
                        <textarea
                            v-model="form.consent_copy.preferences_description"
                            class="admin-settings__input admin-settings__input--textarea"
                            rows="4"
                        />
                        <span
                            v-if="
                                errorFor('consent_copy.preferences_description')
                            "
                            class="type-meta admin-settings__error"
                            >{{
                                errorFor('consent_copy.preferences_description')
                            }}</span
                        >
                    </label>
                    <label class="admin-settings__field">
                        <span class="type-nav">Media notice title</span>
                        <input
                            v-model="form.consent_copy.media_notice_title"
                            class="admin-settings__input"
                            type="text"
                        />
                        <span
                            v-if="errorFor('consent_copy.media_notice_title')"
                            class="type-meta admin-settings__error"
                            >{{
                                errorFor('consent_copy.media_notice_title')
                            }}</span
                        >
                    </label>
                    <label class="admin-settings__field">
                        <span class="type-nav">Media notice description</span>
                        <textarea
                            v-model="form.consent_copy.media_notice_description"
                            class="admin-settings__input admin-settings__input--textarea"
                            rows="4"
                        />
                        <span
                            v-if="
                                errorFor(
                                    'consent_copy.media_notice_description',
                                )
                            "
                            class="type-meta admin-settings__error"
                            >{{
                                errorFor(
                                    'consent_copy.media_notice_description',
                                )
                            }}</span
                        >
                    </label>
                </Panel>

                <Panel
                    id="feature-toggles"
                    class="admin-settings__panel"
                    tone="grid"
                >
                    <div class="admin-settings__panel-intro">
                        <p class="type-eyebrow">Feature toggles</p>
                        <h2 class="type-h3">Bounded public switches</h2>
                        <p class="type-body-sm admin-settings__panel-copy">
                            These switches intentionally gate only approved
                            public surfaces.
                        </p>
                    </div>
                    <label
                        class="admin-settings__toggle"
                        :class="{
                            'admin-settings__toggle--checked':
                                form.feature_toggles.show_labs,
                        }"
                    >
                        <input
                            v-model="form.feature_toggles.show_labs"
                            type="checkbox"
                        />
                        <div>
                            <span class="type-nav">Show Labs</span>
                            <p class="type-body-sm">
                                Keep the public labs surface visible.
                            </p>
                        </div>
                        <span class="type-meta admin-settings__toggle-state">
                            {{
                                form.feature_toggles.show_labs
                                    ? 'Enabled'
                                    : 'Hidden'
                            }}
                        </span>
                    </label>
                    <label
                        class="admin-settings__toggle"
                        :class="{
                            'admin-settings__toggle--checked':
                                form.feature_toggles.show_writing,
                        }"
                    >
                        <input
                            v-model="form.feature_toggles.show_writing"
                            type="checkbox"
                        />
                        <div>
                            <span class="type-nav">Show Writing</span>
                            <p class="type-body-sm">
                                Keep the writing index and links visible.
                            </p>
                        </div>
                        <span class="type-meta admin-settings__toggle-state">
                            {{
                                form.feature_toggles.show_writing
                                    ? 'Enabled'
                                    : 'Hidden'
                            }}
                        </span>
                    </label>
                    <label
                        class="admin-settings__toggle"
                        :class="{
                            'admin-settings__toggle--checked':
                                form.feature_toggles.show_case_studies,
                        }"
                    >
                        <input
                            v-model="form.feature_toggles.show_case_studies"
                            type="checkbox"
                        />
                        <div>
                            <span class="type-nav">Show Case Studies</span>
                            <p class="type-body-sm">
                                Keep the case-studies surface visible.
                            </p>
                        </div>
                        <span class="type-meta admin-settings__toggle-state">
                            {{
                                form.feature_toggles.show_case_studies
                                    ? 'Enabled'
                                    : 'Hidden'
                            }}
                        </span>
                    </label>
                </Panel>
            </form>
        </div>
    </AdminLayout>
</template>

<style scoped>
.admin-settings {
    display: grid;
    gap: clamp(var(--sw-space-md), 4vw, var(--sw-space-lg));
}
.admin-settings__header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: var(--sw-space-sm);
}
.admin-settings__title {
    margin: 0;
}
.admin-settings__copy,
.admin-settings__workflow-note,
.admin-settings__panel-copy {
    max-width: 62ch;
    color: var(--sw-text-secondary);
}
.admin-settings__actions {
    display: grid;
    justify-items: end;
    gap: 0.6rem;
}
.admin-settings__action-row {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-xs);
}
.admin-settings__status {
    color: var(--sw-accent-green);
}
.admin-settings__status--draft {
    color: color-mix(in srgb, var(--sw-accent-sun) 78%, black 18%);
}
.admin-settings__status--error {
    color: var(--sw-accent-coral);
}
.admin-settings__grid {
    display: grid;
    gap: var(--sw-space-md);
}
.admin-settings__workflow,
.admin-settings__summary,
.admin-settings__panel {
    display: grid;
    gap: var(--sw-space-sm);
    padding: clamp(var(--sw-space-sm), 3vw, var(--sw-space-md));
}
.admin-settings__workflow-copy {
    display: grid;
    gap: 0.35rem;
}
.admin-settings__workflow-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}
.admin-settings__pill {
    border: 1px solid color-mix(in srgb, var(--sw-border) 78%, transparent);
    border-radius: 999px;
    background: color-mix(in srgb, var(--sw-bg-base) 78%, transparent);
    padding: 0.35rem 0.65rem;
    color: var(--sw-text-secondary);
}
.admin-settings__summary-list {
    display: grid;
    gap: 0.55rem;
    margin: 0;
    padding-left: 1.1rem;
}
.admin-settings__summary-item,
.admin-settings__error {
    color: var(--sw-accent-coral);
}
.admin-settings__guide-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}
.admin-settings__guide-link {
    border-radius: 999px;
    border: 1px solid color-mix(in srgb, var(--sw-border) 80%, transparent);
    background: color-mix(in srgb, var(--sw-bg-base) 76%, transparent);
    padding: 0.45rem 0.75rem;
    font-size: 0.9rem;
    color: var(--sw-text-primary);
    transition:
        border-color var(--sw-motion-fast),
        transform var(--sw-motion-fast);
}
.admin-settings__panel {
    scroll-margin-top: 150px;
}
.admin-settings__panel-intro {
    display: grid;
    gap: 0.35rem;
}
.admin-settings__field {
    display: grid;
    gap: 0.45rem;
}
.admin-settings__input {
    min-height: 3rem;
    border: 1px solid var(--sw-border);
    border-radius: var(--sw-radius-md);
    background: color-mix(in srgb, var(--sw-bg-base) 92%, transparent);
    padding-inline: 0.95rem;
    transition:
        border-color var(--sw-motion-fast),
        box-shadow var(--sw-motion-fast),
        background-color var(--sw-motion-fast);
}
.admin-settings__input--textarea {
    min-height: 7.5rem;
    resize: vertical;
    padding-block: 0.85rem;
}
.admin-settings__input:focus-visible {
    border-color: var(--sw-accent-dominant);
    outline: none;
    box-shadow: 0 0 0 4px
        color-mix(in srgb, var(--sw-accent-dominant) 14%, transparent);
}
.admin-settings__toggle {
    display: grid;
    grid-template-columns: auto minmax(0, 1fr) auto;
    gap: 0.75rem;
    align-items: start;
    border: 1px solid color-mix(in srgb, var(--sw-border) 78%, transparent);
    border-radius: var(--sw-radius-md);
    background: color-mix(in srgb, var(--sw-bg-base) 78%, transparent);
    padding: 0.9rem 1rem;
    transition:
        border-color var(--sw-motion-fast),
        transform var(--sw-motion-fast),
        background-color var(--sw-motion-fast);
}
.admin-settings__toggle--checked {
    border-color: color-mix(in srgb, var(--sw-accent-green) 42%, transparent);
    background: color-mix(in srgb, var(--sw-accent-green) 10%, transparent);
}
.admin-settings__toggle input {
    margin-top: 0.2rem;
}
.admin-settings__toggle-state {
    border-radius: 999px;
    background: color-mix(in srgb, var(--sw-bg-surface) 92%, transparent);
    padding: 0.35rem 0.65rem;
    color: var(--sw-text-secondary);
}
@media (hover: hover) {
    .admin-settings__guide-link:hover,
    .admin-settings__toggle:hover {
        border-color: var(--sw-accent-dominant);
        transform: translateY(-1px);
    }
}
@media (prefers-reduced-motion: reduce) {
    .admin-settings__guide-link,
    .admin-settings__input,
    .admin-settings__toggle {
        transition: none;
    }
}
@media (max-width: 720px) {
    .admin-settings__header {
        flex-direction: column;
    }
    .admin-settings__actions {
        width: 100%;
        justify-items: start;
    }
    .admin-settings__toggle {
        grid-template-columns: auto minmax(0, 1fr);
    }
    .admin-settings__toggle-state {
        justify-self: start;
    }
}
</style>
