<script setup>
import { computed, nextTick, ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { X, Plus, Pencil } from 'lucide-vue-next';
import axios from 'axios';

const props = defineProps({
    open: { type: Boolean, default: false },
    taskId: { type: [Number, String], default: null },
    statusOptions: { type: Array, default: () => [] },
    priorityOptions: { type: Array, default: () => [] },
});

const emit = defineEmits(['close', 'created', 'updated']);

const assignees = ref([]); // [{ id, name }]
const projects = ref([]);  // [{ id, title }]
const tagOptions = ref([]);
const optionsLoading = ref(false);
const optionsLoaded = ref(false);
const taskLoading = ref(false);
const loadError = ref(null);

const titleInput = ref(null);

const form = useForm({
    title: '',
    description: '',
    status: '',
    priority: '',
    assignee_ids: [],
    due_date: '',
    project_ids: [],
    tags: [],
});

// vue-multiselect works best bound to objects, so these are local
// "selected object[]" refs that stay in sync with the primitive id arrays.
const selectedAssignees = ref([]); // [{ id, name }]
const selectedProjects = ref([]);  // [{ id, title }]
const selectedTags = ref([]);      // [{ title }]

const isEditing = computed(() => !!props.taskId);

async function loadFormOptions() {
    if (optionsLoaded.value || optionsLoading.value) return;

    optionsLoading.value = true;
    try {
        const { data } = await axios.get(route('tasks.form-options'));
        assignees.value = data.assignees ?? [];
        projects.value = data.projects ?? [];
        tagOptions.value = data.tags ?? [];
        optionsLoaded.value = true;
    } catch (e) {
        console.error('Failed to load form options (assignees/projects/tags):', e);
    } finally {
        optionsLoading.value = false;
    }
}

function hydrateSelections() {
    const assigneeIds = new Set(form.assignee_ids ?? []);
    const projectIds = new Set(form.project_ids ?? []);

    selectedAssignees.value = assignees.value.filter((a) => assigneeIds.has(a.id));
    selectedProjects.value = projects.value.filter((p) => projectIds.has(p.id));
    selectedTags.value = (form.tags ?? []).map((title) => ({ title }));
}

async function loadTaskForEdit(id) {
    taskLoading.value = true;
    loadError.value = null;
    try {
        const { data } = await axios.get(route('tasks.edit', id));
        form.defaults({
            title: data.title ?? '',
            description: data.description ?? '',
            status: data.status ?? '',
            priority: data.priority ?? '',
            assignee_ids: data.assignee_ids ?? [],
            due_date: data.due_date ?? '',
            project_ids: data.project_ids ?? [],
            tags: data.tags ?? [],
        });
        form.reset();
        hydrateSelections();
    } catch (e) {
        console.error('Failed to load task for edit:', e);
        loadError.value = e.response
            ? `Couldn't load this task (${e.response.status} ${e.response.statusText || ''}).`.trim()
            : "Couldn't load this task. Check your connection and try again.";
    } finally {
        taskLoading.value = false;
    }
}

watch(
    () => [props.open, props.taskId],
    async ([isOpen]) => {
        if (!isOpen) return;

        loadError.value = null;
        await loadFormOptions();

        if (isEditing.value) {
            await loadTaskForEdit(props.taskId);
        } else {
            form.defaults({
                title: '', description: '', status: '', priority: '',
                assignee_ids: [], due_date: '', project_ids: [], tags: [],
            });
            form.reset();
            selectedAssignees.value = [];
            selectedProjects.value = [];
            selectedTags.value = [];
        }

        await nextTick();
        titleInput.value?.focus();
    },
);

// keep primitive id arrays in sync with the object-based multiselect models
watch(selectedAssignees, (val) => { form.assignee_ids = val.map((a) => a.id); }, { deep: true });
watch(selectedProjects, (val) => { form.project_ids = val.map((p) => p.id); }, { deep: true });
watch(selectedTags, (val) => { form.tags = val.map((t) => t.title); }, { deep: true });

function addTag(newTitle) {
    selectedTags.value.push({ title: newTitle });
}

function close() {
    if (form.processing) return;
    form.reset();
    form.clearErrors();
    loadError.value = null;
    emit('close');
}

function submit() {
    if (isEditing.value) {
        form.put(route('tasks.update', props.taskId), {
            preserveScroll: true,
            onSuccess: () => {
                emit('updated');
                emit('close');
            },
        });
    } else {
        form.post(route('tasks.store'), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                selectedAssignees.value = [];
                selectedProjects.value = [];
                selectedTags.value = [];
                emit('created');
                emit('close');
            },
        });
    }
}
</script>

