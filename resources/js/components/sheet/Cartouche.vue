<script setup lang="ts">
/**
 * The title block of the sheet (specs/018-site-refresh): what an execution
 * drawing carries in its corner. Project, sheet, scale, revision, author,
 * place, and the few ways to find the author. Every value comes from the page
 * props (site identity, settings, SEO title) or from the UI copy: nothing about
 * the owner is written in this file.
 */
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { copy as copyTree } from '@/copy';
import type { SeoPayload, SiteProps } from '@/types';

defineOptions({ name: 'SheetCartouche' });

const page = usePage<{ seo?: SeoPayload; site: SiteProps }>();
const site = computed(() => page.props.site);
const copy = computed(
    () => copyTree[site.value.locale].layout.footer.cartouche,
);

const host = computed(() => {
    try {
        return new URL(site.value.url).host;
    } catch {
        return site.value.url;
    }
});
const sheet = computed(
    () => page.props.seo?.title?.split(/\s[—·]\s/)[0] ?? site.value.name,
);
const github = computed(() => site.value.social.github_url?.trim() || null);
</script>

<template>
    <dl class="cartouche">
        <div class="cartouche__cell">
            <dt>{{ copy.project }}</dt>
            <dd>{{ host }}</dd>
        </div>
        <div class="cartouche__cell cartouche__cell--wide">
            <dt>{{ copy.sheet }}</dt>
            <dd>{{ sheet }}</dd>
        </div>
        <div class="cartouche__cell">
            <dt>{{ copy.scale }}</dt>
            <dd>1:1</dd>
        </div>
        <div class="cartouche__cell">
            <dt>{{ copy.revision }}</dt>
            <dd>{{ copy.revisionValue }}</dd>
        </div>
        <div class="cartouche__cell">
            <dt>{{ copy.author }}</dt>
            <dd>{{ site.author.name }}</dd>
        </div>
        <div class="cartouche__cell">
            <dt>{{ copy.place }}</dt>
            <dd>{{ site.contact.location }}</dd>
        </div>
        <div class="cartouche__cell cartouche__cell--wide">
            <dt>{{ copy.find }}</dt>
            <dd class="cartouche__links">
                <a :href="`mailto:${site.contact.email}`">{{
                    site.contact.email
                }}</a>
                <a v-if="github" :href="github" target="_blank" rel="noopener"
                    >GitHub</a
                >
            </dd>
        </div>
    </dl>
</template>

<style scoped>
.cartouche {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    margin: 0 0 var(--sw-space-sm);
    border-top: var(--sw-hairline) solid var(--sw-text-primary);
    border-left: var(--sw-hairline) solid var(--sw-text-primary);
    background: var(--sw-bg-base);
    font-family: var(--sw-font-code);
    font-size: 11px;
    letter-spacing: 0.04em;
}

.cartouche__cell {
    display: grid;
    gap: 2px;
    min-width: 0;
    padding: 6px 8px 7px;
    border-right: var(--sw-hairline) solid var(--sw-text-primary);
    border-bottom: var(--sw-hairline) solid var(--sw-text-primary);
}

.cartouche__cell--wide {
    grid-column: span 2;
}

.cartouche dt {
    color: var(--sw-text-muted);
    font-size: 9px;
    text-transform: uppercase;
}

.cartouche dd {
    margin: 0;
    overflow-wrap: anywhere;
    color: var(--sw-text-primary);
}

.cartouche__links {
    display: flex;
    flex-wrap: wrap;
    gap: 4px 14px;
}

.cartouche__links a {
    color: var(--sw-path);
    text-decoration: underline;
    text-underline-offset: 2px;
}

@media (min-width: 900px) {
    .cartouche {
        grid-template-columns: 1fr 2fr 0.6fr 1fr 1.4fr 1.4fr 2.2fr;
    }

    .cartouche__cell--wide {
        grid-column: auto;
    }
}
</style>
