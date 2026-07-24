<script setup>
import { ref, computed, watch, onMounted, nextTick } from 'vue';
import axios from 'axios';
import { usePage, Link } from '@inertiajs/vue3';
import { Search, ChevronLeft, ChevronRight, Pencil, Trash2, Calendar, Paperclip, Eye } from 'lucide-vue-next';
import StatusBadge from '@/Components/StatusBadge.vue';
import PriorityBadge from '@/Components/PriorityBadge.vue';
import EmptyState from '@/Components/EmptyState.vue';
import { confirmDelete, successAlert, errorAlert } from '@/swal';

const deletingId = ref(null);

const props = defineProps({
    dataUrl: { type: String, default: '/dashboard/tasks' },
    perPage: { type: Number, default: 8 },
    statusOptions: { type: Array, default: () => [] },
    priorityOptions: { type: Array, default: () => [] },
    projectOptions: { type: Array, default: () => [] },
    fixedProjectSlug: { type: String, default: null },
});

const AVATAR_PALETTE = [
    { bg: 'bg-violet-100', text: 'text-violet-700' },
    { bg: 'bg-blue-100', text: 'text-blue-700' },
    { bg: 'bg-emerald-100', text: 'text-emerald-700' },
    { bg: 'bg-rose-100', text: 'text-rose-700' },
    { bg: 'bg-amber-100', text: 'text-amber-700' },
];

const projectSelectOptions = computed(() => [
    { value: '', label: 'All projects' },
    ...props.projectOptions.map((option) => ({ value: option.value, label: option.label })),
]);

const statusSelectOptions = computed(() => [
    { value: '', label: 'All statuses' },
    ...props.statusOptions.map((option) => ({ value: option.value, label: option.label })),
]);

const prioritySelectOptions = computed(() => [
    { value: '', label: 'All priorities' },
    ...props.priorityOptions.map((option) => ({ value: option.value, label: option.label })),
]);

/**
 * Single source of truth for filter state: the URL query string.
 * `usePage().url` is Inertia's reactive current-URL — it updates whenever
 * an Inertia visit happens (e.g. a sidebar <Link> to ?project=3), even
 * though the underlying Dashboard/TaskTable component instance is reused.
 */
const inertiaPage = usePage();

function parseParams(url) {
    const qs = url.includes('?') ? url.split('?')[1] : '';
    return new URLSearchParams(qs);
}

const initialParams = parseParams(inertiaPage.url);

const loading = ref(true);
const tasks = ref([]);
const recordsFiltered = ref(0);

const search = ref(initialParams.get('search') ?? '');
const status = ref(initialParams.get('status') ?? '');
const priority = ref(initialParams.get('priority') ?? '');

const project = ref(
    props.fixedProjectSlug ? String(props.fixedProjectSlug) : (initialParams.get('project') ?? '')
);
const page = ref(Number(initialParams.get('page')) || 1);

let searchTimer = null;
let requestToken = 0;
let syncingFromUrl = false; // guards against double-fetching when the URL push originates externally (sidebar click)

const totalPages = computed(() => Math.max(1, Math.ceil(recordsFiltered.value / props.perPage)));
const rangeStart = computed(() => (recordsFiltered.value === 0 ? 0 : (page.value - 1) * props.perPage + 1));
const rangeEnd = computed(() => Math.min(page.value * props.perPage, recordsFiltered.value));
const emit = defineEmits(['edit']);

const visiblePages = computed(() => {
    const windowSize = 5;
    let start = Math.max(1, page.value - Math.floor(windowSize / 2));
    let end = Math.min(totalPages.value, start + windowSize - 1);
    start = Math.max(1, end - windowSize + 1);

    const pages = [];
    for (let p = start; p <= end; p++) pages.push(p);
    return pages;
});

function initials(name) {
    return (name ?? '')
        .split(' ')
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0]?.toUpperCase())
        .join('');
}

function avatarClasses(userId) {
    const palette = AVATAR_PALETTE[userId % AVATAR_PALETTE.length];
    return `${palette.bg} ${palette.text}`;
}


