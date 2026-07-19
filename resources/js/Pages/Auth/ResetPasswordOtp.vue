<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import { RotateCw } from 'lucide-vue-next'
import { ref, nextTick } from 'vue'

const props = defineProps({
    email: { type: String, default: '' },
})

const digits = ref(['', '', '', '', '', ''])
const inputs = ref([])
const resending = ref(false)

const form = useForm({
    email: props.email,
    code: '',
    password: '',
    password_confirmation: '',
})

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
    form.post(route('password.store'), {
        onError: () => {
            if (form.errors.code) {
                digits.value = ['', '', '', '', '', '']
                inputs.value[0]?.focus()
            }
        },
    })
}

const resend = () => {
    resending.value = true
    router.post(route('password.email'), { email: props.email }, {
        preserveScroll: true,
        onFinish: () => (resending.value = false),
    })
}
</script>

<template>
    <Head title="Reset password" />
    <GuestLayout>
        <h1 class="text-2xl font-bold text-gray-900">Reset your password</h1>
        <p class="mt-1 text-sm text-gray-500">Enter the 6-digit code sent to {{ email }} and choose a new password</p>

        <form class="mt-8 space-y-5" @submit.prevent="submit">
            <div>
                <InputLabel value="Verification code" />
                <div class="mt-1.5 flex justify-between gap-2">
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
                class="w-full rounded-full bg-indigo-600 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:opacity-50"
                :disabled="form.processing"
            >
                Reset password
            </button>
        </form>

        <button
            type="button"
            class="mt-4 flex w-full items-center justify-center gap-1.5 text-sm font-semibold text-indigo-600 hover:text-indigo-500 disabled:opacity-50"
            :disabled="resending"
            @click="resend"
        >
            <RotateCw class="h-3.5 w-3.5" :class="{ 'animate-spin': resending }" />
            Resend code
        </button>

        <Link :href="route('login')" class="mt-4 flex items-center justify-center text-sm text-gray-500 hover:text-gray-700">
            Back to sign in
        </Link>
    </GuestLayout>
</template>