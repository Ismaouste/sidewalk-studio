<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AdminHead from '@/components/admin/shared/AdminHead.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type {
    FlashProps,
    PublishingState,
    StaticExportSettings,
    ThemeSettings,
} from '@/types';

const props = defineProps<{
    themeSettings: ThemeSettings;
    staticExportSettings: StaticExportSettings;
    publishingState: PublishingState;
}>();

const page = usePage<{ flash: FlashProps }>();
const status = computed(() => page.props.flash?.status ?? null);
const form = useForm({
    theme_settings: { ...props.themeSettings },
    static_export_settings: { ...props.staticExportSettings },
});

function submit() {
    form.put('/admin/theme');
}

function rebuild() {
    form.post('/admin/theme/rebuild');
}

/**
 * The two swatches preview a theme other than the one the admin is wearing,
 * and `tokens.css` scopes each palette to `html[data-theme=…]` — a nested
 * element cannot pick up the theme it is not in, so these values are written
 * out. That makes them a copy, and this copy had gone two palettes stale:
 * it still showed the pre-`52d6fc5` sand and mauve while the real themes are
 * chalk and violet glass, so the preview disagreed with the page around it.
 * If these drift again, read the current values off `--sw-bg-base` and
 * `--sw-bg-grid` in `resources/css/tokens.css`.
 */
function previewStyle(variant: 'morning' | 'sunset') {
    const background =
        variant === 'morning'
            ? `linear-gradient(${form.theme_settings.gradient_angle}deg, #fafaf8, #f3f2ee)`
            : `linear-gradient(${form.theme_settings.gradient_angle}deg, #1a1230, #0d0a17)`;

    return {
        background,
        backdropFilter: `blur(${form.theme_settings.surface_blur}px)`,
        boxShadow:
            variant === 'morning'
                ? `inset 0 0 0 ${form.theme_settings.line_thickness}px rgba(65, 63, 58, 0.12)`
                : `inset 0 0 0 ${form.theme_settings.line_thickness}px rgba(255, 255, 255, 0.14)`,
        color: variant === 'sunset' ? 'white' : 'inherit',
    };
}
</script>

