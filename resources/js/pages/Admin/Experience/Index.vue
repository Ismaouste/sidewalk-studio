<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AdminHead from '@/components/admin/shared/AdminHead.vue';
import Panel from '@/components/ui/Panel.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { FlashProps } from '@/types';

type ExperienceRow = {
    id: number;
    translation_key: string;
    kind: string;
    organisation: string;
    role: string;
    date_range: string;
    is_current: boolean;
    is_undated: boolean;
    detail_group_count: number;
};

const props = defineProps<{
    locale: string;
    locales: string[];
    kinds: string[];
    entries: ExperienceRow[];
}>();

const page = usePage<{ flash: FlashProps }>();
const status = computed(() => page.props.flash?.status ?? null);

/**
 * The list is already in chronological order when it arrives — the ordering
 * is the record's, not the screen's, which is the whole point of the move to
 * rows. Grouping here only splits the one ordered list into its three
 * families; it never sorts.
 */
const families = computed(() =>
    props.kinds.map((kind) => ({
        kind,
        label: labelFor(kind),
        rows: props.entries.filter((entry) => entry.kind === kind),
    })),
);

const undatedCount = computed(
    () => props.entries.filter((entry) => entry.is_undated).length,
);

function labelFor(kind: string): string {
    return kind
        .split('_')
        .join(' ')
        .replace(/^./, (character) => character.toUpperCase());
}

function switchLocale(locale: string): void {
    router.get('/admin/experience', { locale }, { preserveScroll: true });
}
</script>

<template>
    <AdminLayout>
        <AdminHead title="Admin Experience" />

        <div class="admin-experience">
            <header class="admin-experience__header">
                <div>
                    <p class="type-eyebrow">Experience record</p>
                    <h1 class="type-h1 admin-experience__title">
                        Every position held, in the order its dates put it.
                    </h1>
                    <p class="type-body admin-experience__copy">
                        The public chronology is built from these rows. An entry
                        with no end date is the one being lived; an entry with
                        no start date closes the list.
                    </p>
                </div>

                <div class="admin-experience__actions">
                    <div
                        class="admin-experience__locales"
                        role="group"
                        aria-label="Language"
                    >
                        <button
                            v-for="code in props.locales"
                            :key="code"
                            type="button"
                            class="admin-experience__locale"
                            :class="{
                                'admin-experience__locale--active':
                                    code === props.locale,
                            }"
                            :aria-pressed="code === props.locale"
                            @click="switchLocale(code)"
                        >
                            {{ code.toUpperCase() }}
                        </button>
                    </div>
                    <Link
                        :href="`/admin/experience/create?locale=${props.locale}`"
                        class="admin-experience__add"
                        >Add a position</Link
                    >
                </div>
            </header>

            <p v-if="status" class="type-meta admin-experience__status">
                {{ status }}
            </p>

            <p
                v-if="undatedCount > 0"
                class="type-body-sm admin-experience__hint"
            >
                {{ undatedCount }}
                {{ undatedCount === 1 ? 'entry has' : 'entries have' }}
                no start date, so
                {{ undatedCount === 1 ? 'it is' : 'they are' }} ordered last and
                still shown by their written label. Give
                {{ undatedCount === 1 ? 'it' : 'them' }} a date and clear the
                label to let the record speak for itself.
            </p>

            <section
                v-for="family in families"
                :key="family.kind"
                class="admin-experience__family"
            >
                <h2 class="type-eyebrow">
                    {{ family.label }}
                    <span class="admin-experience__count">{{
                        family.rows.length
                    }}</span>
                </h2>

                <p
                    v-if="family.rows.length === 0"
                    class="type-body-sm admin-experience__empty"
                >
                    Nothing filed here yet.
                </p>

                <Panel
                    v-for="entry in family.rows"
                    :key="entry.id"
                    class="admin-experience__card"
                    tone="surface"
                >
                    <div class="admin-experience__card-head">
                        <div class="admin-experience__identity">
                            <p class="type-nav">{{ entry.organisation }}</p>
                            <p class="type-meta admin-experience__meta">
                                {{ entry.role }}
                                <template v-if="entry.date_range"
                                    >· {{ entry.date_range }}</template
                                >
                            </p>
                        </div>
                        <Link
                            :href="`/admin/experience/${entry.id}`"
                            class="admin-experience__link"
                            >Edit</Link
                        >
                    </div>

                    <p class="type-meta admin-experience__badges">
                        <span
                            v-if="entry.is_current"
                            class="admin-experience__badge admin-experience__badge--current"
                            >Current</span
                        >
                        <span
                            v-if="entry.is_undated"
                            class="admin-experience__badge"
                            >No start date</span
                        >
                        <span class="admin-experience__badge"
                            >{{ entry.detail_group_count }} detail
                            {{
                                entry.detail_group_count === 1
                                    ? 'group'
                                    : 'groups'
                            }}</span
                        >
                    </p>
                </Panel>
            </section>
        </div>
    </AdminLayout>
</template>

<style scoped>
.admin-experience,
.admin-experience__family {
    display: grid;
    gap: var(--sw-space-2xs);
}

.admin-experience {
    gap: var(--sw-space-sm);
}

.admin-experience__header {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    justify-content: space-between;
    gap: var(--sw-space-xs);
}

.admin-experience__title,
.admin-experience__copy {
    margin: var(--sw-space-4xs) 0 0;
    max-inline-size: 62ch;
}

.admin-experience__copy,
.admin-experience__meta,
.admin-experience__empty,
.admin-experience__hint {
    color: var(--sw-text-secondary);
}

.admin-experience__actions {
    display: flex;
    align-items: center;
    gap: var(--sw-space-3xs);
}

.admin-experience__locales {
    display: flex;
    gap: var(--sw-space-4xs);
}

.admin-experience__locale,
.admin-experience__add,
.admin-experience__link {
    border: 1px solid var(--sw-border);
    border-radius: var(--sw-radius-sm);
    padding: var(--sw-space-4xs) var(--sw-space-3xs);
    background: transparent;
    color: var(--sw-text-primary);
    font: inherit;
    text-decoration: none;
    cursor: pointer;
    /* 44px at the narrow breakpoint, like every other admin control. */
    min-block-size: 44px;
    display: inline-flex;
    align-items: center;
}

.admin-experience__locale--active {
    border-color: var(--sw-accent-dominant);
    color: var(--sw-accent-dominant);
}

.admin-experience__hint {
    border-inline-start: 2px solid var(--sw-accent-dominant);
    padding-inline-start: var(--sw-space-3xs);
    max-inline-size: 72ch;
}

.admin-experience__card-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: var(--sw-space-3xs);
}

.admin-experience__count {
    color: var(--sw-text-secondary);
}

.admin-experience__badges {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-4xs);
    margin: var(--sw-space-4xs) 0 0;
}

.admin-experience__badge {
    border: 1px solid var(--sw-border);
    border-radius: var(--sw-radius-full);
    padding: 0 var(--sw-space-3xs);
    color: var(--sw-text-secondary);
}

.admin-experience__badge--current {
    border-color: var(--sw-accent-dominant);
    color: var(--sw-accent-dominant);
}

@media (max-width: 640px) {
    .admin-experience__header,
    .admin-experience__card-head {
        flex-direction: column;
    }
}
</style>
