<script setup>
import Dropdown from '@/Components/Dropdown.vue';
import { Bell, CheckSquare, AlertTriangle } from 'lucide-vue-next';

// TODO: replace with real data from a shared Inertia prop or an API call
const notifications = [
    { id: 1, icon: CheckSquare, text: 'Jordan marked "Set up CI/CD pipeline" as done', time: '10m ago' },
    { id: 2, icon: AlertTriangle, text: '"Fix authentication token refresh bug" is overdue', time: '1h ago' },
];
</script>

<template>
    <Dropdown align="right" width="80">
        <template #trigger>
            <button
                type="button"
                class="relative flex h-9 w-9 items-center justify-center rounded-2xl bg-slate-100 text-slate-600 hover:bg-slate-200"
                aria-label="Notifications"
            >
                <Bell class="h-4 w-4" />
                <span
                    v-if="notifications.length"
                    class="absolute right-1.5 top-1.5 h-2 w-2 rounded-full bg-primary-600 ring-2 ring-white"
                />
            </button>
        </template>

        <template #content>
            <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                <span class="text-sm font-semibold text-slate-800">Notifications</span>
                <span class="text-xs text-slate-400">{{ notifications.length }} new</span>
            </div>

            <div class="max-h-72 divide-y divide-slate-100 overflow-y-auto">
                <div
                    v-for="n in notifications"
                    :key="n.id"
                    class="flex items-start gap-3 px-4 py-3 hover:bg-slate-50"
                >
                    <component :is="n.icon" class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" />
                    <div class="min-w-0">
                        <p class="truncate text-sm text-slate-700">{{ n.text }}</p>
                        <p class="text-xs text-slate-400">{{ n.time }}</p>
                    </div>
                </div>

                <div v-if="!notifications.length" class="px-4 py-6 text-center text-sm text-slate-400">
                    You're all caught up.
                </div>
            </div>
        </template>
    </Dropdown>
</template>
