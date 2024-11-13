<script setup lang="ts">
import { Checkbox } from '@/shadcn/ui/checkbox';
import { computed } from 'vue';

const emit = defineEmits(['update:checked']);

const props = defineProps<{
    checked: boolean;
    value?: any;
}>();

const proxyChecked = computed({
    get() {
        return props.checked;
    },

    set(val) {
        emit('update:checked', val);
    },
});
</script>

<template>
    <Checkbox :value="value" v-model="proxyChecked" />
</template>
