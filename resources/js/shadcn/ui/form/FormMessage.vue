<script lang="ts" setup>
import { ErrorMessage } from 'vee-validate';
import { toValue } from 'vue';
import { useFormField } from './useFormField';

const { name, formMessageId } = useFormField();

defineProps({
    serverError: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <ErrorMessage
        v-if="formMessageId"
        :id="formMessageId"
        as="p"
        :name="toValue(name)"
        class="text-sm font-medium text-red-600 dark:text-red-400"
    />
    <p
        v-if="serverError != ''"
        class="text-sm font-medium text-red-600 dark:text-red-400"
    >
        {{ serverError }}
    </p>
</template>
