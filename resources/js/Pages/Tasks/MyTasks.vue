<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import TaskTable from '@/Components/TaskTable.vue';
import CreateTaskModal from '@/Components/CreateTaskModal.vue';
import { Plus } from 'lucide-vue-next';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useTaskModal } from '@/Composables/useTaskModal';

defineProps({
    statusOptions: { type: Array, default: () => [] },
    priorityOptions: { type: Array, default: () => [] },
    projectOptions: { type: Array, default: () => [] },
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
    <AppLayout title="My Tasks" :subtitle="`Tasks assigned to you, ${firstName}`">
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
            <TaskTable
                ref="taskTable"
                data-url="/my-tasks/tasks"
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
