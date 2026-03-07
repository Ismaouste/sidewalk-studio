<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AdminSettingsSection from '@/components/admin/settings/AdminSettingsSection.vue';
import AdminSettingsSectionJumpNav from '@/components/admin/settings/AdminSettingsSectionJumpNav.vue';
import AdminSettingsToggleCard from '@/components/admin/settings/AdminSettingsToggleCard.vue';
import AdminSettingsValidationSummary from '@/components/admin/settings/AdminSettingsValidationSummary.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import { useUnsavedChangesWarning } from '@/composables/useUnsavedChangesWarning';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { FlashProps, SiteSettingsPayload } from '@/types';

const props = defineProps<{ settings: SiteSettingsPayload }>();
const page = usePage<{ flash: FlashProps }>();

const sectionLinks = [
    { href: '#site-identity', label: 'Site identity' },
    { href: '#contact-details', label: 'Contact details' },
    { href: '#social-links', label: 'Social links' },
    { href: '#seo-defaults', label: 'SEO defaults' },
    { href: '#consent-copy', label: 'Consent copy' },
    { href: '#feature-toggles', label: 'Feature toggles' },
] as const;

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
const saveLabel = computed(() =>
    form.processing ? 'Saving settings...' : 'Save settings',
);

useUnsavedChangesWarning(hasChanges);

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
                        <h2 class="type-h3 admin-settings__workflow-title">
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
                    <AdminSettingsValidationSummary :entries="errorSummary" />
                </Panel>

                <Panel class="admin-settings__summary" tone="grid">
                    <AdminSettingsSectionJumpNav :items="sectionLinks" />
                </Panel>

                <AdminSettingsSection
                    id="site-identity"
                    eyebrow="Site identity"
                    title="Name, tagline, and long description"
                    copy="These values shape the public shell and default metadata."
                >
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
                </AdminSettingsSection>

                <AdminSettingsSection
                    id="contact-details"
                    eyebrow="Contact details"
                    title="Public contact and availability"
                    copy="Treat this as operator-approved public contact copy."
                    tone="grid"
                >
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
                </AdminSettingsSection>

                <AdminSettingsSection
                    id="social-links"
                    eyebrow="Social links"
                    title="Public profile URLs"
                    copy="Empty values stay safely hidden on the public site."
                >
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
                </AdminSettingsSection>

                <AdminSettingsSection
                    id="seo-defaults"
                    eyebrow="SEO defaults"
                    title="Fallback metadata"
                    copy="Used only when a stronger page or collection payload is missing."
                    tone="grid"
                >
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
                </AdminSettingsSection>

                <AdminSettingsSection
                    id="consent-copy"
                    eyebrow="Consent copy"
                    title="Preferences and media prompts"
                    copy="Keep this copy plain and operational."
                >
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
                </AdminSettingsSection>

                <AdminSettingsSection
                    id="feature-toggles"
                    eyebrow="Feature toggles"
                    title="Bounded public switches"
                    copy="These switches intentionally gate only approved public surfaces."
                    tone="grid"
                >
                    <AdminSettingsToggleCard
                        v-model="form.feature_toggles.show_labs"
                        label="Show Labs"
                        description="Keep the public labs surface visible."
                    />
                    <AdminSettingsToggleCard
                        v-model="form.feature_toggles.show_writing"
                        label="Show Writing"
                        description="Keep the writing index and links visible."
                    />
                    <AdminSettingsToggleCard
                        v-model="form.feature_toggles.show_case_studies"
                        label="Show Case Studies"
                        description="Keep the case-studies surface visible."
                    />
                </AdminSettingsSection>
            </form>
        </div>
    </AdminLayout>
</template>

<style scoped src="./edit.css"></style>
