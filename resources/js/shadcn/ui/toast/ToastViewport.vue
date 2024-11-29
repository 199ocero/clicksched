<script setup lang="ts">
import { cn } from '@/lib/utils';
import { ToastViewport, type ToastViewportProps } from 'radix-vue';
import { computed, type HTMLAttributes } from 'vue';

const props = defineProps<
    ToastViewportProps & { class?: HTMLAttributes['class'] }
>();

const delegatedProps = computed(() => {
    const { class: _, ...delegated } = props;

    return delegated;
});
</script>

<template>
    <ToastViewport
        v-bind="delegatedProps"
        :class="
            cn(
                'fixed right-0 top-0 z-[100] flex max-h-screen w-full max-w-md flex-col-reverse p-4',
                props.class,
            )
        "
    />
</template>