<template>
    <Teleport to="body">
        <div
            v-if="open"
            class="flex fixed inset-0 z-50 justify-center items-center p-4 bg-black/40"
            @keydown.esc="close"
            @click.self="close"
        >
            <div class="flex max-h-[calc(100vh-2rem)] w-full max-w-2xl flex-col rounded-2xl bg-white shadow-2xl">
                <!-- Header -->
                <div class="flex justify-between items-center px-6 py-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">
                            {{ isEditing ? 'Edit Task' : 'Create New Task' }}
                        </h2>
                        <p class="mt-0.5 text-xs text-slate-500">
                            {{ isEditing ? 'Update the task details' : 'Add a new task to your project' }}
                        </p>
                    </div>
                    <button
                        type="button"
                        class="flex justify-center items-center w-8 h-8 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200"
                        aria-label="Close"
                        @click="close"
                    >
                        <X class="w-4 h-4" />
                    </button>
                </div>

                <!-- Body -->
                <form
                    v-if="!taskLoading && !loadError"
                    id="create-task-form"
                    class="overflow-y-auto flex-1 px-6 py-5 space-y-5"
                    @submit.prevent="submit"
                >
                    <div>
                        <label for="task-title" class="block mb-1.5 text-sm font-medium text-slate-700">Task Title</label>
                        <input
                            id="task-title"
                            ref="titleInput"
                            v-model="form.title"
                            type="text"
                            placeholder="e.g. Redesign the onboarding flow"
                            class="px-3.5 py-2.5 w-full text-sm rounded-2xl border border-slate-200 text-slate-900 placeholder:text-slate-400 focus:border-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-600/30"
                        />
                        <p v-if="form.errors.title" class="mt-1 text-xs text-red-600">{{ form.errors.title }}</p>
                    </div>

                    <div>
                        <label for="task-description" class="block mb-1.5 text-sm font-medium text-slate-700">Description</label>
                        <textarea
                            id="task-description"
                            v-model="form.description"
                            rows="3"
                            placeholder="Add a detailed description of this task..."
                            class="px-3.5 py-2.5 w-full text-sm rounded-2xl border resize-none border-slate-200 text-slate-900 placeholder:text-slate-400 focus:border-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-600/30"
                        />
                        <p v-if="form.errors.description" class="mt-1 text-xs text-red-600">{{ form.errors.description }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="task-status" class="block mb-1.5 text-sm font-medium text-slate-700">Status</label>
                            <select
                                id="task-status"
                                v-model="form.status"
                                class="px-3.5 py-2.5 w-full text-sm bg-white rounded-2xl border border-slate-200 text-slate-900 focus:border-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-600/30"
                            >
                                <option value="">Select status</option>
                                <option v-for="opt in statusOptions" :key="opt.value" :value="opt.value">
                                    {{ opt.label }}
                                </option>
                            </select>
                            <p v-if="form.errors.status" class="mt-1 text-xs text-red-600">{{ form.errors.status }}</p>
                        </div>

                        <div>
                            <label for="task-priority" class="block mb-1.5 text-sm font-medium text-slate-700">Priority</label>
                            <select
                                id="task-priority"
                                v-model="form.priority"
                                class="px-3.5 py-2.5 w-full text-sm bg-white rounded-2xl border border-slate-200 text-slate-900 focus:border-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-600/30"
                            >
                                <option value="">Select priority</option>
                                <option v-for="opt in priorityOptions" :key="opt.value" :value="opt.value">
                                    {{ opt.label }}
                                </option>
                            </select>
                            <p v-if="form.errors.priority" class="mt-1 text-xs text-red-600">{{ form.errors.priority }}</p>
                        </div>
                    </div>



                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-1.5 text-sm font-medium text-slate-700">Assignees</label>
                            <Multiselect
                                v-model="selectedAssignees"
                                :options="assignees"
                                label="name"
                                track-by="id"
                                placeholder="Search assignees..."
                                :loading="optionsLoading"
                                :multiple="true"
                                :close-on-select="false"
                                :searchable="true"
                            />
                            <p v-if="form.errors.assignee_ids" class="mt-1 text-xs text-red-600">{{ form.errors.assignee_ids }}</p>
                        </div>

                        <div>
                            <label for="task-due-date" class="block mb-1.5 text-sm font-medium text-slate-700">Due Date</label>
                            <input
                                id="task-due-date"
                                v-model="form.due_date"
                                type="date"
                                class="px-3.5 py-2.5 w-full text-sm rounded-2xl border border-slate-200 text-slate-900 focus:border-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-600/30"
                            />
                            <p v-if="form.errors.due_date" class="mt-1 text-xs text-red-600">{{ form.errors.due_date }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-1.5 text-sm font-medium text-slate-700">Projects</label>
                            <Multiselect
                                v-model="selectedProjects"
                                :options="projects"
                                label="title"
                                track-by="id"
                                placeholder="Search projects..."
                                :loading="optionsLoading"
                                :multiple="true"
                                :close-on-select="false"
                                :searchable="true"
                            />
                            <p v-if="form.errors.project_ids" class="mt-1 text-xs text-red-600">{{ form.errors.project_ids }}</p>
                        </div>

                        <div>
                            <label class="block mb-1.5 text-sm font-medium text-slate-700">Tags</label>
                            <Multiselect
                                v-model="selectedTags"
                                :options="tagOptions"
                                label="title"
                                track-by="title"
                                placeholder="Design, Frontend, Bug..."
                                :multiple="true"
                                :taggable="true"
                                tag-placeholder="Press enter to create tag"
                                :loading="optionsLoading"
                                @tag="addTag"
                            />
                            <p v-if="form.errors.tags" class="mt-1 text-xs text-red-600">{{ form.errors.tags }}</p>
                        </div>
                    </div>


                </form>

                <div
                    v-else-if="loadError"
                    class="flex flex-col flex-1 gap-3 justify-center items-center px-6 py-16 text-center"
                >
                    <p class="text-sm text-red-600">{{ loadError }}</p>
                    <button
                        type="button"
                        class="text-sm font-medium text-primary-600 hover:underline"
                        @click="loadTaskForEdit(taskId)"
                    >
                        Try again
                    </button>
                </div>

                <div v-else class="flex-1 px-6 py-16 text-sm text-center text-slate-400">
                    Loading task…
                </div>

                <!-- Footer -->
                <div class="flex justify-between items-center px-6 py-4 rounded-b-2xl border-t border-slate-100 bg-slate-50/50">
                    <button
                        type="button"
                        class="px-4 py-2 text-sm font-medium rounded-2xl text-slate-600 hover:bg-slate-100"
                        @click="close"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        form="create-task-form"
                        :disabled="form.processing || taskLoading || !!loadError"
                        class="flex gap-1.5 items-center px-5 py-2 text-sm font-medium text-white rounded-2xl shadow-sm bg-primary-600 shadow-primary-200 hover:bg-primary-700 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <component :is="isEditing ? Pencil : Plus" class="w-3.5 h-3.5" />
                        {{ form.processing ? (isEditing ? 'Saving…' : 'Creating…') : (isEditing ? 'Save Changes' : 'Create Task') }}
                    </button>
                </div>
            </div>
        </div>
    </Teleport>
</template>
