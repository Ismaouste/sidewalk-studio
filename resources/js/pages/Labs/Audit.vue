<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import SectionIntro from '@/components/design-system/SectionIntro.vue';
import SeoMeta from '@/components/SeoMeta.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import { copy as copyTree } from '@/copy';
import SiteLayout from '@/layouts/SiteLayout.vue';
import { capture } from '@/lib/analytics';
import type { SeoPayload, SiteProps } from '@/types';

type FieldMetric = { percentile: number; category: string } | null;

type AuditReport = {
    url: string;
    scores: { performance: number | null; seo: number | null };
    field: { lcp: FieldMetric; inp: FieldMetric; cls: FieldMetric };
    lab: { lcp: string | null; cls: string | null; tbt: string | null };
    opportunities: Array<{ title: string; savings: string }>;
};

const props = defineProps<{ seo: SeoPayload }>();

const page = usePage<{ site: SiteProps }>();
const copy = computed(() => copyTree[page.props.site.locale].pages.labsAudit);

const url = ref('');
const email = ref('');
const honeypot = ref('');
const state = ref<'idle' | 'running' | 'sent' | 'error'>('idle');
const report = ref<AuditReport | null>(null);

const fieldRows = computed(() => {
    if (!report.value) {
        return [];
    }

    const rows: Array<{ label: string; value: string; category: string }> = [];
    const { lcp, inp, cls } = report.value.field;

    if (lcp) {
        rows.push({
            label: 'LCP',
            value: `${(lcp.percentile / 1000).toFixed(1)} s`,
            category: lcp.category,
        });
    }
    if (inp) {
        rows.push({
            label: 'INP',
            value: `${inp.percentile} ms`,
            category: inp.category,
        });
    }
    if (cls) {
        rows.push({
            label: 'CLS',
            value: (cls.percentile / 100).toFixed(2),
            category: cls.category,
        });
    }

    return rows;
});

async function runAudit(): Promise<void> {
    if (state.value === 'running') {
        return;
    }

    state.value = 'running';
    report.value = null;

    try {
        const response = await fetch('/labs/audit', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            body: JSON.stringify({
                url: url.value,
                email: email.value,
                locale: page.props.site.locale,
                company_website: honeypot.value,
            }),
        });

        if (!response.ok) {
            state.value = 'error';
            return;
        }

        const body = (await response.json()) as {
            report: AuditReport | null;
        };
        report.value = body.report;
        state.value = 'sent';
        capture('lead_intent', { channel: 'audit', funnel_stage: 'V3' });
    } catch {
        state.value = 'error';
    }
}
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section class="sw-section labs-audit">
            <SectionIntro
                :eyebrow="copy.eyebrow"
                :title="copy.title"
                :description="copy.summary"
            />

            <Panel class="labs-audit__form-panel" tone="grid">
                <form class="labs-audit__form" @submit.prevent="runAudit">
                    <div class="labs-audit__field">
                        <label class="type-meta" for="audit-url">
                            {{ copy.urlLabel }}
                        </label>
                        <input
                            id="audit-url"
                            v-model="url"
                            class="labs-audit__input"
                            type="url"
                            required
                            :placeholder="copy.urlPlaceholder"
                        />
                    </div>
                    <div class="labs-audit__field">
                        <label class="type-meta" for="audit-email">
                            {{ copy.emailLabel }}
                        </label>
                        <input
                            id="audit-email"
                            v-model="email"
                            class="labs-audit__input"
                            type="email"
                            required
                            autocomplete="email"
                            :placeholder="copy.emailPlaceholder"
                        />
                    </div>
                    <input
                        v-model="honeypot"
                        class="labs-audit__trap"
                        type="text"
                        name="company_website"
                        tabindex="-1"
                        autocomplete="off"
                        aria-hidden="true"
                    />
                    <div class="labs-audit__actions">
                        <Button type="submit" :disabled="state === 'running'">
                            {{ copy.submitCta }}
                        </Button>
                    </div>
                    <p
                        v-if="state === 'running'"
                        class="type-body-sm labs-audit__note"
                        role="status"
                    >
                        {{ copy.runningNote }}
                    </p>
                    <p
                        v-else-if="state === 'error'"
                        class="type-body-sm labs-audit__error"
                    >
                        {{ copy.errorNote }}
                    </p>
                    <p class="type-meta labs-audit__privacy">
                        {{ copy.privacyNote }}
                    </p>
                </form>
            </Panel>

            <Panel
                v-if="state === 'sent' && report"
                class="labs-audit__report"
                tone="surface"
            >
                <p class="type-body-sm labs-audit__sent-note" role="status">
                    {{ copy.sentNote }}
                </p>

                <h2 class="type-h3 labs-audit__heading">
                    {{ copy.scoresHeading }}
                </h2>
                <div class="labs-audit__scores">
                    <div class="labs-audit__score">
                        <span class="type-display-l labs-audit__score-value">
                            {{ report.scores.performance ?? '—' }}
                        </span>
                        <span class="type-meta">
                            {{ copy.performanceLabel }}
                        </span>
                    </div>
                    <div class="labs-audit__score">
                        <span class="type-display-l labs-audit__score-value">
                            {{ report.scores.seo ?? '—' }}
                        </span>
                        <span class="type-meta">{{ copy.seoLabel }}</span>
                    </div>
                </div>

                <h2 class="type-h3 labs-audit__heading">
                    {{ copy.fieldHeading }}
                </h2>
                <dl v-if="fieldRows.length > 0" class="labs-audit__field-rows">
                    <div
                        v-for="row in fieldRows"
                        :key="row.label"
                        class="labs-audit__field-row"
                    >
                        <dt class="type-meta">{{ row.label }}</dt>
                        <dd class="type-body labs-audit__field-value">
                            {{ row.value }} ·
                            {{
                                copy.metricRatings[row.category] ?? row.category
                            }}
                        </dd>
                    </div>
                </dl>
                <p v-else class="type-body-sm labs-audit__no-field">
                    {{ copy.noFieldData }}
                </p>

                <template v-if="report.opportunities.length > 0">
                    <h2 class="type-h3 labs-audit__heading">
                        {{ copy.opportunitiesHeading }}
                    </h2>
                    <ul class="labs-audit__opportunities">
                        <li
                            v-for="item in report.opportunities"
                            :key="item.title"
                            class="type-body-sm labs-audit__opportunity"
                        >
                            <strong>{{ item.title }}</strong>
                            <template v-if="item.savings">
                                — {{ item.savings }}
                            </template>
                        </li>
                    </ul>
                </template>
            </Panel>
        </section>
    </SiteLayout>
