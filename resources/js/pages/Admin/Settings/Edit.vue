<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { FlashProps, SiteSettingsPayload } from '@/types';

const props = defineProps<{
    settings: SiteSettingsPayload;
}>();

const page = usePage<{ flash: FlashProps }>();
const form = useForm<SiteSettingsPayload>({
    site_identity: { ...props.settings.site_identity },
    contact_details: { ...props.settings.contact_details },
    social_links: { ...props.settings.social_links },
    seo_defaults: { ...props.settings.seo_defaults },
    consent_copy: { ...props.settings.consent_copy },
    feature_toggles: { ...props.settings.feature_toggles },
});

const hasChanges = computed(() => form.isDirty);
const status = computed(() => page.props.flash?.status ?? null);

function submit() {
    form.put('/admin/settings', {
        preserveScroll: true,
    });
}

function errorFor(field: string) {
    return form.errors[field as keyof typeof form.errors];
}
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
                        <code>site_settings</code> aggregate. Secrets and
                        provider credentials still stay in <code>.env</code>.
                    </p>
                </div>

                <div class="admin-settings__actions">
                    <span
                        v-if="status"
                        class="type-meta admin-settings__status"
                    >
                        {{ status }}
                    </span>
                    <Button
                        type="submit"
                        :disabled="form.processing || !hasChanges"
                        @click="submit"
                    >
                        Save settings
                    </Button>
                </div>
            </header>

            <form class="admin-settings__grid" @submit.prevent="submit">
                <Panel class="admin-settings__panel" tone="surface">
                    <div class="admin-settings__panel-intro">
                        <p class="type-eyebrow">Site identity</p>
                        <h2 class="type-h3">
                            Name, tagline, and long description
                        </h2>
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
                        >
                            {{ errorFor('site_identity.name') }}
                        </span>
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
                        >
                            {{ errorFor('site_identity.tagline') }}
                        </span>
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
                        >
                            {{ errorFor('site_identity.description') }}
                        </span>
                    </label>
                </Panel>

                <Panel class="admin-settings__panel" tone="grid">
                    <div class="admin-settings__panel-intro">
                        <p class="type-eyebrow">Contact details</p>
                        <h2 class="type-h3">Public contact and availability</h2>
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
                        >
                            {{ errorFor('contact_details.email') }}
                        </span>
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
                        >
                            {{ errorFor('contact_details.location') }}
                        </span>
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
                        >
                            {{ errorFor('contact_details.availability') }}
                        </span>
                    </label>
                </Panel>

                <Panel class="admin-settings__panel" tone="surface">
                    <div class="admin-settings__panel-intro">
                        <p class="type-eyebrow">Social links</p>
                        <h2 class="type-h3">Public profile URLs</h2>
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
                        >
                            {{ errorFor('social_links.github_url') }}
                        </span>
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
                        >
                            {{ errorFor('social_links.linkedin_url') }}
                        </span>
                    </label>
                </Panel>

                <Panel class="admin-settings__panel" tone="grid">
                    <div class="admin-settings__panel-intro">
                        <p class="type-eyebrow">SEO defaults</p>
                        <h2 class="type-h3">Fallback metadata</h2>
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
                        >
                            {{ errorFor('seo_defaults.title_suffix') }}
                        </span>
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
                        >
                            {{ errorFor('seo_defaults.default_description') }}
                        </span>
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
                        >
                            {{ errorFor('seo_defaults.default_robots') }}
                        </span>
                    </label>
                </Panel>

                <Panel class="admin-settings__panel" tone="surface">
                    <div class="admin-settings__panel-intro">
                        <p class="type-eyebrow">Consent copy</p>
                        <h2 class="type-h3">Preferences and media prompts</h2>
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
                        >
                            {{ errorFor('consent_copy.preferences_title') }}
                        </span>
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
                        >
                            {{
                                errorFor('consent_copy.preferences_description')
                            }}
                        </span>
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
                        >
                            {{ errorFor('consent_copy.media_notice_title') }}
                        </span>
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
                        >
                            {{
                                errorFor(
                                    'consent_copy.media_notice_description',
                                )
                            }}
                        </span>
                    </label>
                </Panel>

                <Panel class="admin-settings__panel" tone="grid">
                    <div class="admin-settings__panel-intro">
                        <p class="type-eyebrow">Feature toggles</p>
                        <h2 class="type-h3">Bounded public switches</h2>
                    </div>

                    <label class="admin-settings__toggle">
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
                    </label>

                    <label class="admin-settings__toggle">
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
                    </label>

                    <label class="admin-settings__toggle">
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

.admin-settings__copy {
    max-width: 62ch;
    color: var(--sw-text-secondary);
}

.admin-settings__actions {
    display: grid;
    justify-items: end;
    gap: 0.6rem;
}

.admin-settings__status {
    color: var(--sw-accent-green);
}

.admin-settings__grid {
    display: grid;
    gap: var(--sw-space-md);
}

.admin-settings__panel {
    display: grid;
    gap: var(--sw-space-sm);
    padding: clamp(var(--sw-space-sm), 3vw, var(--sw-space-md));
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

.admin-settings__error {
    color: var(--sw-accent-coral);
}

.admin-settings__toggle {
    display: grid;
    grid-template-columns: auto minmax(0, 1fr);
    gap: 0.75rem;
    align-items: start;
    border: 1px solid color-mix(in srgb, var(--sw-border) 78%, transparent);
    border-radius: var(--sw-radius-md);
    background: color-mix(in srgb, var(--sw-bg-base) 78%, transparent);
    padding: 0.9rem 1rem;
}

.admin-settings__toggle input {
    margin-top: 0.2rem;
}

@media (prefers-reduced-motion: reduce) {
    .admin-settings__input {
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
}
</style>
