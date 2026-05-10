<script setup lang="ts">
import { nextTick, onMounted, ref, watch } from 'vue';

const props = defineProps<{
    html: string;
}>();

const root = ref<HTMLDivElement | null>(null);

function slugify(text: string): string {
    const slug = text
        .normalize('NFD')
        .replace(/[̀-ͯ]/g, '')
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');

    return slug || 'section';
}

function decorateHeadings(): void {
    const container = root.value;
    if (!container) return;

    const headings = container.querySelectorAll<HTMLElement>('h2, h3');
    const used = new Set<string>();

    headings.forEach((heading) => {
        if (heading.querySelector('.prose-anchor')) return;

        const text = heading.textContent?.trim() ?? '';
        const base = slugify(text);
        let candidate = base;
        let counter = 2;
        while (used.has(candidate)) {
            candidate = `${base}-${counter++}`;
        }
        used.add(candidate);

        if (!heading.id) {
            heading.id = candidate;
        }

        const link = document.createElement('a');
        link.className = 'prose-anchor';
        link.href = `#${heading.id}`;
        link.setAttribute('aria-label', 'Copier le lien vers cette section');
        link.textContent = '#';
        link.addEventListener('click', (event) => {
            event.preventDefault();
            const url = `${window.location.origin}${window.location.pathname}#${heading.id}`;
            navigator.clipboard?.writeText(url).catch(() => {});
            history.replaceState(null, '', `#${heading.id}`);
            heading.scrollIntoView({
                behavior: 'smooth',
                block: 'start',
            });
        });

        heading.appendChild(link);
    });

    if (window.location.hash) {
        const target = container.querySelector<HTMLElement>(
            window.location.hash,
        );
        if (target) {
            target.scrollIntoView({ block: 'start' });
        }
    }
}

onMounted(() => {
    decorateHeadings();
});

watch(
    () => props.html,
    async () => {
        await nextTick();
        decorateHeadings();
    },
);
</script>

<template>
    <div ref="root" class="prose-copy" v-html="props.html" />
</template>
