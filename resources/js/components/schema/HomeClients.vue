<script setup lang="ts">
/**
 * The client houses of the Jewely portfolio, on the home page: three numbers
 * that count up once when they scroll into view, then one card per house. It
 * is shown at every width, unlike the career branch of the plan (very wide
 * screens only). A client's site is linked `nofollow`: it is mentioned, not
 * endorsed.
 */
import { usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { copy as copyTree } from '@/copy';
import type { SiteProps } from '@/types';

defineOptions({ name: 'HomeClients' });

const page = usePage<{ site: SiteProps }>();
const locale = computed(() => page.props.site.locale);
const clients = computed(() => copyTree[locale.value].pages.home.clients);

const root = ref<HTMLElement | null>(null);
const progress = ref(1);
const format = (n: number) =>
    new Intl.NumberFormat(locale.value).format(Math.round(n));

let frame = 0;
let observer: IntersectionObserver | undefined;

onMounted(() => {
    const el = root.value;

    if (
        !el ||
        !('IntersectionObserver' in window) ||
        window.matchMedia('(prefers-reduced-motion: reduce)').matches ||
        document.documentElement.getAttribute('data-motion') === 'reduced'
    ) {
        return;
    }

    observer = new IntersectionObserver(
        ([entry]) => {
            if (!entry?.isIntersecting) {
                return;
            }

            observer?.disconnect();

            const start = performance.now();
            const duration = 1100;
            const step = (now: number) => {
                const t = Math.min(1, (now - start) / duration);

                // Ease out: fast at first, then settling on the real number.
                progress.value = 1 - (1 - t) ** 3;

                if (t < 1) {
                    frame = requestAnimationFrame(step);
                }
            };

            progress.value = 0;
            frame = requestAnimationFrame(step);
        },
        { threshold: 0.4 },
    );
    observer.observe(el);
});

onBeforeUnmount(() => {
    observer?.disconnect();
    cancelAnimationFrame(frame);
});
</script>

<template>
    <section
        ref="root"
        class="sw-section home-clients"
        aria-labelledby="home-clients-title"
    >
        <h2 id="home-clients-title" class="type-h2">{{ clients.title }}</h2>
        <p class="type-body home-clients__intro">{{ clients.intro }}</p>

        <ul class="home-clients__figures">
            <li v-for="figure in clients.figures" :key="figure.label">
                <strong class="home-clients__number"
                    >{{ format(figure.value * progress)
                    }}{{ figure.suffix }}</strong
                >
                <span class="type-meta">{{ figure.label }}</span>
            </li>
        </ul>

        <ul class="home-clients__houses">
            <li
                v-for="house in clients.houses"
                :key="house.name"
                class="home-clients__house"
            >
                <h3 class="home-clients__name">
                    {{ house.name }}
                    <span v-if="house.period" class="type-meta">{{
                        house.period
                    }}</span>
                </h3>
                <p class="type-body-sm home-clients__role">{{ house.role }}</p>
                <a
                    v-if="house.href"
                    class="home-clients__link"
                    :href="house.href"
                    target="_blank"
                    rel="nofollow noopener noreferrer"
                    >{{ house.linkLabel }} <span aria-hidden="true">↗</span></a
                >
            </li>
        </ul>
    </section>
</template>

<style scoped>
.home-clients {
    display: grid;
    gap: var(--sw-space-xs);
    padding-block: var(--sw-space-md);
    border-top: var(--sw-hairline) solid var(--sw-border);
}

.home-clients__intro {
    margin: 0;
    max-width: 60ch;
    color: var(--sw-text-secondary);
}

.home-clients__figures {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-xs) var(--sw-space-md);
    margin: 0;
    padding: 0;
    list-style: none;
}

.home-clients__figures li {
    display: grid;
    gap: 0.15rem;
}

.home-clients__number {
    font-family: var(--sw-font-code);
    font-size: clamp(2rem, 5vw, 3.25rem);
    font-weight: 500;
    line-height: 1;
    letter-spacing: -0.03em;
    font-variant-numeric: tabular-nums;
}

.home-clients__houses {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(16rem, 1fr));
    gap: var(--sw-space-xs);
    margin: 0;
    padding: 0;
    list-style: none;
}

.home-clients__house {
    display: grid;
    align-content: start;
    gap: 0.4rem;
    padding: var(--sw-space-xs);
    border: var(--sw-hairline) solid var(--sw-text-primary);
    background: var(--sw-bg-surface);
}

.home-clients__name {
    display: flex;
    flex-wrap: wrap;
    align-items: baseline;
    justify-content: space-between;
    gap: 0 0.75rem;
    margin: 0;
    font-size: 1.125rem;
    font-weight: 600;
}

.home-clients__role {
    margin: 0;
    color: var(--sw-text-secondary);
}

.home-clients__link {
    color: var(--sw-path);
    text-underline-offset: 2px;
}
</style>
