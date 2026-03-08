import {
    onCLS,
    onFCP,
    onINP,
    onLCP,
    onTTFB,
    type Metric,
} from 'web-vitals';

type WebVitalEntry = {
    id: string;
    name: Metric['name'];
    value: number;
    delta: number;
    rating: Metric['rating'];
    navigationType: Metric['navigationType'];
};

let initialized = false;

function report(metric: Metric) {
    const entry: WebVitalEntry = {
        id: metric.id,
        name: metric.name,
        value: Number(metric.value.toFixed(2)),
        delta: Number(metric.delta.toFixed(2)),
        rating: metric.rating,
        navigationType: metric.navigationType,
    };

    window.__SIDEWALK_WEB_VITALS__ = [
        ...(window.__SIDEWALK_WEB_VITALS__ ?? []).filter(
            (item) => item.name !== entry.name,
        ),
        entry,
    ];

    window.dispatchEvent(
        new CustomEvent('sidewalk:web-vital', {
            detail: entry,
        }),
    );

    if (import.meta.env.DEV) {
        console.info(
            `[CWV] ${entry.name}`,
            `${entry.value} (${entry.rating})`,
            entry.navigationType,
        );
    }
}

export function initializeWebVitals() {
    if (initialized || typeof window === 'undefined') {
        return;
    }

    initialized = true;

    onCLS(report);
    onFCP(report);
    onINP(report);
    onLCP(report);
    onTTFB(report);
}
