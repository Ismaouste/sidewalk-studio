export type ConsentCategoryKey = 'necessary' | 'analytics' | 'media';

export type ScriptRegistration = {
    key: string;
    category: ConsentCategoryKey;
    load: () => void | Promise<void>;
    unload?: () => void;
    loaded: boolean;
};

const scripts = new Map<string, ScriptRegistration>();
const embeds = new Map<
    string,
    { category: ConsentCategoryKey; config: Record<string, unknown> }
>();

export function registerScript(
    registration: Omit<ScriptRegistration, 'loaded'>,
): void {
    scripts.set(registration.key, {
        ...registration,
        loaded: false,
    });
}

export function syncScripts(
    category: ConsentCategoryKey,
    accepted: boolean,
): void {
    scripts.forEach((registration) => {
        if (registration.category !== category) {
            return;
        }

        if (accepted && !registration.loaded) {
            registration.load();
            registration.loaded = true;
            return;
        }

        if (!accepted && registration.loaded && registration.unload) {
            registration.unload();
            registration.loaded = false;
        }
    });
}

export function registerEmbed(
    key: string,
    category: ConsentCategoryKey,
    config: Record<string, unknown>,
): void {
    embeds.set(key, { category, config });
}

export function getEmbedsByCategory(
    category: ConsentCategoryKey,
): Record<string, Record<string, unknown>> {
    return Array.from(embeds.entries()).reduce<
        Record<string, Record<string, unknown>>
    >((accumulator, [key, value]) => {
        if (value.category === category) {
            accumulator[key] = value.config;
        }

        return accumulator;
    }, {});
}
