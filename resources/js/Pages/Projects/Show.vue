<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import TaskTable from '@/Components/TaskTable.vue';
import CreateTaskModal from '@/Components/CreateTaskModal.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { Plus } from 'lucide-vue-next';
import { useTaskModal } from '@/Composables/useTaskModal';

const props = defineProps({
    project: { type: Object, required: true },
    statusOptions: { type: Array, default: () => [] },
    priorityOptions: { type: Array, default: () => [] },
});

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
    <AppLayout :title="project.title" :subtitle="project.description || 'Project tasks'">
        <template #topbar-actions>
            <div class="flex gap-3 items-center">
                <StatusBadge :status="project.status" />
                <button
                    type="button"
                    class="flex gap-2 items-center px-4 py-2 text-sm font-medium text-white rounded-2xl shadow-sm bg-primary-600 shadow-primary-200 hover:bg-primary-700"
                    @click="openCreate"
                >
                    <Plus class="w-4 h-4" />
                    New Task
                </button>
            </div>
        </template>

        <div class="space-y-6">
            <TaskTable
                ref="taskTable"
                :data-url="`/projects/${project.slug}/tasks`"
                :per-page="8"
                :status-options="statusOptions"
                :priority-options="priorityOptions"
                :fixed-project-slug="project.slug"
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
