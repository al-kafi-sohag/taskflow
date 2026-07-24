<script setup>
import { computed, ref, watch } from 'vue';
import axios from 'axios';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { errorAlert } from '@/swal';

const props = defineProps({
    open: { type: Boolean, default: false },
    projectSlug: { type: String, default: null },
    statusOptions: { type: Array, default: () => [] },
});

const emit = defineEmits(['close', 'created', 'updated']);

const isEditing = computed(() => props.projectSlug !== null);
const loadingProject = ref(false);

const form = useForm({
    title: '',
    description: '',
    status: '',
});

function resetForm() {
    form.reset();
    form.clearErrors();
    form.status = props.statusOptions[0]?.value ?? '';
}

async function loadProject() {
    loadingProject.value = true;
    try {
        const { data } = await axios.get(route('p.edit', props.projectSlug));
        form.title = data.title;
        form.description = data.description ?? '';
        form.status = data.status;
    } catch (error) {
        console.error('Failed to load project', error);
        errorAlert('Could not load this project. Please try again.');
        emit('close');
    } finally {
        loadingProject.value = false;
    }
}

watch(
    () => props.open,
    (isOpen) => {
        if (!isOpen) return;
        form.clearErrors();
        isEditing.value ? loadProject() : resetForm();
    },
);

function submit() {
    if (isEditing.value) {
        form.put(route('p.update', props.projectSlug), {
            preserveScroll: true,
            onSuccess: () => {
                emit('updated');
                close();
            },
        });
    } else {
        form.post(route('p.store'), {
            preserveScroll: true,
            onSuccess: () => {
                emit('created');
                close();
            },
        });
    }
}

function close() {
    form.reset();
    form.clearErrors();
    emit('close');
}
</script>

<template>
    <Modal :show="open" @close="close">
        <div class="p-6">
            <h2 class="text-base font-semibold text-slate-800">
                {{ isEditing ? 'Edit Project' : 'New Project' }}
            </h2>

            <div v-if="loadingProject" class="py-10 text-sm text-center text-slate-400">
                Loading project…
            </div>

            <form v-else class="mt-4 space-y-4" @submit.prevent="submit">
                <div>
                    <InputLabel for="title" value="Title" />
                    <TextInput id="title" v-model="form.title" type="text" class="block mt-1 w-full" autofocus />
                    <InputError :message="form.errors.title" class="mt-1" />
                </div>

                <div>
                    <InputLabel for="description" value="Description" />
                    <textarea
                        id="description"
                        v-model="form.description"
                        rows="3"
                        class="block mt-1 w-full text-sm rounded-2xl shadow-sm border-slate-300 focus:border-primary-500 focus:ring-primary-500"
                    />
                    <InputError :message="form.errors.description" class="mt-1" />
                </div>

                <div>
                    <InputLabel for="status" value="Status" />
                    <select
                        id="status"
                        v-model="form.status"
                        class="block mt-1 w-full text-sm rounded-2xl shadow-sm border-slate-300 focus:border-primary-500 focus:ring-primary-500"
                    >
                        <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                            {{ option.label }}
                        </option>
                    </select>
                    <InputError :message="form.errors.status" class="mt-1" />
                </div>

                <div class="flex gap-3 justify-end pt-2">
                    <SecondaryButton type="button" @click="close">Cancel</SecondaryButton>
                    <PrimaryButton type="submit" :disabled="form.processing">
                        {{ isEditing ? 'Save Changes' : 'Create Project' }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>
