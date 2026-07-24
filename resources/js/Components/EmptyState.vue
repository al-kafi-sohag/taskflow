<script setup>
defineProps({
    title: { type: String, default: 'Nothing here yet' },
    description: { type: String, default: '' },
});
</script>

<template>
    <div class="flex flex-col items-center justify-center gap-1 py-6 text-center">
        <p class="text-sm font-medium text-slate-600">{{ title }}</p>
        <p v-if="description" class="text-xs text-slate-400">{{ description }}</p>
    </div>
</template>
