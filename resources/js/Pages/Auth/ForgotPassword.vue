<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

defineProps({
    status: { type: String, default: null },
})

const form = useForm({ email: '' })

const submit = () => {
    form.post(route('password.email'))
}
</script>

<template>
    <Head title="Forgot password" />
    <GuestLayout>
        <h1 class="text-2xl font-bold text-gray-900">Forgot your password?</h1>
        <p class="mt-1 text-sm text-gray-500">Enter your email and we'll send you a 6-digit code to reset it</p>

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

            <button
                type="submit"
                class="w-full rounded-full bg-indigo-600 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:opacity-50"
                :disabled="form.processing"
            >
                Send reset code
            </button>
        </form>

        <Link :href="route('login')" class="mt-6 flex items-center justify-center text-sm text-gray-500 hover:text-gray-700">
            Back to sign in
        </Link>
    </GuestLayout>
</template>