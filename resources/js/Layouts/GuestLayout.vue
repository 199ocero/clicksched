<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-white pt-6 sm:justify-center sm:pt-0 dark:bg-zinc-900"
    >
        <div>
            <Link href="/">
                <ApplicationLogo class="h-20 w-20 fill-current text-zinc-500" />
            </Link>
        </div>

        <div
            class="mt-6 w-full overflow-hidden bg-white p-10 sm:max-w-md sm:rounded-lg dark:bg-zinc-900"
        >
            <slot />
        </div>
    </div>
</template>
