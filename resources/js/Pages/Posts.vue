<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Card } from '@/shadcn/ui/card';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Posts" />

    <AuthenticatedLayout>
        <Card>
            <div class="p-5">
                <p>Posts List</p>
            </div>
        </Card>
    </AuthenticatedLayout>
</template>
