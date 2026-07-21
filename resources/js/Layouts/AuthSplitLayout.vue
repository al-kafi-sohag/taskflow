<script setup>
import { Link } from '@inertiajs/vue3'
import { Zap } from 'lucide-vue-next'
const appName = import.meta.env.VITE_APP_NAME
</script>

<template>
    <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
        <div class="w-full max-w-6xl bg-white rounded-3xl shadow-sm overflow-hidden flex flex-col lg:flex-row min-h-[640px]">
            <!-- Left: brand panel -->
            <div class="relative hidden lg:flex lg:w-[46%] flex-col justify-between p-10 overflow-hidden
                        bg-gradient-to-br from-indigo-600 via-indigo-600 to-violet-600 text-white">
                <div class="pointer-events-none absolute -top-24 -right-20 h-72 w-72 rounded-full bg-white/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-28 -left-16 h-64 w-64 rounded-full bg-white/5 blur-2xl"></div>

                <Link href="/" class="relative z-10 flex items-center gap-2">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/15">
                        <Zap class="h-5 w-5 text-white" />
                    </span>
                    <span class="text-lg font-semibold">{{ appName }}</span>
                </Link>

                <blockquote class="relative z-10 max-w-sm">
                    <p class="text-xl font-medium leading-relaxed">
                        "{{ appName }} completely transformed how our team collaborates. We ship 40% faster now."
                    </p>
                    <footer class="mt-6 flex items-center gap-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-white/15 text-xs font-semibold">
                            MR
                        </span>
                        <div class="text-sm">
                            <div class="font-semibold">Maya Rodriguez</div>
                            <div class="text-white/70">CTO, Meridian Labs</div>
                        </div>
                    </footer>
                </blockquote>

                <div class="relative z-10 grid grid-cols-3 gap-4 text-center">
                    <div>
                        <div class="text-lg font-bold">10K+</div>
                        <div class="text-xs text-white/70">Teams</div>
                    </div>
                    <div>
                        <div class="text-lg font-bold">99.9%</div>
                        <div class="text-xs text-white/70">Uptime</div>
                    </div>
                    <div>
                        <div class="text-lg font-bold">4.9/5</div>
                        <div class="text-xs text-white/70">Rating</div>
                    </div>
                </div>
            </div>

            <!-- Right: form panel -->
            <div class="flex w-full lg:w-[54%] items-center justify-center p-8 sm:p-14">
                <div class="w-full max-w-sm">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
