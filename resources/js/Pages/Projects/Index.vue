<script setup>
import { ref } from 'vue';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import EmptyState from '@/Components/EmptyState.vue';
import CreateProjectModal from '@/Components/CreateProjectModal.vue';
import ProjectDetailsModal from '@/Components/ProjectDetailsModal.vue';
import { router } from '@inertiajs/vue3';
import { FolderKanban, Plus, Eye, Pencil, Trash2 } from 'lucide-vue-next';
import { confirmDelete, successAlert, errorAlert } from '@/swal';

defineProps({
    projects: { type: Array, default: () => [] },
    statusOptions: { type: Array, default: () => [] },
});

const showProjectModal = ref(false);
const editingProjectSlug = ref(null);
const deletingSlug = ref(null);

const showDetailsModal = ref(false);
const viewingProject = ref(null);

function openCreate() {
    editingProjectSlug.value = null;
    showProjectModal.value = true;
}

function openEdit(projectSlug) {
    editingProjectSlug.value = projectSlug;
    showProjectModal.value = true;
}

function closeModal() {
    showProjectModal.value = false;
    editingProjectSlug.value = null;
}

function openView(project) {
    viewingProject.value = project;
    showDetailsModal.value = true;
}

function closeView() {
    showDetailsModal.value = false;
}

async function confirmDeleteProject(project) {
    const result = await confirmDelete({
        title: 'Delete this project?',
        text: `"${project.title}" will be deleted.`,
    });

    if (!result.isConfirmed) return;

    deletingSlug.value = project.slug;

    try {
        await axios.delete(route('p.destroy', project.slug));
        successAlert('The project has been deleted.', 'Deleted');
        router.reload({ only: ['projects'] });
    } catch (error) {
        console.error('Failed to delete project', error);
        errorAlert(error.response?.data?.message ?? 'Could not delete this project. Please try again.');
    } finally {
        deletingSlug.value = null;
    }
}
</script>

<template>
    <AppLayout title="Projects" subtitle="All active projects at a glance">
        <template #topbar-actions>
            <button
                type="button"
                class="flex gap-2 items-center px-4 py-2 text-sm font-medium text-white rounded-2xl shadow-sm bg-primary-600 shadow-primary-200 hover:bg-primary-700"
                @click="openCreate"
            >
                <Plus class="w-4 h-4" />
                New Project
            </button>
        </template>

        <div class="space-y-6">
            <div v-if="projects.length === 0" class="p-4 bg-white rounded-2xl border border-slate-200">
                <EmptyState title="No projects yet" description="Create your first project to get started." />
            </div>

            <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="project in projects"
                    :key="project.id"
                    class="p-5 bg-white rounded-2xl border transition-colors border-slate-200 hover:border-primary-200"
                >
                    <div class="flex gap-3 justify-between items-start">
                        <div class="flex gap-3 items-center min-w-0">
                            <span class="flex justify-center items-center w-9 h-9 rounded-2xl bg-primary-50 text-primary-600 shrink-0">
                                <FolderKanban class="w-4 h-4" />
                            </span>
                            <h3 class="text-sm font-semibold truncate text-slate-800">{{ project.title }}</h3>
                        </div>

                        <StatusBadge :status="project.status" />
                    </div>

                    <p v-if="project.description" class="mt-3 text-xs text-slate-500 line-clamp-2">
                        {{ project.description }}
                    </p>

                    <div class="flex justify-between items-center pt-4 mt-4 border-t border-slate-100">
                        <p class="text-xs font-medium text-slate-400">
                            {{ project.tasks_count }} {{ project.tasks_count === 1 ? 'task' : 'tasks' }}
                        </p>

                        <div class="flex gap-3 items-center">
                            <button type="button" class="text-slate-400 hover:text-primary-600" title="View project" @click="openView(project)">
                                <Eye class="w-4 h-4" />
                            </button>
                            <button type="button" class="text-blue-500 hover:text-blue-600" title="Edit project" @click="openEdit(project.slug)">
                                <Pencil class="w-4 h-4" />
                            </button>
                            <button
                                type="button"
                                class="text-red-500 hover:text-red-600 disabled:cursor-not-allowed disabled:opacity-40"
                                title="Delete project"
                                :disabled="deletingSlug === project.slug"
                                @click="confirmDeleteProject(project)"
                            >
                                <Trash2 class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <CreateProjectModal
            :open="showProjectModal"
            :project-slug="editingProjectSlug"
            :status-options="statusOptions"
            @close="closeModal"
            @created="closeModal"
            @updated="closeModal"
        />

        <ProjectDetailsModal
            :open="showDetailsModal"
            :project="viewingProject"
            @close="closeView"
        />
    </AppLayout>
</template>
