<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const firstName = computed(() => page.props.auth.user?.name?.split(' ')[0] ?? '');
</script>

<template>
    <AppLayout
        title="Dashboard"
        :subtitle="`Good morning, ${firstName} — here's what's happening`"
    >
        <div class="space-y-6">
            <!-- KPI cards row -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="n in 4"
                    :key="n"
                    class="rounded-2xl border border-slate-200 bg-white p-5"
                >
                    <!-- TODO: KPI card content (icon, trend badge, value, label) -->
                </div>
            </div>

            <!-- Task table -->
            <div class="rounded-2xl border border-slate-200 bg-white">
                <!-- TODO: table header (search/filter), rows, pagination -->
            </div>
        </div>
    </AppLayout>
</template>
