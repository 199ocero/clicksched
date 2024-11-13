<script setup lang="ts">
import { Label } from '@/shadcn/ui/label';
defineProps<{
    value?: string;
}>();
</script>

<template>
    <Label>{{ value }}</Label>
</template>
