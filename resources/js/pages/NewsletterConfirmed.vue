<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import SectionIntro from '@/components/design-system/SectionIntro.vue';
import SeoMeta from '@/components/SeoMeta.vue';
import Button from '@/components/ui/Button.vue';
import { copy as copyTree } from '@/copy';
import SiteLayout from '@/layouts/SiteLayout.vue';
import type { SeoPayload, SiteProps } from '@/types';

const props = defineProps<{ seo: SeoPayload }>();

const page = usePage<{ site: SiteProps }>();
const copy = computed(
    () => copyTree[page.props.site.locale].pages.newsletterConfirmed,
);
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section class="sw-section newsletter-confirmed">
            <SectionIntro
                :eyebrow="copy.eyebrow"
                :title="copy.title"
                :description="copy.summary"
            >
                <template #actions>
                    <Button href="/journal">{{ copy.journalCta }}</Button>
                    <Button href="/" variant="secondary">
                        {{ copy.backHomeCta }}
                    </Button>
                </template>
            </SectionIntro>
        </section>
    </SiteLayout>
</template>
