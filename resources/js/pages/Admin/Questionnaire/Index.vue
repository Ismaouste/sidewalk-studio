<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AdminHead from '@/components/admin/shared/AdminHead.vue';
import Panel from '@/components/ui/Panel.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { FlashProps } from '@/types';

type Question = {
    key: string;
    surface: string;
    hint: string;
    prompts: Record<string, string>;
    answers: Record<string, string>;
};

const props = defineProps<{
    locales: string[];
    questions: Question[];
    unanswered: number;
}>();

const page = usePage<{ flash: FlashProps }>();
const status = computed(() => page.props.flash?.status ?? null);

/**
 * One draft for the whole set, saved in one request.
 *
 * Answering three of four and losing them to a navigation is the failure this
 * screen cannot have, and per-card saves would invite exactly that.
 */
const answers = ref<Record<string, Record<string, string>>>(
    Object.fromEntries(
        props.questions.map((question) => [
            question.key,
            { ...question.answers },
        ]),
    ),
);

const saving = ref(false);

/** 280 is the cap the server enforces; the count is why, said in advance. */
const LIMIT = 280;

function remaining(key: string, locale: string): number {
    return LIMIT - (answers.value[key]?.[locale]?.length ?? 0);
}

function submit(): void {
    saving.value = true;
    router.put(
        '/admin/questionnaire',
        { answers: answers.value },
        { onFinish: () => (saving.value = false), preserveScroll: true },
    );
}
</script>

<template>
    <AdminLayout>
        <AdminHead title="Admin Questionnaire" />

        <form class="admin-questionnaire" @submit.prevent="submit">
            <header class="admin-questionnaire__header">
                <div>
                    <p class="type-eyebrow">Questionnaire</p>
                    <h1 class="type-h1 admin-questionnaire__title">
                        What the site asks you.
                    </h1>
                    <p class="type-body admin-questionnaire__copy">
                        An answer becomes a marginal note beside a position in
                        the chronology, with the question underneath it. Leave
                        one empty and its note simply does not appear.
                    </p>
                </div>

                <button
                    type="submit"
                    class="admin-questionnaire__save"
                    :disabled="saving"
                >
                    Save answers
                </button>
            </header>

            <p v-if="status" class="type-meta">{{ status }}</p>

            <p
                v-if="props.unanswered > 0"
                class="type-body-sm admin-questionnaire__hint"
            >
                {{ props.unanswered }} of
                {{ props.questions.length * props.locales.length }}
                answers are still open. Nothing breaks while they are — the page
                shows what exists.
            </p>

            <Panel
                v-for="question in props.questions"
                :key="question.key"
                class="admin-questionnaire__card"
                tone="surface"
            >
                <p class="type-meta admin-questionnaire__surface">
                    Appears on · {{ question.surface }}
                </p>
                <p class="type-body-sm admin-questionnaire__nudge">
                    {{ question.hint }}
                </p>

                <div class="admin-questionnaire__locales">
                    <label
                        v-for="locale in props.locales"
                        :key="locale"
                        class="admin-questionnaire__field"
                    >
                        <span class="type-eyebrow"
                            >{{ locale.toUpperCase() }} ·
                            {{ question.prompts[locale] }}</span
                        >
                        <textarea
                            v-model="answers[question.key]![locale]"
                            class="admin-questionnaire__input"
                            rows="3"
                            :maxlength="LIMIT"
                        ></textarea>
                        <span
                            class="type-meta admin-questionnaire__count"
                            :class="{
                                'admin-questionnaire__count--tight':
                                    remaining(question.key, locale) < 40,
                            }"
                            >{{ remaining(question.key, locale) }} left</span
                        >
                    </label>
                </div>
            </Panel>
        </form>
    </AdminLayout>
</template>

<style scoped>
.admin-questionnaire {
    display: grid;
    gap: var(--sw-space-2xs);
}

.admin-questionnaire__header {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    justify-content: space-between;
    gap: var(--sw-space-xs);
}

.admin-questionnaire__title {
    margin: var(--sw-space-4xs) 0 0;
}

.admin-questionnaire__copy,
.admin-questionnaire__nudge,
.admin-questionnaire__surface,
.admin-questionnaire__hint {
    color: var(--sw-text-secondary);
    max-inline-size: 68ch;
}

.admin-questionnaire__copy {
    margin: var(--sw-space-4xs) 0 0;
}

.admin-questionnaire__hint {
    border-inline-start: 2px solid var(--sw-accent-dominant);
    padding-inline-start: var(--sw-space-3xs);
}

.admin-questionnaire__save {
    border: 1px solid var(--sw-button-primary-bg);
    border-radius: var(--sw-radius-sm);
    padding: var(--sw-space-4xs) var(--sw-space-3xs);
    background: var(--sw-button-primary-bg);
    color: var(--sw-button-primary-text);
    font: inherit;
    cursor: pointer;
    min-block-size: 44px;
}

.admin-questionnaire__save:disabled {
    cursor: progress;
    opacity: 0.7;
}

.admin-questionnaire__locales {
    display: grid;
    gap: var(--sw-space-3xs);
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    margin-block-start: var(--sw-space-3xs);
}

.admin-questionnaire__field {
    display: grid;
    gap: var(--sw-space-4xs);
    min-inline-size: 0;
}

.admin-questionnaire__input {
    inline-size: 100%;
    border: 1px solid var(--sw-border);
    border-radius: var(--sw-radius-sm);
    padding: var(--sw-space-3xs);
    background: var(--sw-bg-base);
    color: var(--sw-text-primary);
    font: inherit;
    field-sizing: content;
    min-block-size: 44px;
}

.admin-questionnaire__count {
    color: var(--sw-text-muted);
    justify-self: end;
}

.admin-questionnaire__count--tight {
    color: var(--sw-accent-dominant);
}

@media (max-width: 640px) {
    .admin-questionnaire__header {
        flex-direction: column;
    }
}
</style>
