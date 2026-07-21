<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({
    name: '',
    email: '',
    password: '',
})

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <Head title="Register" />
    <GuestLayout>
        <h1 class="text-2xl font-bold text-gray-900">Create your account</h1>
        <p class="mt-1 text-sm text-gray-500">Start managing tasks like a pro — it's free forever</p>

        <form class="mt-8 space-y-5" @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Full name" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1.5 block w-full rounded-xl"
                    placeholder="Alex Morgan"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Work email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1.5 block w-full rounded-xl"
                    placeholder="you@company.com"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1.5 block w-full rounded-xl"
                    placeholder="Min. 8 characters"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <button
                type="submit"
                class="w-full rounded-full bg-indigo-600 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:opacity-50"
                :disabled="form.processing"
            >
                Create account
            </button>
        </form>

        <p class="mt-4 text-center text-xs text-gray-400">
            By creating an account, you agree to our
            <a href="#" class="font-medium text-gray-600 hover:text-indigo-600">Terms</a>
            and
            <a href="#" class="font-medium text-gray-600 hover:text-indigo-600">Privacy Policy</a>
        </p>

        <p class="mt-4 text-center text-sm text-gray-500">
            Already have an account?
            <Link :href="route('login')" class="font-semibold text-indigo-600 hover:text-indigo-500">
                Sign in
            </Link>
        </p>
    </GuestLayout>
</template>
