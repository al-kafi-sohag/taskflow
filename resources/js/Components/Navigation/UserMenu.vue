<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const page = usePage();
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
    <Dropdown align="right" width="48">
        <template #trigger>
            <button type="button" class="flex h-8 w-8 items-center justify-center rounded-full bg-violet-100 text-xs font-semibold text-violet-700 ring-2 ring-white">
                {{ initials }}
            </button>
        </template>

        <template #content>
            <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
            <DropdownLink :href="route('logout')" method="post" as="button">
                Log Out
            </DropdownLink>
        </template>
    </Dropdown>
</template>
