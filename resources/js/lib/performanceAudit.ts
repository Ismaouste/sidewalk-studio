type ResourceEntry = SidewalkResourceAuditEntry;

let initialized = false;

function shouldAudit() {
    if (typeof window === 'undefined') {
        return false;
    }

    return import.meta.env.DEV || new URLSearchParams(window.location.search).has('audit');
}

function classifyCacheState(entry: PerformanceResourceTiming): ResourceEntry['cacheState'] {
    if (entry.transferSize === 0 && entry.decodedBodySize > 0) {
        return 'cache';
    }

    if (entry.transferSize > 0) {
        return 'network';
    }

    return 'unknown';
}

function snapshotResources(): ResourceEntry[] {
    return performance
        .getEntriesByType('resource')
        .filter(
            (entry): entry is PerformanceResourceTiming =>
                entry instanceof PerformanceResourceTiming,
        )
        .map((entry) => ({
            name: entry.name,
            initiatorType: entry.initiatorType || 'unknown',
            duration: Number(entry.duration.toFixed(2)),
            transferSize: entry.transferSize,
            decodedBodySize: entry.decodedBodySize,
            cacheState: classifyCacheState(entry),
        }))
        .sort((left, right) => right.duration - left.duration)
        .slice(0, 20);
}

function updateWindowAudit(longTasks: Array<{ name: string; duration: number }>) {
    const resources = snapshotResources();
    const cacheHits = resources.filter((entry) => entry.cacheState === 'cache').length;

    window.__SIDEWALK_PERF_AUDIT__ = {
        resources,
        longTasks,
        summary: {
            totalResources: performance.getEntriesByType('resource').length,
            cacheHits,
            slowestResource: resources[0],
            longestTask: longTasks[0],
        },
    };

    window.dispatchEvent(
        new CustomEvent('sidewalk:performance-audit', {
            detail: window.__SIDEWALK_PERF_AUDIT__,
        }),
    );

    if (import.meta.env.DEV) {
        console.info('[Perf audit]', window.__SIDEWALK_PERF_AUDIT__);
    }
}

export function initializePerformanceAudit() {
    if (initialized || !shouldAudit()) {
        return;
    }

    initialized = true;

    const longTasks: Array<{ name: string; duration: number }> = [];
    let idleTimer = 0;

    const scheduleUpdate = () => {
        window.clearTimeout(idleTimer);
        idleTimer = window.setTimeout(() => updateWindowAudit(longTasks), 250);
    };

    try {
        const resourceObserver = new PerformanceObserver(() => {
            scheduleUpdate();
        });
        resourceObserver.observe({ type: 'resource', buffered: true });
    } catch {
        scheduleUpdate();
    }

    try {
        const longTaskObserver = new PerformanceObserver((list) => {
            longTasks.splice(
                0,
                longTasks.length,
                ...list
                    .getEntries()
                    .map((entry) => ({
                        name: entry.name || 'longtask',
                        duration: Number(entry.duration.toFixed(2)),
                    }))
                    .sort((left, right) => right.duration - left.duration)
                    .slice(0, 10),
            );
            scheduleUpdate();
        });
        longTaskObserver.observe({ type: 'longtask', buffered: true });
    } catch {
        // Long task observation is not available in every browser.
    }

    scheduleUpdate();
}
