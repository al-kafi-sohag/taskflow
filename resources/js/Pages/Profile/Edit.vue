<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <AppLayout title="Profile" subtitle="Manage your account settings and preferences">
        <div class="mx-auto max-w-3xl space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-6">
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                    class="max-w-xl"
                />
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6">
                <UpdatePasswordForm class="max-w-xl" />
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6">
                <DeleteUserForm class="max-w-xl" />
            </div>
        </div>
    </AppLayout>
</template>
