<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

defineProps({
    canResetPassword: Boolean,
    status: String,
})

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <Head title="Log in" />
    <GuestLayout>
        <h1 class="text-2xl font-bold text-gray-900">Sign in to TaskFlow</h1>
        <p class="mt-1 text-sm text-gray-500">Enter your credentials to access your workspace</p>

        <div v-if="status" class="mt-4 text-sm font-medium text-green-600">{{ status }}</div>

        <form class="mt-8 space-y-5" @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email address" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1.5 block w-full rounded-xl"
                    placeholder="you@company.com"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <div class="flex items-center justify-between">
                    <InputLabel for="password" value="Password" />
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
                    >
                        Forgot password?
                    </Link>
                </div>
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1.5 block w-full rounded-xl"
                    placeholder="••••••••"
                    required
                    autocomplete="current-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <button
                type="submit"
                class="w-full rounded-full bg-indigo-600 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:opacity-50"
                :disabled="form.processing"
            >
                Sign in
            </button>
        </form>

        <div class="mt-6 flex items-center gap-3 text-xs text-gray-400">
            <div class="h-px flex-1 bg-gray-200" />
            or continue with
            <div class="h-px flex-1 bg-gray-200" />
        </div>

        <a :href="route('social.redirect', 'google')"
            type="button"
            class="mt-4 flex w-full items-center justify-center gap-2 rounded-full border border-gray-200 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
        >
            <svg class="h-4 w-4" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.99.66-2.25 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84A11 11 0 0 0 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09A6.6 6.6 0 0 1 5.5 12c0-.73.12-1.43.34-2.09V7.07H2.18A11 11 0 0 0 1 12c0 1.77.43 3.45 1.18 4.93z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1a11 11 0 0 0-9.82 6.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Continue with Google
        </a>

        <p class="mt-6 text-center text-sm text-gray-500">
            Don't have an account?
            <Link :href="route('register')" class="font-semibold text-indigo-600 hover:text-indigo-500">
                Sign up free
            </Link>
        </p>
    </GuestLayout>
</template>
