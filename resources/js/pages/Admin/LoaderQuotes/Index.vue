<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import QuoteLinePreview from '@/components/shared/QuoteLinePreview.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { FlashProps, LoaderQuoteAdminEntry } from '@/types';

const props = defineProps<{
    quotes: LoaderQuoteAdminEntry[];
}>();

const page = usePage<{ flash: FlashProps }>();
const status = computed(() => page.props.flash?.status ?? null);
const form = useForm({
    quotes: props.quotes.map((quote) => ({ ...quote })),
});

function addQuote(locale: 'en' | 'fr') {
    form.quotes.push({
        text: '',
        type: 'message',
        author: null,
        locale,
        is_active: true,
        theme_target: null,
        weight: 0,
    });
}

function submit() {
    form.put('/admin/loader-quotes');
}
</script>

<template>
    <AdminLayout>
        <Head title="Admin Loader Quotes" />

        <form class="admin-quotes" @submit.prevent="submit">
            <header class="admin-quotes__header">
                <div>
                    <p class="type-eyebrow">Loader quotes</p>
                    <h1 class="type-h1">
                        Managed flash lines for loader and admin hover states.
                    </h1>
                    <p class="type-body admin-quotes__copy">
                        Edit inline, preview instantly, and keep the same quote
                        library across runtime surfaces.
                    </p>
                </div>
                <div class="admin-quotes__actions">
                    <span v-if="status" class="type-meta">{{ status }}</span>
                    <Button type="submit" :disabled="form.processing"
                        >Save loader quotes</Button
                    >
                </div>
            </header>

            <div class="admin-quotes__buttons">
                <Button
                    type="button"
                    variant="secondary"
                    @click="addQuote('en')"
                    >Add English line</Button
                >
                <Button
                    type="button"
                    variant="secondary"
                    @click="addQuote('fr')"
                    >Add French line</Button
                >
            </div>

            <div class="admin-quotes__list">
                <Panel
                    v-for="(quote, index) in form.quotes"
                    :key="`${quote.id ?? 'new'}-${index}`"
                    class="admin-quotes__card"
                    tone="surface"
                >
                    <div class="admin-quotes__fields">
                        <label
                            class="admin-quotes__field admin-quotes__field--wide"
                        >
                            <span class="type-meta">Text</span>
                            <textarea
                                v-model="quote.text"
                                class="admin-quotes__input admin-quotes__input--textarea"
                                rows="3"
                            />
                        </label>
                        <label class="admin-quotes__field">
                            <span class="type-meta">Type</span>
                            <select
                                v-model="quote.type"
                                class="admin-quotes__input"
                            >
                                <option value="message">Message</option>
                                <option value="quote">Quote</option>
                            </select>
                        </label>
                        <label class="admin-quotes__field">
                            <span class="type-meta">Locale</span>
                            <select
                                v-model="quote.locale"
                                class="admin-quotes__input"
                            >
                                <option value="en">English</option>
                                <option value="fr">French</option>
                            </select>
                        </label>
                        <label class="admin-quotes__field">
                            <span class="type-meta">Author</span>
                            <input
                                v-model="quote.author"
                                class="admin-quotes__input"
                                type="text"
                            />
                        </label>
                        <label class="admin-quotes__field">
                            <span class="type-meta">Theme target</span>
                            <select
                                v-model="quote.theme_target"
                                class="admin-quotes__input"
                            >
                                <option :value="null">All themes</option>
                                <option value="morning">Morning</option>
                                <option value="sunset">Sunset</option>
                            </select>
                        </label>
                        <label class="admin-quotes__field">
                            <span class="type-meta">Weight</span>
                            <input
                                v-model="quote.weight"
                                class="admin-quotes__input"
                                type="number"
                                min="0"
                                max="999"
                            />
                        </label>
                        <label class="admin-quotes__toggle">
                            <input v-model="quote.is_active" type="checkbox" />
                            <span>Active</span>
                        </label>
                    </div>

                    <div class="admin-quotes__preview">
                        <p class="type-eyebrow">Live preview</p>
                        <QuoteLinePreview
                            :text="quote.text || 'Preview text'"
                            :type="quote.type"
                            :author="quote.author"
                        />
                    </div>
                </Panel>
            </div>
        </form>
    </AdminLayout>
</template>

<style scoped>
.admin-quotes,
.admin-quotes__list,
.admin-quotes__card,
.admin-quotes__fields {
    display: grid;
    gap: 1rem;
}

.admin-quotes__header,
.admin-quotes__actions,
.admin-quotes__buttons {
    display: flex;
    gap: 1rem;
}

.admin-quotes__header {
    justify-content: space-between;
}

.admin-quotes__buttons {
    flex-wrap: wrap;
}

.admin-quotes__card {
    padding: 1rem;
}

.admin-quotes__fields {
    grid-template-columns: repeat(3, minmax(0, 1fr));
}

.admin-quotes__field {
    display: grid;
    gap: 0.45rem;
}

.admin-quotes__field--wide {
    grid-column: 1 / -1;
}

.admin-quotes__input {
    min-height: 3rem;
    border: 1px solid var(--sw-border);
    border-radius: var(--sw-radius-md);
    background: color-mix(in srgb, var(--sw-bg-base) 92%, transparent);
    padding: 0.85rem 0.95rem;
}

.admin-quotes__input--textarea {
    min-height: 6rem;
    resize: vertical;
}

.admin-quotes__toggle {
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
}

.admin-quotes__preview {
    border-top: 1px solid var(--sw-border);
    padding-top: 1rem;
}

@media (max-width: 960px) {
    .admin-quotes__fields {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 720px) {
    .admin-quotes__header {
        flex-direction: column;
    }

    .admin-quotes__fields {
        grid-template-columns: minmax(0, 1fr);
    }
}
</style>
