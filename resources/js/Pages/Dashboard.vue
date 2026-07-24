<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import TaskTable from '@/Components/TaskTable.vue';
import CreateTaskModal from '@/Components/CreateTaskModal.vue';
import KpiCard from '@/Components/KpiCard.vue';
import { Plus, CheckSquare, CircleCheck, Activity, AlertCircle } from 'lucide-vue-next';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useTaskModal } from '@/Composables/useTaskModal';

const props = defineProps({
    statusOptions: { type: Array, default: () => [] },
    priorityOptions: { type: Array, default: () => [] },
    projectOptions: { type: Array, default: () => [] },
    metrics: { type: Object, default: () => ({}) },
});

const page = usePage();
const firstName = computed(() => page.props.auth.user?.name?.split(' ')[0] ?? '');

const {
    showTaskModal,
    editingTaskId,
    taskTable,
    openCreate,
    openEdit,
    closeModal,
    handleTaskSaved,
} = useTaskModal();
</script>

<template>
    <AppLayout title="Dashboard" :subtitle="`Good morning, ${firstName} — here's what's happening`">
        <template #topbar-actions>
            <button
                type="button"
                class="flex gap-2 items-center px-4 py-2 text-sm font-medium text-white rounded-2xl shadow-sm bg-primary-600 shadow-primary-200 hover:bg-primary-700"
                @click="openCreate"
            >
                <Plus class="w-4 h-4" />
                New Task
            </button>
        </template>

        <div class="space-y-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <KpiCard
                    :icon="CheckSquare"
                    icon-bg-class="bg-indigo-50"
                    icon-text-class="text-indigo-600"
                    :value="metrics.total?.value ?? 0"
                    label="Total Tasks"
                    :change="metrics.total?.change ?? 0"
                    :trend="metrics.total?.trend ?? 'up'"
                />
                <KpiCard
                    :icon="CircleCheck"
                    icon-bg-class="bg-emerald-50"
                    icon-text-class="text-emerald-600"
                    :value="metrics.completed?.value ?? 0"
                    label="Completed"
                    :change="metrics.completed?.change ?? 0"
                    :trend="metrics.completed?.trend ?? 'up'"
                />
                <KpiCard
                    :icon="Activity"
                    icon-bg-class="bg-amber-50"
                    icon-text-class="text-amber-600"
                    :value="metrics.in_progress?.value ?? 0"
                    label="In Progress"
                    :change="metrics.in_progress?.change ?? 0"
                    :trend="metrics.in_progress?.trend ?? 'up'"
                />
                <KpiCard
                    :icon="AlertCircle"
                    icon-bg-class="bg-red-50"
                    icon-text-class="text-red-600"
                    :value="metrics.overdue?.value ?? 0"
                    label="Overdue"
                    :change="metrics.overdue?.change ?? 0"
                    :trend="metrics.overdue?.trend ?? 'up'"
                />
            </div>

            <TaskTable
                ref="taskTable"
                data-url="/dashboard/tasks"
                :per-page="8"
                :status-options="statusOptions"
                :priority-options="priorityOptions"
                :project-options="projectOptions"
                @edit="openEdit"
            />
        </div>

        <CreateTaskModal
            :open="showTaskModal"
            :task-id="editingTaskId"
            :status-options="statusOptions"
            :priority-options="priorityOptions"
            @close="closeModal"
            @created="handleTaskSaved"
            @updated="handleTaskSaved"
        />
    </AppLayout>
</template>
