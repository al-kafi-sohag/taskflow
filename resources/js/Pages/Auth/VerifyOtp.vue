<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import { RotateCw, ChevronLeft, LoaderCircle } from 'lucide-vue-next'
import { ref, nextTick } from 'vue'

const props = defineProps({
    email: { type: String, default: '' },
    status: { type: String, default: null },
})

const CODE_LENGTH = 6

const digits = ref(Array(CODE_LENGTH).fill(''))
const inputs = ref([])
const resending = ref(false)
const form = useForm({ code: '' })

const focusInput = (index) => nextTick(() => inputs.value[index]?.focus())

const resetDigits = () => {
    digits.value = Array(CODE_LENGTH).fill('')
    focusInput(0)
}

const submit = () => {
    if (form.processing) return

    form.code = digits.value.join('')
    if (form.code.length !== CODE_LENGTH) return

    form.post(route('otp.verify'), {
        onError: () => resetDigits(),
    })
}

const onInput = (index, event) => {
    const value = event.target.value.replace(/\D/g, '').slice(-1)
    digits.value[index] = value
    event.target.value = value

    if (value && index < CODE_LENGTH - 1) {
        focusInput(index + 1)
        return
    }

    if (value && index === CODE_LENGTH - 1 && digits.value.every((d) => d !== '')) {
        submit()
    }
}

const onKeydown = (index, event) => {
    if (event.key === 'Backspace' && !digits.value[index] && index > 0) {
        focusInput(index - 1)
    } else if (event.key === 'ArrowLeft' && index > 0) {
        event.preventDefault()
        focusInput(index - 1)
    } else if (event.key === 'ArrowRight' && index < CODE_LENGTH - 1) {
        event.preventDefault()
        focusInput(index + 1)
    }
}

const onPaste = (event) => {
    event.preventDefault()
    const pasted = (event.clipboardData || window.clipboardData).getData('text')
    const value = pasted.replace(/\D/g, '').slice(0, CODE_LENGTH)
    if (!value) return

    digits.value = Array(CODE_LENGTH).fill('')
    value.split('').forEach((char, i) => (digits.value[i] = char))

    focusInput(value.length < CODE_LENGTH ? value.length : CODE_LENGTH - 1)

    if (value.length === CODE_LENGTH) submit()
}

const resend = () => {
    resending.value = true
    resetDigits()
    router.post(route('verification.send'), {}, {
        preserveScroll: true,
        onFinish: () => (resending.value = false),
    })
}
</script>

<template>
    <Head title="Verify your email" />
    <GuestLayout>
        <h1 class="text-2xl font-bold text-slate-900">Check your email</h1>
        <p class="mt-1 text-sm text-slate-500">
            We sent a 6-digit verification code to
            <span class="font-medium text-slate-700">{{ email }}</span>
        </p>

        <div v-if="status === 'verification-link-sent'" class="mt-4 rounded-xl bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-700">
            A new code has been sent.
        </div>

        <form class="mt-8" @submit.prevent="submit">
            <div class="flex justify-between gap-2" @paste="onPaste">
                <input
                    v-for="(digit, index) in digits"
                    :key="index"
                    :ref="el => (inputs[index] = el)"
                    v-model="digits[index]"
                    type="text"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    maxlength="1"
                    :disabled="form.processing"
                    class="h-12 w-12 rounded-xl border border-slate-200 text-center text-lg font-semibold text-slate-900 transition focus:border-[#4F39F6] focus:outline-none focus:ring-2 focus:ring-[#4F39F6]/20 disabled:bg-slate-50 disabled:opacity-60"
                    @input="onInput(index, $event)"
                    @keydown="onKeydown(index, $event)"
                    @focus="$event.target.select()"
                />
            </div>
            <p v-if="form.errors.code" class="mt-3 text-sm text-red-600">{{ form.errors.code }}</p>

            <button
                type="submit"
                class="mt-8 flex w-full items-center justify-center gap-2 rounded-full bg-[#4F39F6] py-2.5 text-sm font-semibold text-white transition hover:bg-[#4230d1] disabled:opacity-50"
                :disabled="form.processing"
            >
                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                {{ form.processing ? 'Verifying…' : 'Verify & continue' }}
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-400">Didn't receive the code?</p>
        <button
            type="button"
            class="mt-1 flex w-full items-center justify-center gap-1.5 text-sm font-semibold text-[#4F39F6] hover:text-[#4230d1] disabled:opacity-50"
            :disabled="resending || form.processing"
            @click="resend"
        >
            <RotateCw class="h-3.5 w-3.5" :class="{ 'animate-spin': resending }" />
            Resend code
        </button>

        <Link :href="route('logout')" method="post" as="button" class="mt-4 flex w-full items-center justify-center gap-1 text-sm text-slate-500 hover:text-slate-700">
            <ChevronLeft class="h-4 w-4" />
            Back to sign in
        </Link>
    </GuestLayout>
</template>
