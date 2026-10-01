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
const copy = computed(() => copyTree[page.props.site.locale].pages.notFound);
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section class="sw-section not-found">
            <SectionIntro
                :eyebrow="copy.eyebrow"
                :title="copy.title"
                :description="copy.summary"
            >
                <template #actions>
                    <Button href="/">{{ copy.backHomeCta }}</Button>
                    <Button href="/work" variant="secondary">
                        {{ copy.workCta }}
                    </Button>
                    <Button href="/contact" variant="secondary">
                        {{ copy.contactCta }}
                    </Button>
                </template>
            </SectionIntro>
        </section>
    </SiteLayout>
</template>
