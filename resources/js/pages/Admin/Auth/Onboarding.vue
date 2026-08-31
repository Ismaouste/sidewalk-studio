<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Button from '@/components/ui/Button.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post('/admin/onboarding', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}
</script>

<template>
    <Head title="Admin Onboarding" />

    <div class="admin-login">
        <div class="admin-login__panel">
            <div class="admin-login__intro">
                <p class="type-eyebrow">First-run onboarding</p>
                <h1 class="type-h1 admin-login__title">
                    Create the first operator
                </h1>
                <p class="type-body admin-login__copy">
                    This flow is the production-safe bootstrap. It creates the
                    first operator account and initializes site settings from
                    committed defaults.
                </p>
            </div>

            <form class="admin-login__form" @submit.prevent="submit">
                <label class="admin-login__field">
                    <span class="type-nav">Name</span>
                    <input
                        v-model="form.name"
                        class="admin-login__input"
                        type="text"
                        required
                    />
                    <span
                        v-if="form.errors.name"
                        class="type-meta admin-login__error"
                        >{{ form.errors.name }}</span
                    >
                </label>

                <label class="admin-login__field">
                    <span class="type-nav">Email</span>
                    <input
                        v-model="form.email"
                        class="admin-login__input"
                        type="email"
                        required
                    />
                    <span
                        v-if="form.errors.email"
                        class="type-meta admin-login__error"
                        >{{ form.errors.email }}</span
                    >
                </label>

                <label class="admin-login__field">
                    <span class="type-nav">Password</span>
                    <input
                        v-model="form.password"
                        class="admin-login__input"
                        type="password"
                        required
                    />
                    <span
                        v-if="form.errors.password"
                        class="type-meta admin-login__error"
                        >{{ form.errors.password }}</span
                    >
                </label>

                <label class="admin-login__field">
                    <span class="type-nav">Confirm password</span>
                    <input
                        v-model="form.password_confirmation"
                        class="admin-login__input"
                        type="password"
                        required
                    />
                </label>

                <div class="admin-login__actions">
                    <Button type="submit" :disabled="form.processing">
                        Create operator and continue
                    </Button>
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
            color-mix(in srgb, var(--sw-accent-green) 16%, transparent),
            transparent 28%
        ),
        linear-gradient(
            180deg,
            color-mix(in srgb, var(--sw-bg-grid) 82%, white 18%),
            var(--sw-bg-base)
        );
}

.admin-login__panel {
    width: min(100%, 560px);
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
}

.admin-login__actions {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-xs);
}

.admin-login__error {
    color: var(--sw-accent-coral);
}
</style>
