<script setup>
import { TrendingUp, TrendingDown } from 'lucide-vue-next';

defineProps({
    icon: { type: [Object, Function], required: true },
    iconBgClass: { type: String, default: 'bg-slate-100' },
    iconTextClass: { type: String, default: 'text-slate-600' },
    value: { type: [Number, String], required: true },
    label: { type: String, required: true },
    change: { type: Number, default: 0 },
    trend: { type: String, default: 'up' }, // 'up' | 'down'
    helperText: { type: String, default: 'vs last month' },
});
</script>

<template>
    <div class="p-5 bg-white rounded-2xl border border-slate-200">
        <div class="flex justify-between items-start mb-4">
            <span
                class="flex justify-center items-center w-10 h-10 rounded-2xl"
                :class="iconBgClass"
            >
                <component :is="icon" class="w-5 h-5" :class="iconTextClass" />
            </span>

            <span
                class="inline-flex gap-1 items-center px-2 py-1 text-xs font-semibold rounded-full"
                :class="trend === 'up' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600'"
            >
                <component :is="trend === 'up' ? TrendingUp : TrendingDown" class="w-3 h-3" />
                {{ change }}%
            </span>
        </div>

        <p class="text-2xl font-bold text-slate-900">{{ value }}</p>
        <p class="mt-1 text-sm font-medium text-slate-700">{{ label }}</p>
        <p class="text-xs text-slate-400">{{ helperText }}</p>
    </div>
</template>
