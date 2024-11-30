<script lang="ts" setup>
import { cn } from '@/lib/utils';
import { Label } from '@/shadcn/ui/label';
import type { LabelProps } from 'radix-vue';
import type { HTMLAttributes } from 'vue';
import { useFormField } from './useFormField';

const props = defineProps<LabelProps & { class?: HTMLAttributes['class'] }>();

const { error, formItemId } = useFormField();
</script>

<template>
    <Label
        :class="cn(error && 'text-red-600 dark:text-red-400', props.class)"
        :for="formItemId"
    >
        <slot />
    </Label>
</template>
