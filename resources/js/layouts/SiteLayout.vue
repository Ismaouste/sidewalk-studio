<script setup lang="ts">
import AmbientGrid from '@/components/design-system/AmbientGrid.vue';
import AppFooter from '@/components/layout/AppFooter.vue';
import AppHeader from '@/components/layout/AppHeader.vue';
</script>

<template>
    <div class="sw-shell">
        <AmbientGrid />
        <AppHeader />
        <main class="sw-main">
            <div class="sw-container">
                <slot />
            </div>
        </main>
        <AppFooter />
    </div>
</template>
