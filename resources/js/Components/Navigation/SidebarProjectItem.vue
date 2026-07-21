<script setup>
defineProps({
    href: { type: String, default: '#' },
    label: { type: String, required: true },
    count: { type: [String, Number], default: null },
    dotClass: { type: String, default: 'bg-slate-400' },
});
</script>

<template>
    <Link
        :href="href"
        class="flex items-center gap-3 rounded-2xl px-3 py-2 text-sm font-medium text-slate-600 transition-colors duration-150 hover:bg-slate-100"
    >
        <span class="h-2.5 w-2.5 shrink-0 rounded-full" :class="dotClass" />
        <span class="flex-1 truncate">{{ label }}</span>
        <span v-if="count !== null" class="text-xs font-medium text-slate-400">
            {{ count }}
        </span>
    </Link>
</template>
