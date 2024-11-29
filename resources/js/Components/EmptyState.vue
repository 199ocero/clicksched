<script setup lang="ts">
import { Card, CardDescription, CardHeader, CardTitle } from '@/shadcn/ui/card';

defineProps({
    icon: {
        type: [Object, Function],
        required: false,
        default: null,
    },
    title: {
        type: String,
        default: 'No items found',
    },
    description: {
        type: String,
        default: 'There are currently no items to display.',
    },
});
</script>

<template>
    <Card class="flex items-center justify-center p-6">
        <div class="text-center">
            <component v-if="icon" :is="icon" class="mx-auto mb-4 h-12 w-12" />
            <CardHeader class="p-0 pb-2">
                <CardTitle>{{ title }}</CardTitle>
                <CardDescription>{{ description }}</CardDescription>
            </CardHeader>
        </div>
    </Card>
</template>
