<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import TaskTable from '@/Components/TaskTable.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
    statusOptions: { type: Array, default: () => [] },
    priorityOptions: { type: Array, default: () => [] },
});

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
            <TaskTable
                data-url="/dashboard/tasks"
                :per-page="8"
                :status-options="statusOptions"
                :priority-options="priorityOptions"
            />
        </div>
    </AppLayout>
</template>
