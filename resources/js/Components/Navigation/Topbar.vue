<script setup>
import { Search, Menu } from 'lucide-vue-next';
import UserMenu from '@/Components/Navigation/UserMenu.vue';
import NotificationsMenu from '@/Components/Navigation/NotificationsMenu.vue';

defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, default: '' },
});

defineEmits(['toggle-sidebar']);
</script>

<template>
    <header class="flex h-16 shrink-0 items-center justify-between border-b border-slate-200 bg-white px-4 sm:px-6">
        <div class="flex items-center gap-3">
            <button
                type="button"
                class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 lg:hidden"
                @click="$emit('toggle-sidebar')"
                aria-label="Open sidebar"
            >
                <Menu class="h-5 w-5" />
            </button>

            <div>
                <h1 class="text-base font-semibold text-slate-900">{{ title }}</h1>
                <p v-if="subtitle" class="text-xs text-slate-500">{{ subtitle }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div class="relative hidden sm:block">
                <Search class="pointer-events-none absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-400" />
                <input
                    type="text"
                    placeholder="Search tasks..."
                    class="w-56 rounded-2xl border-0 bg-slate-100 py-2 pl-8 pr-4 text-sm text-slate-700 placeholder:text-slate-400 focus:ring-2 focus:ring-primary-600"
                />
            </div>

            <slot name="actions" />

            <NotificationsMenu />

            <UserMenu />
        </div>
    </header>
</template>
