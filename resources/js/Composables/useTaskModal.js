import { ref } from 'vue';

export function useTaskModal() {
    const showTaskModal = ref(false);
    const editingTaskId = ref(null);
    const taskTable = ref(null);

    function openCreate() {
        editingTaskId.value = null;
        showTaskModal.value = true;
    }

    function openEdit(taskId) {
        editingTaskId.value = taskId;
        showTaskModal.value = true;
    }

    function closeModal() {
        showTaskModal.value = false;
        editingTaskId.value = null;
    }

    function handleTaskSaved() {
        taskTable.value?.reload?.();
    }

    return {
        showTaskModal,
        editingTaskId,
        taskTable,
        openCreate,
        openEdit,
        closeModal,
        handleTaskSaved,
    };
}
