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
    interface Window {
        iframemanager?: () => {
            run: (config: Record<string, unknown>) => void;
            acceptService: (service: string | string[]) => void;
            rejectService: (service: string | string[]) => void;
        };
        SidewalkConsent?: {
            showPreferences: () => void;
        };
    }
}

export {};