<template>
    <AdminLayout>
        <AdminHead title="Admin Theme and Publishing" />

        <form class="admin-theme" @submit.prevent="submit">
            <header class="admin-theme__header">
                <div>
                    <p class="type-eyebrow">Theme and publishing</p>
                    <h1 class="type-h1 admin-theme__title">
                        Visual defaults, static export, and rebuild workflow.
                    </h1>
                    <p class="type-body admin-theme__copy">
                        Theme previews stay aligned with the current Morning and
                        Sunset system while rebuild state remains explicit.
                    </p>
                </div>
                <div class="admin-theme__actions">
                    <span v-if="status" class="type-meta">{{ status }}</span>
                    <Button type="submit" :disabled="form.processing"
                        >Save theme settings</Button
                    >
                </div>
            </header>

            <div class="admin-theme__grid">
                <Panel class="admin-theme__panel" tone="elevated">
                    <p class="type-eyebrow">Theme controls</p>
                    <label class="admin-theme__field">
                        <span class="type-nav">Default theme</span>
                        <select
                            v-model="form.theme_settings.default_theme"
                            class="admin-theme__input"
                        >
                            <option value="morning">Morning</option>
                            <option value="sunset">Sunset</option>
                        </select>
                    </label>
                    <label class="admin-theme__field">
                        <span class="type-nav">Gradient angle</span>
                        <input
                            v-model="form.theme_settings.gradient_angle"
                            class="admin-theme__input"
                            type="range"
                            min="0"
                            max="360"
                        />
                        <span class="type-meta"
                            >{{ form.theme_settings.gradient_angle }}°</span
                        >
                    </label>
                    <label class="admin-theme__field">
                        <span class="type-nav">Surface blur</span>
                        <input
                            v-model="form.theme_settings.surface_blur"
                            class="admin-theme__input"
                            type="range"
                            min="0"
                            max="40"
                        />
                        <span class="type-meta"
                            >{{ form.theme_settings.surface_blur }}px</span
                        >
                    </label>
                    <label class="admin-theme__field">
                        <span class="type-nav">Line thickness</span>
                        <input
                            v-model="form.theme_settings.line_thickness"
                            class="admin-theme__input"
                            type="range"
                            min="1"
                            max="8"
                        />
                        <span class="type-meta"
                            >{{ form.theme_settings.line_thickness }}px</span
                        >
                    </label>

                    <div class="admin-theme__preview-grid">
                        <div
                            class="admin-theme__preview"
                            :style="previewStyle('morning')"
                        >
                            <span class="type-meta">Morning</span>
                        </div>
                        <div
                            class="admin-theme__preview"
                            :style="previewStyle('sunset')"
                        >
                            <span class="type-meta">Sunset</span>
                        </div>
                    </div>
                </Panel>

                <Panel class="admin-theme__panel" tone="surface">
                    <p class="type-eyebrow">Static export settings</p>
                    <label class="admin-theme__toggle">
                        <input
                            v-model="
                                form.static_export_settings.static_mode_enabled
                            "
                            type="checkbox"
                        />
                        <span>Enable static HTML generation mode</span>
                    </label>
                    <label class="admin-theme__toggle">
                        <input
                            v-model="
                                form.static_export_settings.github_pages_enabled
                            "
                            type="checkbox"
                        />
                        <span>Enable GitHub Pages related behavior</span>
                    </label>
                    <label class="admin-theme__toggle">
                        <input
                            v-model="
                                form.static_export_settings.preprod_mode_enabled
                            "
                            type="checkbox"
                        />
                        <span>Mark the app as preprod/admin-only</span>
                    </label>
                    <label class="admin-theme__field">
                        <span class="type-nav">Export base path</span>
                        <input
                            v-model="
                                form.static_export_settings.export_base_path
                            "
                            class="admin-theme__input"
                            type="text"
                        />
                    </label>
                    <label class="admin-theme__field">
                        <span class="type-nav">Export output path</span>
                        <input
                            v-model="
                                form.static_export_settings.export_output_path
                            "
                            class="admin-theme__input"
                            type="text"
                        />
                    </label>
                </Panel>

                <Panel
                    class="admin-theme__panel admin-theme__panel--full"
                    tone="grid"
                >
                    <div class="admin-theme__publish-head">
                        <div>
                            <p class="type-eyebrow">Rebuild workflow</p>
                            <h2 class="type-h3">Current state</h2>
                            <p class="type-body-sm admin-theme__copy">
                                {{
                                    publishingState.last_change_summary ??
                                    'No pending content or settings changes.'
                                }}
                            </p>
                        </div>
                        <Button
                            type="button"
                            :disabled="
                                !publishingState.rebuild_required ||
                                form.processing
                            "
                            @click="rebuild"
                        >
                            Rebuild public output
                        </Button>
                    </div>
                    <p class="type-meta">
                        Rebuild required:
                        {{ publishingState.rebuild_required ? 'yes' : 'no'
                        }}<br />
                        Last rebuilt at:
                        {{ publishingState.last_rebuilt_at ?? 'never' }}
                    </p>
                </Panel>
            </div>
        </form>
    </AdminLayout>
</template>

<style scoped>
.admin-theme,
.admin-theme__grid,
.admin-theme__panel {
    display: grid;
    gap: 1rem;
}

.admin-theme__header,
.admin-theme__actions,
.admin-theme__publish-head {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
}

.admin-theme__grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.admin-theme__panel {
    padding: 1rem;
}

.admin-theme__panel--full {
    grid-column: 1 / -1;
}

.admin-theme__field {
    display: grid;
    gap: 0.45rem;
}

.admin-theme__input {
    min-height: 3rem;
    border: 1px solid var(--sw-border);
    border-radius: var(--sw-radius-md);
    background: color-mix(in srgb, var(--sw-bg-base) 92%, transparent);
    padding-inline: 0.95rem;
}

.admin-theme__toggle {
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
}

.admin-theme__preview-grid {
    display: grid;
    gap: 1rem;
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.admin-theme__preview {
    min-height: 120px;
    border-radius: var(--sw-radius-lg);
    border: 1px solid var(--sw-border);
    padding: 1rem;
}

@media (max-width: 960px) {
    .admin-theme__grid {
        grid-template-columns: minmax(0, 1fr);
    }
}

@media (max-width: 720px) {
    .admin-theme__header,
    .admin-theme__publish-head {
        flex-direction: column;
    }
}
</style>
