<script setup lang="ts">
const props = defineProps<{
    html: string;
}>();
</script>

<template>
    <div class="prose-copy" v-html="props.html" />
</template>
