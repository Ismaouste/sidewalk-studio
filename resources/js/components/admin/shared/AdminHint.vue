<script setup lang="ts">
import { computed, ref } from 'vue';

const props = defineProps<{
    label: string;
    text: string;
}>();

const pinned = ref(false);
const open = ref(false);

const isVisible = computed(() => open.value || pinned.value);

function togglePinned() {
    pinned.value = !pinned.value;
    open.value = pinned.value;
}
</script>

<template>
    <span
        class="admin-hint"
        @mouseenter="open = true"
        @mouseleave="open = false"
    >
        <button type="button" class="admin-hint__trigger" @click="togglePinned">
            {{ label }}
        </button>
        <span v-if="isVisible" class="admin-hint__tooltip">
            {{ text }}
        </span>
    </span>
</template>

<style scoped>
.admin-hint {
    position: relative;
    display: inline-flex;
}

.admin-hint__trigger {
    inline-size: 1.55rem;
    block-size: 1.55rem;
    border-radius: 999px;
    border: 1px solid var(--sw-border);
    font-size: 0.76rem;
    color: var(--sw-text-secondary);
}

.admin-hint__tooltip {
    position: absolute;
    inset: calc(100% + 0.45rem) auto auto 50%;
    z-index: 20;
    width: min(18rem, 70vw);
    transform: translateX(-50%);
    border: 1px solid var(--sw-border);
    border-radius: var(--sw-radius-md);
    background: color-mix(in srgb, var(--sw-bg-base) 97%, transparent);
    padding: 0.7rem 0.8rem;
    box-shadow: var(--sw-shadow-md);
    color: var(--sw-text-secondary);
}
</style>
