<script setup lang="ts">
import { Input } from '@/shadcn/ui/input';
import { onMounted, ref } from 'vue';

const model = defineModel<string>({ required: true });

const input = ref<InstanceType<typeof Input> | null>(null);

onMounted(() => {
    if (input.value?.$el.hasAttribute('autofocus')) {
        input.value?.$el.focus();
    }
});

defineExpose({ focus: () => input.value?.$el.focus() });
</script>

<template>
    <Input v-model="model" ref="input" />
</template>
