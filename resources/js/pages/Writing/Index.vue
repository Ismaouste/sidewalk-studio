<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import ContentVisual from '@/components/content/ContentVisual.vue';
import LegendChip from '@/components/design-system/LegendChip.vue';
import SectionIntro from '@/components/design-system/SectionIntro.vue';
import SeoMeta from '@/components/SeoMeta.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import { useLocalMemory } from '@/composables/useLocalMemory';
import { copy as copyTree } from '@/copy';
import SiteLayout from '@/layouts/SiteLayout.vue';
import { formatPublicDate } from '@/lib/formatDate';
import type { ContentItem, SeoPayload, SiteProps } from '@/types';

const props = defineProps<{
    seo: SeoPayload;
    items: ContentItem[];
}>();

const page = usePage<{ site: SiteProps }>();

function writingMeta(item: ContentItem) {
    return [
        formatPublicDate(item.published_at, page.props.site.locale, 'month'),
        `${item.reading_time} min`,
    ];
}

const copy = computed(
    () => copyTree[page.props.site.locale].pages.writingIndex,
);
const landmarks = computed(
    () => copyTree[page.props.site.locale].layout.landmarks,
);

function entryLabel(item: ContentItem): string {
    return item.category === 'journal'
        ? copy.value.entryLabelJournal
        : copy.value.entryLabelNote;
}

/**
 * Which entries are new is a fact about this browser, so it cannot be part of
 * the server-rendered markup — everyone is served the same HTML. The set stays
 * empty through the first render and fills in on mount, which is also why
 * there is no hydration mismatch to reconcile.
 */
const newSlugs = ref(new Set<string>());

onMounted(() => {
    const { isNewSinceLastVisit } = useLocalMemory();

    newSlugs.value = new Set(
        props.items
            .filter((item) => isNewSinceLastVisit(item.published_at))
            .map((item) => item.slug),
    );
});
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section class="sw-section writing-index">
            <SectionIntro
                :eyebrow="copy.eyebrow"
                :title="copy.title"
                :description="copy.description"
            >
                <template #actions>
                    <Button href="/experience">{{ copy.projectsCta }}</Button>
                    <Button href="/contact" variant="secondary">
                        {{ copy.contactCta }}
                    </Button>
                </template>

                <LegendChip :label="copy.editorialLabel" tone="violet" />
                <LegendChip
                    :label="copy.publishedEntriesLabel(props.items.length)"
                    tone="sun"
                />
            </SectionIntro>

            <div class="writing-index__list">
                <Link
                    v-for="item in props.items"
                    :key="item.slug"
                    :href="item.url"
                    prefetch="hover"
                    cache-for="30s"
                    class="writing-index__link"
                    :data-new="newSlugs.has(item.slug) ? '' : null"
                >
                    <Panel class="writing-index__card" tone="surface">
                        <ContentVisual :item="item" compact />
                        <div class="writing-index__body">
                            <div class="writing-index__meta">
                                <span
                                    class="writing-index__new"
                                    :title="copy.newBadgeDescription"
                                >
                                    {{ copy.newBadge }}
                                </span>
                                <LegendChip
                                    :label="entryLabel(item)"
                                    :tone="
                                        item.category === 'journal'
                                            ? 'violet'
                                            : 'coral'
                                    "
                                />
                                <span
                                    v-for="value in writingMeta(item)"
                                    :key="value"
                                    class="type-meta writing-index__meta-item"
                                >
                                    {{ value }}
                                </span>
                            </div>

                            <h2 class="type-h2 writing-index__title">
                                {{ item.title }}
                            </h2>

                            <p class="type-body writing-index__summary">
                                {{ item.summary }}
                            </p>

                            <div class="writing-index__tags">
                                <span
                                    v-for="tag in item.tags"
                                    :key="tag"
                                    class="type-meta writing-index__tag"
                                >
                                    {{ tag }}
                                </span>
                            </div>
                        </div>
                    </Panel>
                </Link>
            </div>

            <nav class="writing-index__nudge" :aria-label="landmarks.nextStep">
                <Button href="/contact" variant="ghost" arrow>
                    {{ copy.nudgeContactCta }}
                </Button>
            </nav>
        </section>
    </SiteLayout>
