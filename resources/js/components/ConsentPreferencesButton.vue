<script setup lang="ts">
function openPreferences() {
    window.SidewalkConsent?.showPreferences();
}
</script>

<template>
    <button
        type="button"
        class="inline-flex items-center gap-2 rounded-full border border-[var(--border-strong)] px-4 py-2 text-sm font-medium text-[var(--ink)] transition hover:border-[var(--accent-green)] hover:bg-white/70"
        @click="openPreferences"
    >
        Privacy controls
    </button>
</template>
