<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Button from '@/components/ui/Button.vue';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post('/admin/login', {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <Head title="Admin Login" />

    <div class="admin-login">
        <div class="admin-login__panel">
            <div class="admin-login__intro">
                <p class="type-eyebrow">Protected admin</p>
                <h1 class="type-h1 admin-login__title">Operator login</h1>
                <p class="type-body admin-login__copy">
                    This shell is limited to internal settings work. Create the
                    first operator locally with
                    <code>php artisan admin:create-user &lt;email&gt;</code>.
                </p>
            </div>

            <form class="admin-login__form" @submit.prevent="submit">
                <label class="admin-login__field">
                    <span class="type-nav">Email</span>
                    <input
                        v-model="form.email"
                        class="admin-login__input"
                        type="email"
                        name="email"
                        autocomplete="email"
                        required
                    />
                    <span
                        v-if="form.errors.email"
                        class="type-meta admin-login__error"
                    >
                        {{ form.errors.email }}
                    </span>
                </label>

                <label class="admin-login__field">
                    <span class="type-nav">Password</span>
                    <input
                        v-model="form.password"
                        class="admin-login__input"
                        type="password"
                        name="password"
                        autocomplete="current-password"
                        required
                    />
                    <span
                        v-if="form.errors.password"
                        class="type-meta admin-login__error"
                    >
                        {{ form.errors.password }}
                    </span>
                </label>

                <label class="admin-login__remember">
                    <input v-model="form.remember" type="checkbox" />
                    <span class="type-body-sm">Keep this session active</span>
                </label>

                <div class="admin-login__actions">
                    <Button type="submit" :disabled="form.processing">
                        Sign in
                    </Button>
                    <Button href="/" variant="secondary"> Back to site </Button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
.admin-login {
    min-height: 100vh;
    display: grid;
    place-items: center;
    padding: 24px;
    background:
        radial-gradient(
            circle at top,
            color-mix(in srgb, var(--sw-accent-sun) 16%, transparent),
            transparent 28%
        ),
        linear-gradient(
            180deg,
            color-mix(in srgb, var(--sw-bg-grid) 82%, white 18%),
            var(--sw-bg-base)
        );
}

.admin-login__panel {
    width: min(100%, 520px);
    display: grid;
    gap: var(--sw-space-md);
    border: 1px solid var(--sw-border);
    border-radius: var(--sw-radius-xl);
    background: color-mix(in srgb, var(--sw-bg-surface) 94%, transparent);
    padding: clamp(var(--sw-space-md), 5vw, var(--sw-space-lg));
    box-shadow: var(--sw-shadow-md);
}

.admin-login__intro,
.admin-login__form {
    display: grid;
    gap: var(--sw-space-sm);
}

.admin-login__title {
    margin: 0;
}

.admin-login__copy {
    color: var(--sw-text-secondary);
}

.admin-login__field {
    display: grid;
    gap: 0.45rem;
}

.admin-login__input {
    min-height: 3rem;
    border: 1px solid var(--sw-border);
    border-radius: var(--sw-radius-md);
    background: color-mix(in srgb, var(--sw-bg-base) 92%, transparent);
    padding-inline: 0.95rem;
    transition:
        border-color var(--sw-motion-fast),
        box-shadow var(--sw-motion-fast),
        background-color var(--sw-motion-fast);
}

.admin-login__input:focus-visible {
    border-color: var(--sw-accent-dominant);
    outline: none;
    box-shadow: 0 0 0 4px
        color-mix(in srgb, var(--sw-accent-dominant) 14%, transparent);
}

.admin-login__remember {
    display: inline-flex;
    align-items: center;
    gap: 0.55rem;
    color: var(--sw-text-secondary);
}

.admin-login__actions {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-xs);
}

.admin-login__error {
    color: var(--sw-accent-coral);
}

@media (prefers-reduced-motion: reduce) {
    .admin-login__input {
        transition: none;
    }
}
</style>
