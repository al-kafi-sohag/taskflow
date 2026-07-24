<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: { type: String, default: '#' },
    label: { type: String, required: true },
    count: { type: [String, Number], default: null },
    dotClass: { type: String, default: 'bg-slate-400' },
    active: { type: Boolean, default: false },
});
</script>

<template>
    <Link
        :href="href"
        class="flex gap-3 items-center px-3 py-2 text-sm font-medium rounded-2xl transition-colors duration-150"
        :class="active ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-slate-100'"
    >
        <span class="w-2.5 h-2.5 rounded-full shrink-0" :class="dotClass" />
        <span class="flex-1 truncate">{{ label }}</span>
        <span v-if="count !== null" class="text-xs font-medium" :class="active ? 'text-primary-500' : 'text-slate-400'">
            {{ count }}
        </span>
    </Link>
</template>
