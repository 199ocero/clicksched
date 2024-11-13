<script setup lang="ts">
import { Button } from '@/shadcn/ui/button';
import { Icon } from '@iconify/vue';
import { useColorMode } from '@vueuse/core';

const mode = useColorMode();

const toggleTheme = () => {
    mode.value = mode.value === 'dark' ? 'light' : 'dark';
};
</script>

<template>
    <Button
        variant="ghost"
        size="icon"
        class="rounded-full"
        @click="toggleTheme"
    >
        <Icon
            icon="radix-icons:sun"
            v-if="mode === 'dark'"
            class="h-[1.2rem] w-[1.2rem] transition-all"
        />
        <Icon
            icon="radix-icons:moon"
            v-else
            class="h-[1.2rem] w-[1.2rem] transition-all"
        />
        <span class="sr-only">Toggle theme</span>
    </Button>
</template>
