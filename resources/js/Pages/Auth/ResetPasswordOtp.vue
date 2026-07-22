<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import { RotateCw, LoaderCircle } from 'lucide-vue-next'
import { ref, nextTick } from 'vue'

const props = defineProps({
    email: { type: String, default: '' },
})

const CODE_LENGTH = 6

const digits = ref(Array(CODE_LENGTH).fill(''))
const inputs = ref([])
const resending = ref(false)

const form = useForm({
    email: props.email,
    code: '',
    password: '',
    password_confirmation: '',
})

const focusInput = (index) => nextTick(() => inputs.value[index]?.focus())

const resetCode = () => {
    digits.value = Array(CODE_LENGTH).fill('')
    focusInput(0)
}

const submit = () => {
    if (form.processing) return

    form.code = digits.value.join('')
    if (form.code.length !== CODE_LENGTH) return

    form.post(route('password.store'), {
        onError: () => {
            if (form.errors.code) resetCode()
        },
    })
}

const onInput = (index, event) => {
    const value = event.target.value.replace(/\D/g, '').slice(-1)
    digits.value[index] = value
    event.target.value = value

    if (value && index < CODE_LENGTH - 1) {
        focusInput(index + 1)
    }
    // Note: unlike VerifyOtp, we don't auto-submit here — password fields
    // still need to be filled in, so completing the code just advances focus.
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
}

const resend = () => {
    resending.value = true
    resetCode()
    router.post(route('password.email'), { email: props.email }, {
        preserveScroll: true,
        onFinish: () => (resending.value = false),
    })
}
</script>

<template>
    <Head title="Reset password" />
    <GuestLayout>
        <h1 class="text-2xl font-bold text-slate-900">Reset your password</h1>
        <p class="mt-1 text-sm text-slate-500">Enter the 6-digit code sent to {{ email }} and choose a new password</p>

        <form class="mt-8 space-y-5" @submit.prevent="submit">
            <div>
                <InputLabel value="Verification code" />
                <div class="mt-1.5 flex justify-between gap-2" @paste="onPaste">
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
                <InputError class="mt-2" :message="form.errors.code" />
            </div>

            <div>
                <InputLabel for="password" value="New password" />
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

            <div>
                <InputLabel for="password_confirmation" value="Confirm new password" />
                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1.5 block w-full rounded-xl"
                    placeholder="Re-enter password"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <button
                type="submit"
                class="flex w-full items-center justify-center gap-2 rounded-full bg-[#4F39F6] py-2.5 text-sm font-semibold text-white transition hover:bg-[#4230d1] disabled:opacity-50"
                :disabled="form.processing"
            >
                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                {{ form.processing ? 'Resetting…' : 'Reset password' }}
            </button>
        </form>

        <button
            type="button"
            class="mt-4 flex w-full items-center justify-center gap-1.5 text-sm font-semibold text-[#4F39F6] hover:text-[#4230d1] disabled:opacity-50"
            :disabled="resending || form.processing"
            @click="resend"
        >
            <RotateCw class="h-3.5 w-3.5" :class="{ 'animate-spin': resending }" />
            Resend code
        </button>

        <Link :href="route('login')" class="mt-4 flex items-center justify-center text-sm text-slate-500 hover:text-slate-700">
            Back to sign in
        </Link>
    </GuestLayout>
</template>
