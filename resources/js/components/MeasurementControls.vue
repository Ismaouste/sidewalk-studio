<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { copy as copyTree } from '@/copy';
import { REPLAY_STORAGE_KEY, setSessionReplay } from '@/lib/analytics';
import { isAudienceOptedOut, setAudienceOptOut } from '@/lib/audience';
import { readStorage, removeStorage, writeStorage } from '@/lib/safeStorage';
import type { SiteProps } from '@/types';

/**
 * The two measurement switches that live outside the consent banner.
 *
 * The audience ping (T1) needs no consent, so what it gets is an opt-out.
 * Session replay (T3) is the opposite: it rides on accepted analytics but
 * is never enabled by the banner — only by its own switch here. Consent
 * state arrives through the `cc:*` window events vanilla-cookieconsent
 * already dispatches, so there is nothing to poll.
 */

const page = usePage<{ site: SiteProps }>();

const copy = computed(
    () => copyTree[page.props.site.locale].pages.dataProcessing,
);

const audienceOptedOut = ref(false);
const replayEnabled = ref(false);
const analyticsAccepted = ref(false);

function refreshConsent(): void {
    analyticsAccepted.value =
        window.SidewalkConsent?.acceptedCategory('analytics') ?? false;
}

onMounted(() => {
    audienceOptedOut.value = isAudienceOptedOut();
    replayEnabled.value = readStorage('local', REPLAY_STORAGE_KEY) === '1';
    refreshConsent();
    window.addEventListener('cc:onConsent', refreshConsent);
    window.addEventListener('cc:onChange', refreshConsent);
});

onBeforeUnmount(() => {
    window.removeEventListener('cc:onConsent', refreshConsent);
    window.removeEventListener('cc:onChange', refreshConsent);
});

function toggleAudience(event: Event): void {
    const optedOut = !(event.target as HTMLInputElement).checked;
    audienceOptedOut.value = optedOut;
    setAudienceOptOut(optedOut);
}

function toggleReplay(event: Event): void {
    const enabled = (event.target as HTMLInputElement).checked;
    replayEnabled.value = enabled;

    if (enabled) {
        writeStorage('local', REPLAY_STORAGE_KEY, '1');
    } else {
        removeStorage('local', REPLAY_STORAGE_KEY);
    }

    setSessionReplay(enabled);
}

function openPreferences(): void {
    window.SidewalkConsent?.showPreferences();
}
</script>

<template>
    <div class="measurement-controls">
        <label class="measurement-controls__row">
            <input
                type="checkbox"
                class="measurement-controls__switch"
                :checked="!audienceOptedOut"
                @change="toggleAudience"
            />
            <span class="measurement-controls__text">
                <span class="measurement-controls__label type-body">{{
                    copy.audienceOptOutLabel
                }}</span>
                <span class="measurement-controls__hint type-body-sm">{{
                    copy.audienceOptOutHint
                }}</span>
            </span>
        </label>

        <label class="measurement-controls__row">
            <input
                type="checkbox"
                class="measurement-controls__switch"
                :checked="replayEnabled"
                :disabled="!analyticsAccepted"
                @change="toggleReplay"
            />
            <span class="measurement-controls__text">
                <span class="measurement-controls__label type-body">{{
                    copy.replayLabel
                }}</span>
                <span class="measurement-controls__hint type-body-sm">
                    {{
                        analyticsAccepted
                            ? copy.replayHint
                            : copy.replayHintConsentNeeded
                    }}
                    <button
                        v-if="!analyticsAccepted"
                        type="button"
                        class="measurement-controls__preferences"
                        @click="openPreferences"
                    >
                        {{ copy.openPreferences }}
                    </button>
                </span>
            </span>
        </label>
    </div>
</template>

<style scoped>
.measurement-controls {
    display: grid;
    gap: var(--sw-space-2xs);
    margin-top: var(--sw-space-2xs);
    border-top: 1px solid color-mix(in srgb, var(--sw-border) 88%, transparent);
    padding-top: var(--sw-space-2xs);
}

.measurement-controls__row {
    display: flex;
    gap: var(--sw-space-2xs);
    align-items: flex-start;
    cursor: pointer;
}

.measurement-controls__row:has(.measurement-controls__switch:disabled) {
    cursor: not-allowed;
}

.measurement-controls__switch {
    margin-top: 0.3rem;
    inline-size: 1.05rem;
    block-size: 1.05rem;
    accent-color: var(--sw-accent-dominant);
}

.measurement-controls__switch:disabled {
    opacity: 0.45;
}

.measurement-controls__text {
    display: grid;
    gap: 2px;
}

.measurement-controls__row:has(.measurement-controls__switch:disabled)
    .measurement-controls__label {
    color: var(--sw-text-muted);
}

.measurement-controls__hint {
    color: var(--sw-text-muted);
}

.measurement-controls__preferences {
    border: 0;
    background: none;
    padding: 0;
    font: inherit;
    color: var(--sw-accent-dominant);
    text-decoration: underline;
    text-underline-offset: 0.18em;
    cursor: pointer;
}
</style>