</template>

<style scoped>
.writing-index {
    display: grid;
    gap: var(--sw-space-sm);
}

.writing-index__list {
    display: grid;
    gap: var(--sw-space-sm);
    max-width: 60rem;
}

.writing-index__link {
    display: block;
    border-radius: var(--sw-radius-lg);
}

.writing-index__card {
    display: grid;
    gap: var(--sw-space-xs);
    padding: clamp(12px, 2vw, 16px);
    align-items: stretch;
    transition:
        transform var(--sw-motion-fast),
        border-color var(--sw-motion-fast),
        background-color var(--sw-motion-fast);
}

.writing-index__body {
    display: grid;
    gap: var(--sw-space-xs);
    align-content: start;
}

.writing-index__meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.55rem;
    align-items: baseline;
    justify-content: flex-start;
}

.writing-index__meta-item {
    display: inline-flex;
    align-items: baseline;
    color: var(--sw-text-muted);
}

/* The badge ships on every card and CSS decides which ones show it, so the
   only thing script does is set an attribute. `display: none` also keeps the
   hidden ones out of the accessibility tree, which a visually-hidden
   treatment would not.

   Outlined, not filled, and the label takes --sw-text-primary rather than an
   accent. Both filled and tinted treatments were measured and both fail in
   morning at this size — accent-on-transparent lands at 3.7:1, and the
   primary button's own pair is near-white on orange at 2.95:1. Text-primary
   is legible on a card surface in either theme by construction, so the accent
   carries the emphasis through the border where contrast rules are 3:1 and
   not 4.5:1. Typography mirrors LegendChip, which sits beside it — two chips
   in one row should not be set in two typefaces. */
.writing-index__new {
    display: none;
    align-items: center;
    padding-inline: var(--sw-space-4xs);
    border: var(--sw-runtime-line-thickness, 1px) solid
        var(--sw-accent-dominant);
    border-radius: var(--sw-radius-sm);
    color: var(--sw-text-primary);
    font-family: var(--sw-font-heading);
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
}

.writing-index__link[data-new] .writing-index__new {
    display: inline-flex;
}

.writing-index__meta-item::before {
    content: '/';
    margin-right: 0.55rem;
    color: color-mix(in srgb, var(--sw-text-muted) 82%, transparent);
}

.writing-index__title,
.writing-index__summary {
    margin: 0;
}

.writing-index__title {
    color: var(--sw-text-primary);
}

.writing-index__summary {
    max-width: 48rem;
    color: var(--sw-text-secondary);
}

.writing-index__tags {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-3xs);
}

.writing-index__tag {
    display: inline-flex;
    align-items: center;
    color: var(--sw-text-muted);
}

.writing-index__link:focus-visible {
    outline: none;
}

.writing-index__link:focus-visible .writing-index__card,
.writing-index__link:active .writing-index__card {
    border-color: var(--sw-border-focus);
}

.writing-index__link:active .writing-index__card {
    transform: translateY(1px);
}

@media (hover: hover) {
    .writing-index__link:hover .writing-index__card {
        transform: translateY(-2px);
        border-color: var(--sw-card-hover-border);
        background: color-mix(in srgb, var(--sw-bg-elevated) 86%, transparent);
    }
}

@media (min-width: 720px) {
    .writing-index__card {
        grid-template-columns: minmax(5.8rem, 7rem) minmax(0, 1fr);
        column-gap: var(--sw-space-sm);
        row-gap: var(--sw-space-2xs);
    }

    .writing-index__card :deep(.content-visual) {
        min-height: 100%;
        height: 100%;
    }
}

.writing-index__nudge {
    display: flex;
    justify-content: flex-end;
    padding-top: var(--sw-space-sm);
    border-top: 1px solid color-mix(in srgb, var(--sw-border) 56%, transparent);
}

@media (max-width: 640px) {
    .writing-index {
        gap: var(--sw-space-xs);
    }

    .writing-index__meta {
        align-items: start;
        justify-content: flex-start;
    }

    .writing-index__nudge {
        justify-content: stretch;
    }

    .writing-index__nudge :deep(.sw-button) {
        width: 100%;
        justify-content: space-between;
    }
}
</style>
