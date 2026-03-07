<script setup lang="ts">
import LegendChip from '@/components/design-system/LegendChip.vue';
import SectionDivider from '@/components/design-system/SectionDivider.vue';
import SectionIntro from '@/components/design-system/SectionIntro.vue';
import SeoMeta from '@/components/SeoMeta.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import type { SeoPayload, SiteContact } from '@/types';

const props = defineProps<{
    seo: SeoPayload;
    contact: SiteContact;
    services: string[];
}>();

const serviceTones = ['dominant', 'green', 'coral'] as const;
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section class="sw-section contact-page">
            <SectionIntro
                eyebrow="Contact"
                title="Privacy, SEO, and Laravel modernization conversations."
                :description="props.contact.availability"
            >
                <template #actions>
                    <Button :href="`mailto:${props.contact.email}`">
                        Email directly
                    </Button>
                    <Button href="/case-studies" variant="secondary">
                        Review case studies
                    </Button>
                </template>

                <LegendChip label="Privacy-first engagements" tone="green" />
                <LegendChip
                    :label="`${props.contact.location} base`"
                    tone="sun"
                />
            </SectionIntro>

            <SectionDivider label="Working focus" />

            <div class="contact-page__grid">
                <Panel class="contact-page__services" tone="surface">
                    <p class="type-eyebrow">Where I can help</p>

                    <ul class="contact-page__service-list">
                        <li
                            v-for="(service, index) in props.services"
                            :key="service"
                            class="contact-page__service-item"
                        >
                            <LegendChip
                                :label="`Focus 0${index + 1}`"
                                :tone="serviceTones[index] ?? 'violet'"
                            />
                            <p class="type-body contact-page__service-copy">
                                {{ service }}
                            </p>
                        </li>
                    </ul>
                </Panel>

                <Panel class="contact-page__details" tone="grid">
                    <p class="type-eyebrow">Details</p>

                    <dl class="contact-page__detail-list">
                        <div class="contact-page__detail">
                            <dt class="type-nav">Email</dt>
                            <dd class="type-body-sm">
                                <a
                                    class="contact-page__detail-link"
                                    :href="`mailto:${props.contact.email}`"
                                >
                                    {{ props.contact.email }}
                                </a>
                            </dd>
                        </div>

                        <div class="contact-page__detail">
                            <dt class="type-nav">Location</dt>
                            <dd class="type-body-sm">
                                {{ props.contact.location }}
                            </dd>
                        </div>

                        <div class="contact-page__detail">
                            <dt class="type-nav">Availability</dt>
                            <dd class="type-body-sm">
                                {{ props.contact.availability }}
                            </dd>
                        </div>
                    </dl>
                </Panel>
            </div>
        </section>
    </SiteLayout>
</template>

<style scoped>
.contact-page {
    display: grid;
    gap: var(--sw-space-sm);
}

.contact-page__grid {
    display: grid;
    gap: var(--sw-space-sm);
    align-items: start;
    grid-template-columns: minmax(0, 1.15fr) minmax(18rem, 0.85fr);
}

.contact-page__services,
.contact-page__details {
    display: grid;
    gap: var(--sw-space-sm);
    padding: var(--sw-space-sm);
}

.contact-page__service-list,
.contact-page__detail-list {
    display: grid;
    gap: var(--sw-space-xs);
    margin: 0;
    padding: 0;
}

.contact-page__service-item,
.contact-page__detail {
    display: grid;
    gap: var(--sw-space-3xs);
    border-top: 1px solid var(--sw-border);
    padding-top: var(--sw-space-xs);
}

.contact-page__service-item:first-child,
.contact-page__detail:first-child {
    border-top: 0;
    padding-top: 0;
}

.contact-page__service-copy,
.contact-page__detail dd {
    margin: 0;
    color: var(--sw-text-secondary);
}

.contact-page__detail-link {
    color: var(--sw-accent-dominant);
    text-decoration: underline;
    text-underline-offset: 0.2em;
}

@media (max-width: 960px) {
    .contact-page__grid {
        grid-template-columns: minmax(0, 1fr);
    }
}
</style>
