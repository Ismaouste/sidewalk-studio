<script setup lang="ts">
import { computed } from 'vue';
import AdminHead from '@/components/admin/shared/AdminHead.vue';
import Panel from '@/components/ui/Panel.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { ContactSubmissionEntry } from '@/types';

const props = defineProps<{
    submissions: ContactSubmissionEntry[];
}>();

const submissions = computed(() =>
    props.submissions.map((submission) => ({
        ...submission,
        createdAtLabel: submission.created_at
            ? new Intl.DateTimeFormat('en', {
                  dateStyle: 'medium',
                  timeStyle: 'short',
              }).format(new Date(submission.created_at))
            : 'Unknown time',
    })),
);
</script>

<template>
    <AdminLayout>
        <AdminHead title="Contact submissions" />

        <div class="admin-contact-submissions">
            <header class="admin-contact-submissions__header">
                <p class="type-eyebrow">Inbox</p>
                <h1 class="type-h1 admin-contact-submissions__title">
                    Review contact messages stored from the public form.
                </h1>
                <p class="type-body admin-contact-submissions__copy">
                    Messages are stored locally so the public site can stay
                    simple without relying on a third-party form provider.
                </p>
            </header>

            <div
                v-if="submissions.length"
                class="admin-contact-submissions__list"
            >
                <Panel
                    v-for="submission in submissions"
                    :key="submission.id"
                    class="admin-contact-submissions__item"
                    tone="surface"
                >
                    <div class="admin-contact-submissions__meta">
                        <span class="type-eyebrow">
                            {{ submission.locale.toUpperCase() }}
                        </span>
                        <span class="type-meta">
                            {{ submission.createdAtLabel }}
                        </span>
                    </div>

                    <div class="admin-contact-submissions__identity">
                        <h2 class="type-h3">
                            {{ submission.name }}
                        </h2>
                        <a
                            class="admin-contact-submissions__email"
                            :href="`mailto:${submission.email}`"
                        >
                            {{ submission.email }}
                        </a>
                        <p
                            v-if="submission.company"
                            class="type-body-sm admin-contact-submissions__company"
                        >
                            {{ submission.company }}
                        </p>
                    </div>

                    <p class="type-body admin-contact-submissions__summary">
                        {{ submission.summary }}
                    </p>
                </Panel>
            </div>

            <Panel v-else class="admin-contact-submissions__empty" tone="grid">
                <p class="type-eyebrow">No messages yet</p>
                <p class="type-body-sm">
                    Public contact submissions will appear here once the first
                    form is sent.
                </p>
            </Panel>
        </div>
    </AdminLayout>
</template>

<style scoped>
.admin-contact-submissions {
    display: grid;
    gap: var(--sw-space-md);
}

.admin-contact-submissions__header,
.admin-contact-submissions__list {
    display: grid;
    gap: var(--sw-space-sm);
}

.admin-contact-submissions__title,
.admin-contact-submissions__copy,
.admin-contact-submissions__summary {
    margin: 0;
}

.admin-contact-submissions__copy,
.admin-contact-submissions__company,
.admin-contact-submissions__summary {
    color: var(--sw-text-secondary);
}

.admin-contact-submissions__item {
    display: grid;
    gap: var(--sw-space-xs);
    padding: var(--sw-space-sm);
}

.admin-contact-submissions__meta {
    display: flex;
    justify-content: space-between;
    gap: var(--sw-space-xs);
}

.admin-contact-submissions__identity {
    display: grid;
    gap: 0.35rem;
}

.admin-contact-submissions__identity h2 {
    margin: 0;
}

.admin-contact-submissions__email {
    width: fit-content;
    color: var(--sw-accent-dominant);
    text-decoration: underline;
    text-underline-offset: 0.18em;
}

.admin-contact-submissions__empty {
    display: grid;
    gap: var(--sw-space-3xs);
    padding: var(--sw-space-sm);
}
</style>
