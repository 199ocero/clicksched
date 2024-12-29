<script setup lang="ts">
import { computed } from 'vue';

// Define props
const props = defineProps<{
    status:
        | 'draft'
        | 'queued'
        | 'running'
        | 'published'
        | 'scheduled'
        | 'failed';
}>();

// Computed property for dynamic classes
const badgeClasses = computed(() => {
    const status = props.status;

    return {
        'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100':
            status === 'draft',
        'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100':
            status === 'queued',
        'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100':
            status === 'running',
        'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100':
            status === 'published',
        'bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100':
            status === 'scheduled',
        'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100':
            status === 'failed',
    };
});

// Computed property for status label
const statusLabel = computed(() => {
    const labels: Record<string, string> = {
        draft: 'Draft',
        queued: 'Queued',
        running: 'Running',
        published: 'Published',
        scheduled: 'Scheduled',
        failed: 'Failed',
    };
    return labels[props.status] || 'Unknown';
});
</script>
<template>
    <div class="inline-flex">
        <span
            :class="badgeClasses"
            class="inline-flex items-center rounded-full px-3 py-0.5 text-xs font-medium capitalize"
        >
            {{ statusLabel }}
        </span>
    </div>
</template>
