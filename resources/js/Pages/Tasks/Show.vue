<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import CreateTaskModal from '@/Components/CreateTaskModal.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import PriorityBadge from '@/Components/PriorityBadge.vue';
import { Pencil, Plus, Calendar, Paperclip, MessageSquare, Clock3 } from 'lucide-vue-next';

const props = defineProps({
    task: { type: Object, required: true },
    statusOptions: { type: Array, default: () => [] },
    priorityOptions: { type: Array, default: () => [] },
});

const activeTab = ref('overview');

const showTaskModal = ref(false);
const editingTaskId = ref(null);

function openCreate() {
    editingTaskId.value = null;
    showTaskModal.value = true;
}

function openEdit() {
    editingTaskId.value = props.task.id;
    showTaskModal.value = true;
}

function closeModal() {
    showTaskModal.value = false;
    editingTaskId.value = null;
}

function handleTaskSaved() {
    router.reload({ only: ['task'] });
}

const primaryProject = computed(() => props.task.projects?.[0] ?? null);

function initials(name) {
    return (name ?? '')
        .split(' ')
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0]?.toUpperCase())
        .join('');
}
</script>

<template>
    <AppLayout
        title="Task Detail"
        :subtitle="`${task.code}${primaryProject ? ' · ' + primaryProject.title : ''}`"
    >
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

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Main -->
            <div class="space-y-6 lg:col-span-2">
                <div class="p-6 bg-white rounded-2xl border border-slate-200">
                    <div class="flex justify-between items-start">
                        <div class="flex flex-wrap gap-2 items-center">
                            <span class="px-2 py-1 font-mono text-xs rounded-lg bg-slate-100 text-slate-500">
                                {{ task.code }}
                            </span>
                            <span
                                v-for="project in task.projects"
                                :key="project.id"
                                class="px-2 py-1 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-lg"
                            >
                                {{ project.title }}
                            </span>
                        </div>

                        <button
                            type="button"
                            class="flex gap-1.5 items-center px-4 py-2 text-sm font-medium rounded-2xl border border-slate-200 text-slate-700 hover:bg-slate-50"
                            @click="openEdit"
                        >
                            <Pencil class="w-3.5 h-3.5" />
                            Edit
                        </button>
                    </div>

                    <h1 class="mt-3 text-xl font-semibold text-slate-900">{{ task.title }}</h1>

                    <div class="flex flex-wrap gap-2 items-center mt-3">
                        <StatusBadge :status="task.status" />
                        <PriorityBadge :priority="task.priority" />
                        <span
                            v-for="tag in task.tags"
                            :key="tag"
                            class="rounded-[10px] bg-slate-100 px-2 py-1 text-xs text-slate-500"
                        >
                            {{ tag }}
                        </span>
                    </div>

                    <p class="mt-4 text-sm leading-relaxed text-slate-600">
                        {{ task.description || 'No description provided.' }}
                    </p>

                    <div class="flex gap-6 mt-6 text-sm font-medium border-b border-slate-100">
                        <button
                            type="button"
                            class="pb-3 border-b-2"
                            :class="activeTab === 'overview' ? 'border-primary-600 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700'"
                            @click="activeTab = 'overview'"
                        >
                            Overview
                        </button>
                        <button
                            type="button"
                            class="pb-3 border-b-2"
                            :class="activeTab === 'activity' ? 'border-primary-600 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700'"
                            @click="activeTab = 'activity'"
                        >
                            Activity
                        </button>
                    </div>
                </div>

                <div class="p-6 bg-white rounded-2xl border border-slate-200">
                    <div v-if="activeTab === 'overview'" class="flex flex-col gap-3 justify-center items-center py-12 text-center">
                        <MessageSquare class="w-8 h-8 text-slate-300" />
                        <p class="text-sm font-medium text-slate-500">Comments coming soon</p>
                        <p class="max-w-xs text-xs text-slate-400">
                            Discussion threads on tasks are on the roadmap — check back soon.
                        </p>
                    </div>
                    <div v-else class="flex flex-col gap-3 justify-center items-center py-12 text-center">
                        <Clock3 class="w-8 h-8 text-slate-300" />
                        <p class="text-sm font-medium text-slate-500">Activity log coming soon</p>
                        <p class="max-w-xs text-xs text-slate-400">
                            A full history of changes to this task will appear here.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <div class="p-5 bg-white rounded-2xl border border-slate-200">
                    <p class="mb-4 text-xs font-semibold tracking-widest uppercase text-slate-400">Details</p>

                    <dl class="space-y-4">
                        <div class="flex justify-between items-center">
                            <dt class="text-sm text-slate-500">Assignee</dt>
                            <dd v-if="task.assignees.length" class="flex gap-2 items-center">
                                <span class="flex justify-center items-center w-6 h-6 text-[11px] font-semibold text-violet-700 bg-violet-100 rounded-full">
                                    {{ initials(task.assignees[0].name) }}
                                </span>
                                <span class="text-sm font-medium text-slate-800">
                                    {{ task.assignees[0].name }}
                                    <template v-if="task.assignees.length > 1">+{{ task.assignees.length - 1 }}</template>
                                </span>
                            </dd>
                            <dd v-else class="text-sm text-slate-400">Unassigned</dd>
                        </div>

                        <div class="flex justify-between items-center">
                            <dt class="text-sm text-slate-500">Due Date</dt>
                            <dd class="flex gap-1.5 items-center text-sm font-medium text-slate-800">
                                <Calendar class="w-3.5 h-3.5 text-slate-400" />
                                {{ task.due_date ?? '—' }}
                            </dd>
                        </div>

                        <div class="flex justify-between items-center">
                            <dt class="text-sm text-slate-500">Project</dt>
                            <dd v-if="task.projects.length" class="flex flex-wrap gap-1.5 justify-end">
                                <span
                                    v-for="project in task.projects"
                                    :key="project.id"
                                    class="rounded-[10px] bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-600"
                                >
                                    {{ project.title }}
                                </span>
                            </dd>
                            <dd v-else class="text-sm text-slate-400">—</dd>
                        </div>

                        <div class="flex justify-between items-center">
                            <dt class="text-sm text-slate-500">Created</dt>
                            <dd class="text-sm font-medium text-slate-800">{{ task.created_at }}</dd>
                        </div>

                        <div class="flex justify-between items-center">
                            <dt class="text-sm text-slate-500">Comments</dt>
                            <dd class="text-xs italic text-slate-400">Coming soon</dd>
                        </div>

                        <div class="flex justify-between items-center">
                            <dt class="text-sm text-slate-500">Attachments</dt>
                            <dd class="flex gap-1.5 items-center text-sm font-medium text-slate-800">
                                <Paperclip class="w-3.5 h-3.5 text-slate-400" />
                                {{ task.attachments_count }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="p-5 bg-white rounded-2xl border border-slate-200">
                    <p class="mb-3 text-xs font-semibold tracking-widest uppercase text-slate-400">Attachments</p>
                    <p class="py-6 text-sm text-center text-slate-400">Coming soon</p>
                </div>
            </div>
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
