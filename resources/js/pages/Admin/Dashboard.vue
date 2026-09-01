<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import AdminHead from '@/components/admin/shared/AdminHead.vue';
import Panel from '@/components/ui/Panel.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';

type AttentionItem = {
    key: string;
    count: number;
    label: string;
    consequence: string;
    href: string;
    detail?: Array<{ page_key: string; locale: string; count: number }>;
};

const props = defineProps<{
    attention: AttentionItem[];
    record: {
        positions: number;
        current: number;
        published: number;
        pages: number;
        locales: string[];
    };
    activity: Array<{
        id: number;
        action: string;
        subject: string;
        actor: string | null;
        at: string | null;
    }>;
    rebuildRequired: boolean;
}>();

const allClear = computed(() => props.attention.length === 0);
</script>

<template>
    <AdminLayout>
        <AdminHead title="Admin" />

        <div class="admin-dashboard">
            <header>
                <p class="type-eyebrow">Back office</p>
                <h1 class="type-h1 admin-dashboard__title">
                    {{
                        allClear
                            ? 'Nothing is waiting on you.'
                            : 'What is unfinished.'
                    }}
                </h1>
                <p class="type-body admin-dashboard__copy">
                    Everything below is derived, never stored. Each unfinished
                    thing says what the public site currently does about it, so
                    you never have to guess whether it is visibly broken or
                    quietly waiting.
                </p>
            </header>

            <p
                v-if="props.rebuildRequired"
                class="type-body-sm admin-dashboard__rebuild"
            >
                Content has changed since the last static export.
                <Link href="/admin/theme">Rebuild</Link> when you are done
                editing.
            </p>

            <section v-if="!allClear" class="admin-dashboard__attention">
                <Link
                    v-for="item in props.attention"
                    :key="item.key"
                    :href="item.href"
                    class="admin-dashboard__item"
                >
                    <span class="type-h3 admin-dashboard__count">{{
                        item.count
                    }}</span>
                    <span class="admin-dashboard__item-body">
                        <span class="type-nav">{{ item.label }}</span>
                        <span
                            class="type-body-sm admin-dashboard__consequence"
                            >{{ item.consequence }}</span
                        >
                        <span
                            v-if="item.detail"
                            class="type-meta admin-dashboard__detail"
                        >
                            <span
                                v-for="row in item.detail"
                                :key="`${row.page_key}-${row.locale}`"
                                >{{ row.page_key }} ·
                                {{ row.locale.toUpperCase() }} ({{
                                    row.count
                                }})</span
                            >
                        </span>
                    </span>
                </Link>
            </section>

            <section class="admin-dashboard__record">
                <h2 class="type-eyebrow">The record</h2>
                <div class="admin-dashboard__figures">
                    <Panel class="admin-dashboard__figure" tone="surface">
                        <p class="type-h3">{{ props.record.positions }}</p>
                        <p class="type-body-sm">
                            positions, {{ props.record.current }} of them
                            current
                        </p>
                    </Panel>
                    <Panel class="admin-dashboard__figure" tone="surface">
                        <p class="type-h3">{{ props.record.published }}</p>
                        <p class="type-body-sm">published entries</p>
                    </Panel>
                    <Panel class="admin-dashboard__figure" tone="surface">
                        <p class="type-h3">{{ props.record.pages }}</p>
                        <p class="type-body-sm">
                            page records across
                            {{ props.record.locales.length }} languages
                        </p>
                    </Panel>
                </div>
            </section>

            <section v-if="props.activity.length > 0">
                <h2 class="type-eyebrow">Recently</h2>
                <ul class="admin-dashboard__activity">
                    <li
                        v-for="entry in props.activity"
                        :key="entry.id"
                        class="type-body-sm"
                    >
                        <span class="admin-dashboard__action">{{
                            entry.action
                        }}</span>
                        <span class="type-meta"
                            >{{ entry.actor ?? 'unknown' }} ·
                            {{ entry.at }}</span
                        >
                    </li>
                </ul>
            </section>
        </div>
    </AdminLayout>
</template>

<style scoped>
.admin-dashboard {
    display: grid;
    gap: var(--sw-space-sm);
}

.admin-dashboard__title {
    margin: var(--sw-space-4xs) 0 0;
}

.admin-dashboard__copy,
.admin-dashboard__consequence,
.admin-dashboard__detail {
    color: var(--sw-text-secondary);
    max-inline-size: 68ch;
}

.admin-dashboard__copy {
    margin: var(--sw-space-4xs) 0 0;
}

.admin-dashboard__rebuild {
    border-inline-start: 2px solid var(--sw-accent-dominant);
    padding-inline-start: var(--sw-space-3xs);
    color: var(--sw-text-secondary);
}

.admin-dashboard__attention,
.admin-dashboard__activity {
    display: grid;
    gap: var(--sw-space-3xs);
}

.admin-dashboard__item {
    display: flex;
    align-items: baseline;
    gap: var(--sw-space-3xs);
    border: 1px solid var(--sw-border);
    border-radius: var(--sw-radius-md);
    padding: var(--sw-space-3xs);
    color: inherit;
    text-decoration: none;
    transition: border-color var(--sw-motion-fast);
}

@media (hover: hover) {
    .admin-dashboard__item:hover {
        border-color: var(--sw-accent-dominant);
    }
}

/**
 * The number is the only place the count is said, so it must not be crowded
 * by the label beside it. `flex-shrink: 0` because a two-digit count in a
 * `2ch` box was letting the label ride over its first digit.
 */
.admin-dashboard__count {
    color: var(--sw-accent-dominant);
    font-variant-numeric: tabular-nums;
    min-inline-size: 3ch;
    flex-shrink: 0;
    text-align: end;
}

.admin-dashboard__item-body,
.admin-dashboard__detail {
    display: grid;
    gap: var(--sw-space-4xs);
}

.admin-dashboard__detail {
    grid-auto-flow: column;
    grid-auto-columns: max-content;
    gap: var(--sw-space-3xs);
    overflow-x: auto;
}

.admin-dashboard__figures {
    display: grid;
    gap: var(--sw-space-3xs);
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    margin-block-start: var(--sw-space-3xs);
}

.admin-dashboard__figure {
    padding: var(--sw-space-3xs);
}

.admin-dashboard__activity {
    list-style: none;
    margin: var(--sw-space-3xs) 0 0;
    padding: 0;
}

.admin-dashboard__activity li {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: var(--sw-space-3xs);
    border-block-end: 1px solid
        color-mix(in srgb, var(--sw-border) 60%, transparent);
    padding-block: var(--sw-space-4xs);
}

.admin-dashboard__action {
    font-family: var(--sw-font-code);
}

@media (prefers-reduced-motion: reduce) {
    .admin-dashboard__item {
        transition: none;
    }
}
</style>
