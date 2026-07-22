<script setup>
import { computed } from 'vue';
import { CircleDashed, RefreshCw, Eye, CircleCheck, XCircle } from 'lucide-vue-next';

const props = defineProps({
    // { value, name, label, badge: { bg, text, border } } — as returned
    // by TaskStatus::badge()/label() from the backend.
    status: { type: Object, required: true },
});

// Icon is a purely visual pairing keyed off the enum case name; colors
// and labels always come from the backend enum, not from here.
const ICON_MAP = {
    TO_DO: CircleDashed,
    IN_PROGRESS: RefreshCw,
    IN_REVIEW: Eye,
    DONE: CircleCheck,
    CANCELLED: XCircle,
};

const icon = computed(() => ICON_MAP[props.status.name] ?? CircleDashed);
</script>

<template>
    <span
        class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-[10px] border px-2 py-1 text-xs font-medium"
        :class="[status.badge.bg, status.badge.text, status.badge.border]"
    >
        <component :is="icon" class="h-3 w-3" />
        {{ status.label }}
    </span>
</template>
