<script setup>
defineProps({
    // { value, name, label, badge: { bg, text, border, dot } } — as
    // returned by TaskPriority::badge()/label() from the backend.
    priority: { type: Object, required: true },
});
</script>

<template>
    <span
        class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full border px-2 py-1 text-xs font-medium"
        :class="[priority.badge.bg, priority.badge.text, priority.badge.border]"
    >
        <span class="h-1.5 w-1.5 rounded-full" :class="priority.badge.dot" />
        {{ priority.label }}
    </span>
</template>
