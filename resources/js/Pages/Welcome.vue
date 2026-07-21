<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { Zap, CheckSquare, Users, BarChart3 } from 'lucide-vue-next'
</script>

<template>
    <Head title="Welcome" />
    <div class="min-h-screen bg-white flex items-center justify-center p-4">
        <div class="w-full max-w-md text-center">
            <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-600">
                <Zap class="h-7 w-7 text-white" />
            </span>

            <h1 class="mt-6 text-3xl font-bold text-gray-900">Welcome to {{ appName }}</h1>
            <p class="mt-3 text-sm leading-relaxed text-gray-500">
                The modern task management platform designed for high-performance teams.
                Ship faster, collaborate smarter, stay organized.
            </p>

            <div class="mt-8 space-y-3">
                <Link
                    :href="route('register')"
                    class="block w-full rounded-full bg-indigo-600 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700"
                >
                    Get started for free
                </Link>
                <Link
                    :href="route('login')"
                    class="block w-full rounded-full border border-gray-200 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                >
                    Sign in to your account
                </Link>
            </div>

            <div class="mt-10 flex justify-center gap-10">
                <div class="flex flex-col items-center gap-2">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-50">
                        <CheckSquare class="h-4 w-4 text-indigo-600" />
                    </span>
                    <span class="text-xs text-gray-500">Smart task tracking</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-green-50">
                        <Users class="h-4 w-4 text-green-600" />
                    </span>
                    <span class="text-xs text-gray-500">Team collaboration</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50">
                        <BarChart3 class="h-4 w-4 text-blue-600" />
                    </span>
                    <span class="text-xs text-gray-500">Progress analytics</span>
                </div>
            </div>
        </div>
    </div>
</template>
