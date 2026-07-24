<script setup>
import Modal from '@/Components/Modal.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    open: { type: Boolean, default: false },
    project: { type: Object, default: null },
});

const emit = defineEmits(['close']);

function close() {
    emit('close');
}
</script>

<template>
    <Modal :show="open" @close="close">
        <div v-if="project" class="p-6">
            <h2 class="text-base font-semibold text-slate-800">Project Details</h2>
            <p class="mt-0.5 text-xs text-slate-500">{{ project.title }}</p>

            <dl class="mt-5 divide-y divide-slate-100">
                <div class="flex justify-between items-center py-3 first:pt-0">
                    <dt class="text-xs font-medium text-slate-400">ID</dt>
                    <dd class="text-sm font-medium text-slate-700">{{ project.id }}</dd>
                </div>
                <div class="flex justify-between items-center py-3">
                    <dt class="text-xs font-medium text-slate-400">Title</dt>
                    <dd class="text-sm font-medium text-slate-700">{{ project.title }}</dd>
                </div>
                <div class="flex justify-between items-center py-3">
                    <dt class="text-xs font-medium text-slate-400">Slug</dt>
                    <dd class="font-mono text-sm text-slate-700">{{ project.slug }}</dd>
                </div>
                <div class="flex justify-between items-center py-3">
                    <dt class="text-xs font-medium text-slate-400">Status</dt>
                    <dd><StatusBadge :status="project.status" /></dd>
                </div>
                <div class="flex justify-between items-center py-3">
                    <dt class="text-xs font-medium text-slate-400">Created At</dt>
                    <dd class="text-sm text-slate-700">{{ project.created_at ?? '—' }}</dd>
                </div>
                <div class="flex justify-between items-center py-3">
                    <dt class="text-xs font-medium text-slate-400">Created By</dt>
                    <dd class="text-sm text-slate-700">{{ project.created_by ?? '—' }}</dd>
                </div>
                <div class="flex justify-between items-center py-3">
                    <dt class="text-xs font-medium text-slate-400">Updated At</dt>
                    <dd class="text-sm text-slate-700">{{ project.updated_at ?? '—' }}</dd>
                </div>
                <div class="flex justify-between items-center py-3 last:pb-0">
                    <dt class="text-xs font-medium text-slate-400">Updated By</dt>
                    <dd class="text-sm text-slate-700">{{ project.updated_by ?? '—' }}</dd>
                </div>
            </dl>

            <div class="flex justify-end pt-5">
                <SecondaryButton type="button" @click="close">Close</SecondaryButton>
            </div>
        </div>
    </Modal>
</template>
