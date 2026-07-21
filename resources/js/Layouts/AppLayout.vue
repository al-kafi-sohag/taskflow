<script setup>
import { ref, onMounted, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import Sidebar from '@/Components/Navigation/Sidebar.vue';
import Topbar from '@/Components/Navigation/Topbar.vue';

defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, default: '' },
});

const mobileOpen = ref(false);
const collapsed = ref(false);

// Persist collapse preference (real app, not an artifact — localStorage is fine here)
onMounted(() => {
    collapsed.value = localStorage.getItem('sidebar:collapsed') === '1';
});

watch(collapsed, (value) => {
    localStorage.setItem('sidebar:collapsed', value ? '1' : '0');
});
</script>

<template>
    <Head :title="title" />

    <div class="flex h-screen overflow-hidden bg-slate-50">
        <Sidebar
            :open="mobileOpen"
            :collapsed="collapsed"
            @close="mobileOpen = false"
            @toggle-collapse="collapsed = !collapsed"
        />

        <div class="flex min-w-0 flex-1 flex-col">
            <Topbar :title="title" :subtitle="subtitle" @toggle-sidebar="mobileOpen = true">
                <template #actions>
                    <slot name="topbar-actions" />
                </template>
            </Topbar>

            <main class="flex-1 overflow-y-auto p-6">
                <slot />
            </main>
        </div>
    </div>
</template>