function syncUrl() {
    const params = new URLSearchParams();
    if (search.value) params.set('search', search.value);
    if (status.value) params.set('status', status.value);
    if (priority.value) params.set('priority', priority.value);
    if (project.value && !props.fixedProjectSlug) params.set('project', project.value);
    if (page.value > 1) params.set('page', String(page.value));

    const queryString = params.toString();
    const newUrl = `${window.location.pathname}${queryString ? `?${queryString}` : ''}`;
    const currentUrl = `${window.location.pathname}${window.location.search}`;

    if (newUrl !== currentUrl) {
        window.history.replaceState(window.history.state, '', newUrl);
    }

}

async function fetchTasks() {
    loading.value = true;
    const token = ++requestToken;

    try {
        const { data } = await axios.get(props.dataUrl, {
            params: {
                draw: token,
                start: (page.value - 1) * props.perPage,
                length: props.perPage,
                'search[value]': search.value,
                status: status.value,
                priority: priority.value,
                project: project.value,
            },
        });

        if (token !== requestToken) return;

        tasks.value = data.data;
        recordsFiltered.value = data.recordsFiltered;
    } catch (error) {
        console.error('Failed to load tasks', error);
        tasks.value = [];
        recordsFiltered.value = 0;
    } finally {
        if (token === requestToken) loading.value = false;
    }
}

async function confirmDeleteTask(task) {
    const result = await confirmDelete({
        title: 'Delete this task?',
        text: `"${task.title}" will be deleted.`,
    });

    if (!result.isConfirmed) return;

    deletingId.value = task.id;

    try {
        await axios.delete(route('tasks.destroy', task.id));

        const index = tasks.value.findIndex((t) => t.id === task.id);
        if (index !== -1) tasks.value.splice(index, 1);
        recordsFiltered.value = Math.max(0, recordsFiltered.value - 1);

        if (tasks.value.length === 0 && page.value > 1) {
            page.value -= 1;
        }

        successAlert('The task has been deleted.', 'Deleted');
    } catch (error) {
        console.error('Failed to delete task', error);
        errorAlert(error.response?.data?.message ?? 'Could not delete this task. Please try again.');
    } finally {
        deletingId.value = null;
    }
}

function goToPage(target) {
    if (target < 1 || target > totalPages.value || target === page.value) return;
    page.value = target;
}

// Filter → fetch + URL sync
watch(search, () => {
    if (syncingFromUrl) return;
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        page.value = 1;
        syncUrl();
        fetchTasks();
    }, 400);
});

watch([status, priority, project], () => {
    if (syncingFromUrl) return;
    page.value = 1;
    syncUrl();
    fetchTasks();
});

watch(page, () => {
    if (syncingFromUrl) return;
    syncUrl();
    fetchTasks();
});

// URL (Inertia visit, e.g. sidebar project click) → filters
watch(
    () => inertiaPage.url,
    (newUrl) => {
        const params = parseParams(newUrl);
        syncingFromUrl = true;
        search.value = params.get('search') ?? '';
        status.value = params.get('status') ?? '';
        priority.value = params.get('priority') ?? '';
        project.value = props.fixedProjectSlug ? String(props.fixedProjectSlug) : (params.get('project') ?? '');
        page.value = Number(params.get('page')) || 1;
        fetchTasks();
        nextTick(() => { syncingFromUrl = false; });
    },
);

onMounted(fetchTasks);
defineExpose({ reload: fetchTasks });
</script>

