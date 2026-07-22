<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import axios from 'axios';
import { Search, ChevronLeft, ChevronRight, Pencil, Trash2, Calendar, Paperclip } from 'lucide-vue-next';
import StatusBadge from '@/Components/StatusBadge.vue';
import PriorityBadge from '@/Components/PriorityBadge.vue';
import EmptyState from '@/Components/EmptyState.vue';

const props = defineProps({
    dataUrl: { type: String, default: '/dashboard/tasks' },
    perPage: { type: Number, default: 8 },
    statusOptions: { type: Array, default: () => [] }, // TaskStatus::options()
    priorityOptions: { type: Array, default: () => [] }, // TaskPriority::options()
});

const AVATAR_PALETTE = [
    { bg: 'bg-violet-100', text: 'text-violet-700' },
    { bg: 'bg-blue-100', text: 'text-blue-700' },
    { bg: 'bg-emerald-100', text: 'text-emerald-700' },
    { bg: 'bg-rose-100', text: 'text-rose-700' },
    { bg: 'bg-amber-100', text: 'text-amber-700' },
];

const statusSelectOptions = computed(() => [
    { value: '', label: 'All statuses' },
    ...props.statusOptions.map((option) => ({ value: option.value, label: option.label })),
]);

const prioritySelectOptions = computed(() => [
    { value: '', label: 'All priorities' },
    ...props.priorityOptions.map((option) => ({ value: option.value, label: option.label })),
]);

const loading = ref(true);
const tasks = ref([]);
const recordsFiltered = ref(0);

const search = ref('');
const status = ref('');
const priority = ref('');
const page = ref(1);

let searchTimer = null;
let requestToken = 0;

const totalPages = computed(() => Math.max(1, Math.ceil(recordsFiltered.value / props.perPage)));
const rangeStart = computed(() => (recordsFiltered.value === 0 ? 0 : (page.value - 1) * props.perPage + 1));
const rangeEnd = computed(() => Math.min(page.value * props.perPage, recordsFiltered.value));

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
            },
        });

        if (token !== requestToken) return; // a newer request already landed

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

function goToPage(target) {
    if (target < 1 || target > totalPages.value || target === page.value) return;
    page.value = target;
}

watch(search, () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        page.value = 1;
        fetchTasks();
    }, 400);
});

watch([status, priority], () => {
    page.value = 1;
    fetchTasks();
});

watch(page, fetchTasks);

onMounted(fetchTasks);
</script>

