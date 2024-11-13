<script setup lang="ts">
import { Button } from '@/shadcn/ui/button';
withDefaults(
    defineProps<{
        type?: 'button' | 'submit' | 'reset';
    }>(),
    {
        type: 'button',
    },
);
</script>

<template>
    <Button variant="outline" :type="type">
        <slot />
    </Button>
</template>
