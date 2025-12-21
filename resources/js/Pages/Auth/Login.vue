<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import Message from 'primevue/message';

const { t } = useI18n();

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform((data) => ({
        ...data,
        remember: data.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head :title="t('auth.loginTitle')" />

    <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
        <div class="w-full max-w-md">
            <!-- Logo & Title -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-primary-500 mb-4">
                    <i class="pi pi-shop text-3xl text-white"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Interdiscount</h1>
                <p class="text-gray-500 mt-1">{{ t('auth.loginSubtitle') }}</p>
            </div>

            <!-- Login Card -->
            <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
                <Message v-if="status" severity="success" :closable="false" class="mb-4">
                    {{ status }}
                </Message>

                <Message v-if="form.errors.email" severity="error" :closable="false" class="mb-4">
                    {{ form.errors.email }}
                </Message>

                <form @submit.prevent="submit" class="space-y-5">
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ t('auth.email') }}
                        </label>
                        <InputText
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="w-full"
                            :invalid="!!form.errors.email"
                            required
                            autofocus
                            autocomplete="username"
                            data-testid="email-input"
                        />
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ t('auth.password') }}
                        </label>
                        <Password
                            v-model="form.password"
                            inputId="password"
                            :feedback="false"
                            toggleMask
                            class="w-full"
                            inputClass="w-full"
                            :invalid="!!form.errors.password"
                            required
                            autocomplete="current-password"
                            :pt="{ root: { 'data-testid': 'password-input' } }"
                        />
                        <small v-if="form.errors.password" class="text-red-500 mt-1 block">
                            {{ form.errors.password }}
                        </small>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <Checkbox
                                v-model="form.remember"
                                inputId="remember"
                                :binary="true"
                                data-testid="remember-checkbox"
                            />
                            <label for="remember" class="text-sm text-gray-600 cursor-pointer">
                                {{ t('auth.rememberMe') }}
                            </label>
                        </div>
                    </div>

                    <!-- Submit -->
                    <Button
                        type="submit"
                        :label="t('auth.login')"
                        :loading="form.processing"
                        class="w-full"
                        severity="primary"
                        data-testid="login-button"
                    />
                </form>
            </div>

            <!-- Footer -->
            <p class="text-center text-xs text-gray-400 mt-6">
                &copy; {{ new Date().getFullYear() }} Interdiscount Clone. Admin Panel.
            </p>
        </div>
    </div>
</template>