<template>
    <div class="bg-white rounded-2xl border border-slate-200">
        <!-- Toolbar -->
        <div class="flex flex-wrap gap-3 items-center p-4 border-b border-slate-100">
            <div class="relative w-full max-w-[384px] flex-1">
                <Search class="absolute left-3 top-1/2 w-3.5 h-3.5 -translate-y-1/2 pointer-events-none text-slate-400" />
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search tasks, IDs, tags..."
                    class="py-2 pr-4 pl-8 w-full text-sm rounded-2xl border-0 bg-slate-100 text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                />
            </div>

            <select
                v-if="!fixedProjectSlug"
                v-model="project"
                class="py-2 pr-8 pl-3 text-sm rounded-2xl border-0 bg-slate-100 text-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-200"
            >
                <option v-for="option in projectSelectOptions" :key="option.value" :value="option.value">
                    {{ option.label }}
                </option>
            </select>

            <select
                v-model="status"
                class="py-2 pr-8 pl-3 text-sm rounded-2xl border-0 bg-slate-100 text-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-200"
            >
                <option v-for="option in statusSelectOptions" :key="option.value" :value="option.value">
                    {{ option.label }}
                </option>
            </select>

            <select
                v-model="priority"
                class="py-2 pr-8 pl-3 text-sm rounded-2xl border-0 bg-slate-100 text-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-200"
            >
                <option v-for="option in prioritySelectOptions" :key="option.value" :value="option.value">
                    {{ option.label }}
                </option>
            </select>

            <span class="ml-auto text-xs text-slate-400">{{ recordsFiltered }} tasks</span>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50">
                        <th class="px-4 py-3 w-10">
                            <input type="checkbox" class="w-3.5 h-3.5 rounded border-slate-300" />
                        </th>
                        <th class="px-2 py-3 text-[12px] font-semibold uppercase tracking-wide text-slate-500">ID</th>
                        <th class="px-2 py-3 text-[12px] font-semibold uppercase tracking-wide text-slate-500">Task</th>
                        <th class="px-2 py-3 text-[12px] font-semibold uppercase tracking-wide text-slate-500">Projects</th>
                        <th class="px-2 py-3 text-[12px] font-semibold uppercase tracking-wide text-slate-500">Status</th>
                        <th class="px-2 py-3 text-[12px] font-semibold uppercase tracking-wide text-slate-500">Priority</th>
                        <th class="px-2 py-3 text-[12px] font-semibold uppercase tracking-wide text-slate-500">Assignee</th>
                        <th class="px-2 py-3 text-[12px] font-semibold uppercase tracking-wide text-slate-500">Due Date</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="loading">
                        <td colspan="9" class="px-4 py-10 text-sm text-center text-slate-400">
                            Loading tasks…
                        </td>
                    </tr>

                    <tr v-else-if="tasks.length === 0">
                        <td colspan="9" class="px-4 py-4">
                            <EmptyState title="No tasks found" description="Try adjusting your search or filters." />
                        </td>
                    </tr>

                    <template v-else>
                        <tr
                            v-for="task in tasks"
                            :key="task.id"
                            class="border-b border-slate-50 last:border-b-0 hover:bg-slate-50/60"
                        >
                            <td class="px-4 py-4 align-top">
                                <input type="checkbox" class="w-3.5 h-3.5 rounded border-slate-300" />
                            </td>

                            <td class="px-2 py-4 font-mono text-xs align-top text-slate-400">
                                {{ task.code }}
                            </td>

                            <td class="px-2 py-4 align-top">
                                <p class="text-sm font-medium text-slate-800">{{ task.title }}</p>
                                <div class="flex flex-wrap gap-2 items-center mt-1">
                                    <span
                                        v-for="tag in task.tag_list"
                                        :key="tag"
                                        class="rounded-[10px] bg-slate-100 px-1.5 py-0.5 text-xs text-slate-500"
                                    >
                                        {{ tag }}
                                    </span>
                                    <span
                                        v-if="task.attachments_count"
                                        class="inline-flex gap-1 items-center text-xs text-slate-400"
                                    >
                                        <Paperclip class="w-2.5 h-2.5" />
                                        {{ task.attachments_count }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-2 py-4 align-top">
                                <div v-if="task.project_list.length" class="flex flex-wrap gap-1.5">
                                    <span
                                        v-for="project in task.project_list"
                                        :key="project"
                                        class="rounded-[10px] bg-indigo-50 px-1.5 py-0.5 text-xs font-medium text-indigo-600"
                                    >
                                        {{ project }}
                                    </span>
                                </div>
                                <span v-else class="text-xs text-slate-400">—</span>
                            </td>

                            <td class="px-2 py-4 align-top">
                                <StatusBadge :status="task.status" />
                            </td>

                            <td class="px-2 py-4 align-top">
                                <PriorityBadge :priority="task.priority" />
                            </td>

                            <td class="px-2 py-4 align-top">
                                <div v-if="task.assignee_list.length" class="flex items-center">
                                    <span
                                        v-for="(assignee, idx) in task.assignee_list.slice(0, 3)"
                                        :key="assignee.id"
                                        class="flex h-6 w-6 items-center justify-center rounded-full text-[11px] font-semibold ring-2 ring-white"
                                        :class="[avatarClasses(assignee.id), idx > 0 ? '-ml-2' : '']"
                                        :title="assignee.name"
                                    >
                                        {{ initials(assignee.name) }}
                                    </span>
                                    <span
                                        v-if="task.assignee_list.length > 3"
                                        class="flex h-6 w-6 -ml-2 items-center justify-center rounded-full bg-slate-100 text-[10px] font-semibold text-slate-500 ring-2 ring-white"
                                    >
                                        +{{ task.assignee_list.length - 3 }}
                                    </span>
                                </div>
                                <span v-else class="text-xs text-slate-400">Unassigned</span>
                            </td>

                            <td class="px-2 py-4 align-top">
                                <span v-if="task.due_date" class="inline-flex gap-1.5 items-center text-xs text-slate-500">
                                    <Calendar class="w-2.5 h-2.5" />
                                    {{ task.due_date }}
                                </span>
                                <span v-else class="text-xs text-slate-400">—</span>
                            </td>

                            <td class="px-4 py-4 align-top">
                                <div class="flex gap-3 items-center">
                                    <Link
                                        :href="route('tasks.show', task.id)"
                                        class="text-slate-400 hover:text-slate-600"
                                        title="View task"
                                    >
                                        <Eye class="w-4 h-4" />
                                    </Link>
                                    <button
                                        type="button"
                                        class="text-blue-500 hover:text-blue-600"
                                        title="Edit task"
                                        @click="$emit('edit', task.id)"
                                    >
                                        <Pencil class="w-4 h-4" />
                                    </button>
                                    <button
                                        type="button"
                                        class="text-red-500 hover:text-red-600 disabled:cursor-not-allowed disabled:opacity-40"
                                        title="Delete task"
                                        :disabled="deletingId === task.id"
                                        @click="confirmDeleteTask(task)"
                                    >
                                        <Trash2 class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Footer / Pagination -->
        <div class="flex justify-between items-center px-4 py-3 rounded-b-2xl border-t border-slate-100 bg-slate-50/50">
            <p class="text-xs text-slate-500">
                Showing {{ rangeStart }}-{{ rangeEnd }} of {{ recordsFiltered }} tasks
            </p>

            <div class="flex gap-1 items-center">
                <button
                    type="button"
                    class="flex justify-center items-center w-7 h-7 rounded-xl text-slate-500 hover:bg-slate-100 disabled:opacity-40"
                    :disabled="page === 1"
                    @click="goToPage(page - 1)"
                >
                    <ChevronLeft class="w-3.5 h-3.5" />
                </button>

                <button
                    v-for="p in visiblePages"
                    :key="p"
                    type="button"
                    class="flex justify-center items-center w-7 h-7 text-xs font-medium rounded-xl"
                    :class="p === page ? 'bg-indigo-600 text-white' : 'text-slate-600 hover:bg-slate-100'"
                    @click="goToPage(p)"
                >
                    {{ p }}
                </button>

                <button
                    type="button"
                    class="flex justify-center items-center w-7 h-7 rounded-xl text-slate-500 hover:bg-slate-100 disabled:opacity-40"
                    :disabled="page === totalPages"
                    @click="goToPage(page + 1)"
                >
                    <ChevronRight class="w-3.5 h-3.5" />
                </button>
            </div>
        </div>
    </div>
</template>
