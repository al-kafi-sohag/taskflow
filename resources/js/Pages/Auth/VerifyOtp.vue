<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import { RotateCw, ChevronLeft } from 'lucide-vue-next'
import { ref, nextTick } from 'vue'

const props = defineProps({
    email: { type: String, default: '' },
    status: { type: String, default: null },
})

const digits = ref(['', '', '', '', '', ''])
const inputs = ref([])
const resending = ref(false)
const form = useForm({ code: '' })

const onInput = (index, event) => {
    const value = event.target.value.replace(/\D/g, '').slice(-1)
    digits.value[index] = value
    if (value && index < 5) nextTick(() => inputs.value[index + 1]?.focus())
}

const onKeydown = (index, event) => {
    if (event.key === 'Backspace' && !digits.value[index] && index > 0) {
        inputs.value[index - 1]?.focus()
    }
}

const submit = () => {
    form.code = digits.value.join('')
    form.post(route('otp.verify'), {
        onError: () => {
            digits.value = ['', '', '', '', '', '']
            inputs.value[0]?.focus()
        },
    })
}

const resend = () => {
    resending.value = true
    router.post(route('verification.send'), {}, {
        preserveScroll: true,
        onFinish: () => (resending.value = false),
    })
}
</script>

<template>
    <Head title="Verify your email" />
    <GuestLayout>
        <h1 class="text-2xl font-bold text-gray-900">Check your email</h1>
        <p class="mt-1 text-sm text-gray-500">We sent a 6-digit verification code to {{ email }}</p>

        <div v-if="status === 'verification-link-sent'" class="mt-4 text-sm font-medium text-green-600">
            A new code has been sent.
        </div>

        <form class="mt-8" @submit.prevent="submit">
            <div class="flex justify-between gap-2">
                <input
                    v-for="(digit, index) in digits"
                    :key="index"
                    :ref="el => (inputs[index] = el)"
                    v-model="digits[index]"
                    type="text"
                    inputmode="numeric"
                    maxlength="1"
                    class="h-12 w-12 rounded-xl border border-gray-200 text-center text-lg font-semibold focus:border-indigo-500 focus:ring-indigo-500"
                    @input="onInput(index, $event)"
                    @keydown="onKeydown(index, $event)"
                />
            </div>
            <p v-if="form.errors.code" class="mt-3 text-sm text-red-600">{{ form.errors.code }}</p>

            <button
                type="submit"
                class="mt-8 w-full rounded-full bg-indigo-600 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:opacity-50"
                :disabled="form.processing"
            >
                Verify & continue
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-400">Didn't receive the code?</p>
        <button
            type="button"
            class="mt-1 flex w-full items-center justify-center gap-1.5 text-sm font-semibold text-indigo-600 hover:text-indigo-500 disabled:opacity-50"
            :disabled="resending"
            @click="resend"
        >
            <RotateCw class="h-3.5 w-3.5" :class="{ 'animate-spin': resending }" />
            Resend code
        </button>

        <Link :href="route('logout')" method="post" as="button" class="mt-4 flex w-full items-center justify-center gap-1 text-sm text-gray-500 hover:text-gray-700">
            <ChevronLeft class="h-4 w-4" />
            Back to sign in
        </Link>
    </GuestLayout>
</template>