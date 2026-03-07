<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import Panel from '@/components/ui/Panel.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { AdminAuditLogEntry } from '@/types';

const props = defineProps<{
    entries: AdminAuditLogEntry[];
}>();

const entries = computed(() =>
    props.entries.map((entry) => ({
        ...entry,
        actorLabel:
            entry.actor.email ??
            entry.actor.name ??
            (entry.actor.type === 'console'
                ? 'Console command'
                : 'Unknown actor'),
        timestampLabel: entry.created_at
            ? new Intl.DateTimeFormat('en', {
                  dateStyle: 'medium',
                  timeStyle: 'short',
              }).format(new Date(entry.created_at))
            : 'Unknown time',
    })),
);

function actionLabel(action: string): string {
    if (action === 'site_settings.updated') {
        return 'Updated site settings';
    }

    if (action === 'operator.created') {
        return 'Created operator account';
    }

    return action;
}

function subjectLabel(subject: string): string {
    if (subject === 'site_settings') {
        return 'site_settings';
    }

    if (subject === 'operator_account') {
        return 'operator account';
    }

    return subject;
}
</script>

<template>
    <AdminLayout>
        <Head title="Admin Audit Log" />

        <div class="admin-audit-log">
            <header class="admin-audit-log__header">
                <div>
                    <p class="type-eyebrow">Admin audit log</p>
                    <h1 class="type-h1 admin-audit-log__title">
                        Review the latest sensitive operator actions.
                    </h1>
                    <p class="type-body admin-audit-log__copy">
                        This log is intentionally narrow. It currently records
                        successful site settings writes and operator bootstrap
                        actions without storing secrets or raw payload values.
                    </p>
                </div>
            </header>

            <div v-if="entries.length" class="admin-audit-log__entries">
                <Panel
                    v-for="entry in entries"
                    :key="entry.id"
                    class="admin-audit-log__entry"
                    tone="surface"
                >
                    <div class="admin-audit-log__entry-meta">
                        <span class="type-eyebrow">
                            {{ actionLabel(entry.action) }}
                        </span>
                        <span class="type-meta">
                            {{ entry.timestampLabel }}
                        </span>
                    </div>

                    <div class="admin-audit-log__entry-body">
                        <h2 class="type-h3 admin-audit-log__entry-title">
                            {{ subjectLabel(entry.subject) }}
                        </h2>
                        <p class="type-body-sm admin-audit-log__entry-copy">
                            Actor: {{ entry.actorLabel }}
                        </p>
                    </div>

                    <div
                        v-if="entry.summary.changed_groups?.length"
                        class="admin-audit-log__summary"
                    >
                        <p class="type-meta">
                            Changed groups:
                            {{ entry.summary.changed_groups.join(', ') }}
                        </p>
                        <p class="type-meta">
                            Changed fields:
                            {{ entry.summary.field_count }}
                        </p>
                        <p class="type-meta admin-audit-log__fields">
                            {{ entry.summary.changed_fields?.join(', ') }}
                        </p>
                    </div>

                    <div
                        v-else-if="entry.summary.created_user_email"
                        class="admin-audit-log__summary"
                    >
                        <p class="type-meta">
                            Created user:
                            {{ entry.summary.created_user_email }}
                        </p>
                        <p
                            v-if="entry.summary.created_user_name"
                            class="type-meta"
                        >
                            Name: {{ entry.summary.created_user_name }}
                        </p>
                    </div>
                </Panel>
            </div>

            <Panel v-else class="admin-audit-log__empty" tone="grid">
                <p class="type-eyebrow">No entries yet</p>
                <p class="type-body-sm">
                    Sensitive operator actions will appear here once the first
                    successful settings write or operator bootstrap happens.
                </p>
            </Panel>
        </div>
    </AdminLayout>
</template>

<style scoped>
.admin-audit-log {
    display: grid;
    gap: var(--sw-space-md);
}

.admin-audit-log__header {
    display: grid;
    gap: var(--sw-space-xs);
}

.admin-audit-log__title {
    margin: 0;
}

.admin-audit-log__copy {
    max-width: 70ch;
    color: var(--sw-text-secondary);
}

.admin-audit-log__entries {
    display: grid;
    gap: var(--sw-space-sm);
}

.admin-audit-log__entry {
    display: grid;
    gap: var(--sw-space-xs);
    padding: var(--sw-space-sm);
}

.admin-audit-log__entry-meta,
.admin-audit-log__summary {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem 1rem;
    align-items: center;
}

.admin-audit-log__entry-meta {
    justify-content: space-between;
}

.admin-audit-log__entry-title {
    margin: 0;
}

.admin-audit-log__entry-copy,
.admin-audit-log__fields {
    color: var(--sw-text-secondary);
}

.admin-audit-log__fields {
    width: 100%;
    overflow-wrap: anywhere;
}

.admin-audit-log__empty {
    display: grid;
    gap: 0.45rem;
    padding: var(--sw-space-sm);
}
@media (max-width: 640px) {
    .admin-audit-log__entry-meta {
        align-items: flex-start;
        flex-direction: column;
    }
}
</style>
