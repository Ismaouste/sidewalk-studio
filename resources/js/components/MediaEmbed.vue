<script setup lang="ts">
const props = defineProps<{
    title: string;
    service: string;
    id: string;
}>();
</script>

<template>
    <div
        class="rounded-[2rem] border border-[var(--border)] bg-[var(--surface-strong)] p-3 shadow-[var(--shadow-soft)]"
    >
        <div
            class="min-h-[18rem] overflow-hidden rounded-[1.5rem] bg-[var(--surface)]"
            :data-service="props.service"
            :data-id="props.id"
            :data-title="props.title"
        />
    </div>
</template>