</template>

<style scoped>
.labs-audit {
    display: grid;
    gap: var(--sw-space-sm);
}

.labs-audit__form-panel,
.labs-audit__report {
    display: grid;
    gap: var(--sw-space-xs);
    padding: var(--sw-space-sm);
}

.labs-audit__form {
    display: grid;
    gap: var(--sw-space-xs);
    max-width: 32rem;
}

.labs-audit__field {
    display: grid;
    gap: var(--sw-space-3xs);
}

.labs-audit__input {
    width: 100%;
    border: 1px solid color-mix(in srgb, var(--sw-border) 88%, transparent);
    border-radius: var(--sw-radius-sm);
    background: color-mix(in srgb, var(--sw-bg-elevated) 88%, transparent);
    color: var(--sw-text-primary);
    font: inherit;
    padding: var(--sw-space-2xs) var(--sw-space-xs);
    transition:
        border-color var(--sw-motion-fast),
        background-color var(--sw-motion-fast);
}

.labs-audit__input::placeholder {
    color: var(--sw-text-muted);
}

.labs-audit__input:focus-visible {
    outline: 2px solid var(--sw-border-focus);
    outline-offset: 2px;
}

/* Same visually-hidden honeypot recipe as the newsletter block. */
.labs-audit__trap {
    position: absolute;
    width: 1px;
    height: 1px;
    overflow: hidden;
    clip-path: inset(50%);
    white-space: nowrap;
}

.labs-audit__note,
.labs-audit__error,
.labs-audit__privacy,
.labs-audit__sent-note,
.labs-audit__heading,
.labs-audit__no-field,
.labs-audit__opportunity {
    margin: 0;
}

.labs-audit__error {
    color: var(--sw-accent-dominant);
}

.labs-audit__privacy {
    color: var(--sw-text-muted);
}

.labs-audit__scores {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-sm);
}

.labs-audit__score {
    display: grid;
    gap: var(--sw-space-4xs);
    justify-items: start;
}

.labs-audit__score-value {
    line-height: 1;
}

.labs-audit__field-rows {
    display: grid;
    gap: var(--sw-space-2xs);
    margin: 0;
}

.labs-audit__field-row {
    display: flex;
    align-items: baseline;
    gap: var(--sw-space-xs);
    border-top: 1px solid var(--sw-border);
    padding-top: var(--sw-space-2xs);
}

.labs-audit__field-row:first-child {
    border-top: 0;
    padding-top: 0;
}

.labs-audit__field-value {
    margin: 0;
    color: var(--sw-text-secondary);
}

.labs-audit__opportunities {
    display: grid;
    gap: var(--sw-space-3xs);
    margin: 0;
    padding: 0;
    list-style: none;
}

.labs-audit__opportunity {
    color: var(--sw-text-secondary);
}
</style>
