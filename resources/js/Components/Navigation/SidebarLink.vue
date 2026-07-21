<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: { type: String, required: true },
    active: { type: Boolean, default: false },
    badge: { type: [String, Number], default: null },
    collapsed: { type: Boolean, default: false },
});

const classes = computed(() =>
    props.active
        ? 'bg-primary-600 text-white shadow-sm shadow-primary-100'
        : 'text-slate-600 hover:bg-slate-100',
);

const badgeClasses = computed(() =>
    props.active ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-600',
);
</script>

<template>
    <Link
        :href="href"
        class="flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-medium transition-colors duration-150"
        :class="[classes, { 'lg:justify-center lg:px-0': collapsed }]"
        :title="collapsed ? undefined : undefined"
    >
        <span class="h-[18px] w-[18px] shrink-0">
            <slot name="icon" />
        </span>

        <span class="flex-1 truncate" :class="{ 'lg:hidden': collapsed }">
            <slot />
        </span>

        <span
            v-if="badge"
            class="rounded-full px-2 py-0.5 text-xs font-bold"
            :class="[badgeClasses, { 'lg:hidden': collapsed }]"
        >
            {{ badge }}
        </span>
    </Link>
</template>
