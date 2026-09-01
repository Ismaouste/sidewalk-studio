import { onBeforeUnmount, onMounted } from 'vue';
import type { Ref } from 'vue';
import { capture } from '@/lib/analytics';

/**
 * Funnel stage V1: someone actually read the piece.
 *
 * "Read" means the end of the article entered the viewport — an
 * IntersectionObserver on a sentinel, not a timer. Fires at most once per
 * page view, and only ever reaches PostHog when the analytics category
 * has been accepted (capture() is a no-op otherwise).
 */
export function useReaderEngagement(
    sentinel: Ref<HTMLElement | null>,
    section: 'journal' | 'case-studies',
): void {
    let observer: IntersectionObserver | null = null;

    onMounted(() => {
        if (!sentinel.value || typeof IntersectionObserver === 'undefined') {
            return;
        }

        observer = new IntersectionObserver((entries) => {
            if (entries.some((entry) => entry.isIntersecting)) {
                capture('reader_engaged', {
                    funnel_stage: 'V1',
                    section,
                });
                observer?.disconnect();
                observer = null;
            }
        });

        observer.observe(sentinel.value);
    });

    onBeforeUnmount(() => {
        observer?.disconnect();
    });
}
