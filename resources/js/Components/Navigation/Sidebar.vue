<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import SidebarLink from '@/Components/Navigation/SidebarLink.vue';
import SidebarProjectItem from '@/Components/Navigation/SidebarProjectItem.vue';
import {
    LayoutDashboard,
    CheckSquare,
    User,
    Settings,
    PanelLeftClose,
    PanelLeft,
} from 'lucide-vue-next';

const props = defineProps({
    open: { type: Boolean, default: false },       // mobile drawer
    collapsed: { type: Boolean, default: false },  // desktop icon-only rail
});

const emit = defineEmits(['close', 'toggle-collapse']);

const page = usePage();

const navItems = [
    {
        label: 'Dashboard',
        href: route('d.index'),
        active: () => route().current('d.index'),
        icon: LayoutDashboard,
    },
    {
        label: 'My Tasks',
        href: '#', // TODO: route('tasks.index')
        active: () => route().current('tasks.*'),
        icon: CheckSquare,
        badge: 4,
    },
    {
        label: 'Profile',
        href: route('profile.edit'),
        active: () => route().current('profile.edit'),
        icon: User,
    },
    {
        label: 'Settings',
        href: '#', // TODO: no settings route yet
        active: () => false,
        icon: Settings,
    },
];

const projects = [
    { label: 'Platform', count: 12, dotClass: 'bg-blue-500' },
    { label: 'Growth', count: 8, dotClass: 'bg-purple-500' },
    { label: 'Infrastructure', count: 5, dotClass: 'bg-emerald-500' },
    { label: 'Design', count: 3, dotClass: 'bg-red-500' },
];

const authUser = computed(() => page.props.auth.user);

const initials = computed(() => {
    if (!authUser.value?.name) return '';
    return authUser.value.name
        .split(' ')
        .map((n) => n[0])
        .slice(0, 2)
        .join('')
        .toUpperCase();
});
</script>

<template>
    <!-- Mobile overlay -->
    <div
        v-show="open"
        class="fixed inset-0 z-30 bg-slate-900/30 lg:hidden"
        @click="emit('close')"
    />

    <aside
        class="fixed inset-y-0 left-0 z-40 flex w-60 shrink-0 -translate-x-full transform flex-col border-r border-slate-200 bg-slate-50 transition-all duration-200 ease-in-out lg:static lg:translate-x-0"
        :class="[open ? 'translate-x-0' : '', collapsed ? 'lg:w-[72px]' : 'lg:w-60']"
    >
        <!-- Logo -->
        <div
            class="flex h-16 shrink-0 items-center border-b border-slate-200 px-4"
            :class="collapsed ? 'lg:justify-center lg:px-0' : 'justify-between'"
        >
            <Link :href="route('d.index')" class="flex items-center gap-2.5 overflow-hidden">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-2xl bg-primary-600 shadow-sm shadow-primary-100">
                    <ApplicationLogo class="h-4 w-4 fill-current text-white" />
                </span>
                <span
                    class="whitespace-nowrap text-base font-bold tracking-tight text-slate-900"
                    :class="{ 'lg:hidden': collapsed }"
                >
                    TaskFlow
                </span>
            </Link>

            <button
                type="button"
                class="hidden h-6 w-6 shrink-0 items-center justify-center rounded-full bg-slate-200 text-slate-600 hover:bg-slate-300 lg:flex"
                :class="{ 'lg:absolute lg:right-2 lg:top-3': collapsed }"
                @click="emit('toggle-collapse')"
                :aria-label="collapsed ? 'Expand sidebar' : 'Collapse sidebar'"
            >
                <PanelLeft v-if="collapsed" class="h-3.5 w-3.5" />
                <PanelLeftClose v-else class="h-3.5 w-3.5" />
            </button>
        </div>

        <!-- Primary nav -->
        <nav class="flex-1 space-y-1 overflow-y-auto px-2 py-3">
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
                <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-widest text-slate-400">
                    Projects
                </p>
                <div class="space-y-1">
                    <SidebarProjectItem
                        v-for="project in projects"
                        :key="project.label"
                        :label="project.label"
                        :count="project.count"
                        :dot-class="project.dotClass"
                    />
                </div>
            </div>
        </nav>

        <!-- User footer -->
        <div class="border-t border-slate-200 p-3">
            <Link
                :href="route('profile.edit')"
                class="flex items-center gap-3 rounded-2xl p-2 hover:bg-slate-100"
                :class="{ 'lg:justify-center': collapsed }"
            >
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-violet-100 text-xs font-semibold text-violet-700 ring-2 ring-white">
                    {{ initials }}
                </span>
                <span class="min-w-0 flex-1 overflow-hidden" :class="{ 'lg:hidden': collapsed }">
                    <span class="block truncate text-sm font-semibold text-slate-800">
                        {{ authUser?.name }}
                    </span>
                    <span class="block truncate text-xs text-slate-500">
                        {{ authUser?.email }}
                    </span>
                </span>
            </Link>
        </div>
    </aside>
</template>
