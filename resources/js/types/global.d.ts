declare module 'vite/client' {
    interface ImportMetaEnv {
        readonly VITE_APP_NAME: string;
        [key: string]: string | boolean | undefined;
    }

    interface ImportMeta {
        readonly env: ImportMetaEnv;
        readonly glob: <T>(pattern: string) => Record<string, () => Promise<T>>;
    }
}

declare global {
    interface IdleDeadline {
        didTimeout: boolean;
        timeRemaining: () => number;
    }

    type SidewalkWebVital = {
        id: string;
        name: 'CLS' | 'FCP' | 'INP' | 'LCP' | 'TTFB';
        value: number;
        delta: number;
        rating: 'good' | 'needs-improvement' | 'poor';
        navigationType: string;
    };

    type SidewalkResourceAuditEntry = {
        name: string;
        initiatorType: string;
        duration: number;
        transferSize: number;
        decodedBodySize: number;
        cacheState: 'cache' | 'network' | 'unknown';
    };

    type SidewalkPerformanceAudit = {
        resources: SidewalkResourceAuditEntry[];
        longTasks: Array<{ name: string; duration: number }>;
        summary: {
            totalResources: number;
            cacheHits: number;
            slowestResource?: SidewalkResourceAuditEntry;
            longestTask?: { name: string; duration: number };
        };
    };

    interface Window {
        requestIdleCallback?: (
            callback: (deadline: IdleDeadline) => void,
            options?: { timeout: number },
        ) => number;
        cancelIdleCallback?: (handle: number) => void;
        iframemanager?: () => {
            run: (config: Record<string, unknown>) => void;
            acceptService: (service: string | string[]) => void;
            rejectService: (service: string | string[]) => void;
        };
        SidewalkConsent?: {
            showPreferences: () => void;
        };
        __SIDEWALK_WEB_VITALS__?: SidewalkWebVital[];
        __SIDEWALK_PERF_AUDIT__?: SidewalkPerformanceAudit;
    }
}

export {};