<template>
    <div class="rounded-2xl border border-slate-200 bg-white">
        <!-- Toolbar -->
        <div class="flex flex-wrap items-center gap-3 border-b border-slate-100 p-4">
            <div class="relative w-full max-w-[384px] flex-1">
                <Search class="pointer-events-none absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-400" />
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search tasks, IDs, tags..."
                    class="w-full rounded-2xl border-0 bg-slate-100 py-2 pl-8 pr-4 text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                />
            </div>

            <select
                v-model="status"
                class="rounded-2xl border-0 bg-slate-100 py-2 pl-3 pr-8 text-sm text-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-200"
            >
                <option v-for="option in statusSelectOptions" :key="option.value" :value="option.value">
                    {{ option.label }}
                </option>
            </select>

            <select
                v-model="priority"
                class="rounded-2xl border-0 bg-slate-100 py-2 pl-3 pr-8 text-sm text-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-200"
            >
                <option v-for="option in prioritySelectOptions" :key="option.value" :value="option.value">
                    {{ option.label }}
                </option>
            </select>

            <span class="ml-auto text-xs text-slate-400">{{ recordsFiltered }} tasks</span>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50">
                        <th class="w-10 px-4 py-3">
                            <input type="checkbox" class="h-3.5 w-3.5 rounded border-slate-300" />
                        </th>
                        <th class="px-2 py-3 text-[12px] font-semibold uppercase tracking-wide text-slate-500">ID</th>
                        <th class="px-2 py-3 text-[12px] font-semibold uppercase tracking-wide text-slate-500">Task</th>
                        <th class="px-2 py-3 text-[12px] font-semibold uppercase tracking-wide text-slate-500">Status</th>
                        <th class="px-2 py-3 text-[12px] font-semibold uppercase tracking-wide text-slate-500">Priority</th>
                        <th class="px-2 py-3 text-[12px] font-semibold uppercase tracking-wide text-slate-500">Assignee</th>
                        <th class="px-2 py-3 text-[12px] font-semibold uppercase tracking-wide text-slate-500">Due Date</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="loading">
                        <td colspan="8" class="px-4 py-10 text-center text-sm text-slate-400">
                            Loading tasks…
                        </td>
                    </tr>

                    <tr v-else-if="tasks.length === 0">
                        <td colspan="8" class="px-4 py-4">
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
                                <input type="checkbox" class="h-3.5 w-3.5 rounded border-slate-300" />
                            </td>

                            <td class="px-2 py-4 align-top font-mono text-xs text-slate-400">
                                {{ task.code }}
                            </td>

                            <td class="px-2 py-4 align-top">
                                <p class="text-sm font-medium text-slate-800">{{ task.title }}</p>
                                <div class="mt-1 flex flex-wrap items-center gap-2">
                                    <span
                                        v-for="tag in task.tag_list"
                                        :key="tag"
                                        class="rounded-[10px] bg-slate-100 px-1.5 py-0.5 text-xs text-slate-500"
                                    >
                                        {{ tag }}
                                    </span>
                                    <span
                                        v-if="task.attachments_count"
                                        class="inline-flex items-center gap-1 text-xs text-slate-400"
                                    >
                                        <Paperclip class="h-2.5 w-2.5" />
                                        {{ task.attachments_count }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-2 py-4 align-top">
                                <StatusBadge :status="task.status" />
                            </td>

                            <td class="px-2 py-4 align-top">
                                <PriorityBadge :priority="task.priority" />
                            </td>

                            <td class="px-2 py-4 align-top">
                                <div v-if="task.assignee_list.length" class="flex items-center gap-2">
                                    <span
                                        class="flex h-6 w-6 items-center justify-center rounded-full text-[11px] font-semibold"
                                        :class="avatarClasses(task.assignee_list[0].id)"
                                    >
                                        {{ initials(task.assignee_list[0].name) }}
                                    </span>
                                    <span class="text-xs font-medium text-slate-600">
                                        {{ task.assignee_list[0].name.split(' ')[0] }}
                                    </span>
                                </div>
                                <span v-else class="text-xs text-slate-400">Unassigned</span>
                            </td>

                            <td class="px-2 py-4 align-top">
                                <span v-if="task.due_date" class="inline-flex items-center gap-1.5 text-xs text-slate-500">
                                    <Calendar class="h-2.5 w-2.5" />
                                    {{ task.due_date }}
                                </span>
                                <span v-else class="text-xs text-slate-400">—</span>
                            </td>

                            <td class="px-4 py-4 align-top">
                                <div class="flex items-center gap-3">
                                    <button type="button" class="text-blue-500 hover:text-blue-600" title="Edit (coming soon)">
                                        <Pencil class="h-4 w-4" />
                                    </button>
                                    <button type="button" class="text-red-500 hover:text-red-600" title="Delete (coming soon)">
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Footer / Pagination -->
        <div class="flex items-center justify-between rounded-b-2xl border-t border-slate-100 bg-slate-50/50 px-4 py-3">
            <p class="text-xs text-slate-500">
                Showing {{ rangeStart }}-{{ rangeEnd }} of {{ recordsFiltered }} tasks
            </p>

            <div class="flex items-center gap-1">
                <button
                    type="button"
                    class="flex h-7 w-7 items-center justify-center rounded-xl text-slate-500 hover:bg-slate-100 disabled:opacity-40"
                    :disabled="page === 1"
                    @click="goToPage(page - 1)"
                >
                    <ChevronLeft class="h-3.5 w-3.5" />
                </button>

                <button
                    v-for="p in visiblePages"
                    :key="p"
                    type="button"
                    class="flex h-7 w-7 items-center justify-center rounded-xl text-xs font-medium"
                    :class="p === page ? 'bg-indigo-600 text-white' : 'text-slate-600 hover:bg-slate-100'"
                    @click="goToPage(p)"
                >
                    {{ p }}
                </button>

                <button
                    type="button"
                    class="flex h-7 w-7 items-center justify-center rounded-xl text-slate-500 hover:bg-slate-100 disabled:opacity-40"
                    :disabled="page === totalPages"
                    @click="goToPage(page + 1)"
                >
                    <ChevronRight class="h-3.5 w-3.5" />
                </button>
            </div>
        </div>
    </div>
</template>
