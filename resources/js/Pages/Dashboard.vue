<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Card } from '@/shadcn/ui/card';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <Card>
            <div class="p-5">
                <p>
                    Welcome back
                    <span class="font-bold">{{
                        $page.props.auth.user.name
                    }}</span
                    >!
                </p>
            </div>
        </Card>
    </AuthenticatedLayout>
</template>
