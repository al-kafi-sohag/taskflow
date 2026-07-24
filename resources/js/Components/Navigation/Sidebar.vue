<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import SidebarLink from '@/Components/Navigation/SidebarLink.vue';
import SidebarProjectItem from '@/Components/Navigation/SidebarProjectItem.vue';
import {
    LayoutDashboard,
    CheckSquare,
    FolderKanban,
    User,
    Settings,
    PanelLeftClose,
    PanelLeft,
} from 'lucide-vue-next';

const props = defineProps({
    open: { type: Boolean, default: false },
    collapsed: { type: Boolean, default: false },
});

const emit = defineEmits(['close', 'toggle-collapse']);

const page = usePage();

const myTasksCount = computed(() => page.props.myTasksCount ?? 0);

const navItems = [
    { label: 'Dashboard', href: route('d.index'), active: () => route().current('d.index') && !activeProjectSlug.value, icon: LayoutDashboard },
    { label: 'My Tasks', href: route('mt.index'), active: () => route().current('mt.index'), icon: CheckSquare, badge: myTasksCount },
    { label: 'Projects', href: route('p.index'), active: () => route().current('p.index'), icon: FolderKanban },
    { label: 'Profile', href: route('profile.edit'), active: () => route().current('profile.edit'), icon: User },
    { label: 'Settings', href: '#', active: () => false, icon: Settings },
];

const DOT_PALETTE = ['bg-blue-500', 'bg-purple-500', 'bg-emerald-500', 'bg-red-500', 'bg-amber-500', 'bg-indigo-500'];

function dotClassFor(projectId) {
    return DOT_PALETTE[projectId % DOT_PALETTE.length];
}

const projects = computed(() => page.props.sidebarProjects ?? []);

const activeProjectSlug = computed(() => {
    const raw = new URLSearchParams(window.location.search).get('project');
    return raw || null;
});

function projectHref(project) {
    return route('p.show', project.slug);
}

function isActiveProject(project) {
    return route().current('p.show', { project: project.slug });
}

const authUser = computed(() => page.props.auth.user);

const initials = computed(() => {
    if (!authUser.value?.name) return '';
    return authUser.value.name.split(' ').map((n) => n[0]).slice(0, 2).join('').toUpperCase();
});
</script>

<template>
    <div v-show="open" class="fixed inset-0 z-30 bg-slate-900/30 lg:hidden" @click="emit('close')" />

    <aside
        class="flex fixed inset-y-0 left-0 z-40 flex-col w-60 border-r transition-all duration-200 ease-in-out transform -translate-x-full shrink-0 border-slate-200 bg-slate-50 lg:static lg:translate-x-0"
        :class="[open ? 'translate-x-0' : '', collapsed ? 'lg:w-[72px]' : 'lg:w-60']"
    >
        <div class="flex items-center px-4 h-16 border-b shrink-0 border-slate-200" :class="collapsed ? 'lg:justify-center lg:px-0' : 'justify-between'">
            <Link :href="route('d.index')" class="flex overflow-hidden gap-2.5 items-center">
                <span class="flex justify-center items-center w-8 h-8 rounded-2xl shadow-sm shrink-0 bg-primary-600 shadow-primary-100">
                    <ApplicationLogo class="w-4 h-4 text-white fill-current" />
                </span>
                <span class="text-base font-bold tracking-tight whitespace-nowrap text-slate-900" :class="{ 'lg:hidden': collapsed }">
                    TaskFlow
                </span>
            </Link>

            <button
                type="button"
                class="hidden justify-center items-center w-6 h-6 rounded-full shrink-0 bg-slate-200 text-slate-600 hover:bg-slate-300 lg:flex"
                :class="{ 'lg:absolute lg:right-2 lg:top-3': collapsed }"
                @click="emit('toggle-collapse')"
                :aria-label="collapsed ? 'Expand sidebar' : 'Collapse sidebar'"
            >
                <PanelLeft v-if="collapsed" class="w-3.5 h-3.5" />
                <PanelLeftClose v-else class="w-3.5 h-3.5" />
            </button>
        </div>

        <nav class="overflow-y-auto flex-1 px-2 py-3 space-y-1">
            <SidebarLink
                v-for="item in navItems"
                :key="item.label"
                :href="item.href"
                :active="item.active()"
                :badge="item.badge"
                :collapsed="collapsed"
            >
                <template #icon>
                    <component :is="item.icon" class="h-[18px] w-[18px]" />
                </template>
                {{ item.label }}
            </SidebarLink>

            <div class="pt-4" :class="{ 'lg:hidden': collapsed }">
                <p class="px-3 pb-2 text-xs font-semibold tracking-widest uppercase text-slate-400">
                    Projects
                </p>
                <div v-if="projects.length" class="space-y-1">
                    <SidebarProjectItem
                        v-for="project in projects"
                        :key="project.id"
                        :href="projectHref(project)"
                        :label="project.label"
                        :count="project.count"
                        :dot-class="dotClassFor(project.id)"
                        :active="isActiveProject(project)"
                    />
                </div>
                <p v-else class="px-3 text-xs text-slate-400">No active projects yet.</p>
            </div>
        </nav>

        <div class="p-3 border-t border-slate-200">
            <Link :href="route('profile.edit')" class="flex gap-3 items-center p-2 rounded-2xl hover:bg-slate-100" :class="{ 'lg:justify-center': collapsed }">
                <span class="flex justify-center items-center w-8 h-8 text-xs font-semibold text-violet-700 bg-violet-100 rounded-full ring-2 ring-white shrink-0">
                    {{ initials }}
                </span>
                <span class="overflow-hidden flex-1 min-w-0" :class="{ 'lg:hidden': collapsed }">
                    <span class="block text-sm font-semibold truncate text-slate-800">{{ authUser?.name }}</span>
                    <span class="block text-xs truncate text-slate-500">{{ authUser?.email }}</span>
                </span>
            </Link>
        </div>
    </aside>
</template>
