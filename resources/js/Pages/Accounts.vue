<script setup lang="ts">
import EmptyState from '@/Components/EmptyState.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Button } from '@/shadcn/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/shadcn/ui/card';
import {
    faBluesky,
    faFacebook,
    faInstagram,
    faLinkedin,
    faXTwitter,
} from '@fortawesome/free-brands-svg-icons';
import { faX } from '@fortawesome/free-solid-svg-icons';

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

import { UserRoundX } from 'lucide-vue-next';

import { Head } from '@inertiajs/vue3';

defineProps({
    accounts: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <Head title="Accounts" />

    <AuthenticatedLayout>
        <div class="space-y-8">
            <Card>
                <CardHeader>
                    <CardTitle>Connect Accounts</CardTitle>
                    <CardDescription>
                        Connect your social media accounts to enable
                        cross-platform features
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div
                        class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4"
                    >
                        <Button
                            variant="outline"
                            class="flex items-center gap-2"
                        >
                            <FontAwesomeIcon :icon="faBluesky" />
                            Bluesky
                        </Button>

                        <Button
                            variant="outline"
                            class="flex items-center gap-2"
                            disabled
                        >
                            <FontAwesomeIcon :icon="faFacebook" />
                            Facebook
                        </Button>

                        <Button
                            variant="outline"
                            class="flex items-center gap-2"
                            disabled
                        >
                            <FontAwesomeIcon :icon="faXTwitter" />
                            X (Twitter)
                        </Button>

                        <Button
                            variant="outline"
                            class="flex items-center gap-2"
                            disabled
                        >
                            <FontAwesomeIcon :icon="faInstagram" />
                            Instagram
                        </Button>

                        <Button
                            variant="outline"
                            class="flex items-center gap-2"
                            disabled
                        >
                            <FontAwesomeIcon :icon="faLinkedin" />
                            LinkedIn
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <Card v-if="accounts.length > 0">
                <CardHeader>
                    <CardTitle>Connected Accounts</CardTitle>
                    <CardDescription>
                        List of your currently connected social media accounts
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-1 divide-y">
                        <div
                            class="flex flex-col items-start justify-between gap-5 py-4 pb-2 md:flex-row md:items-start md:gap-20"
                        >
                            <div class="flex items-center gap-3">
                                <FontAwesomeIcon :icon="faFacebook" />
                                <span>Facebook</span>
                            </div>
                            <div
                                class="flex flex-wrap justify-start gap-2 md:justify-end"
                            >
                                <div
                                    v-for="i in 10"
                                    :key="i"
                                    class="flex cursor-pointer items-center gap-2 rounded-md border border-zinc-200 py-0.5 pl-3 pr-0.5 dark:border-zinc-800"
                                >
                                    <span class="text-sm"
                                        >John Doe {{ i }}</span
                                    >
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="h-6 w-6"
                                    >
                                        <FontAwesomeIcon :icon="faX" />
                                    </Button>
                                </div>
                            </div>
                        </div>
                        <div
                            class="flex flex-col items-start justify-between gap-5 py-4 pb-2 md:flex-row md:items-start md:gap-20"
                        >
                            <div class="flex items-center gap-3">
                                <FontAwesomeIcon :icon="faXTwitter" />
                                <span>X (Twitter)</span>
                            </div>
                            <div
                                class="flex flex-wrap justify-start gap-2 md:justify-end"
                            >
                                <div
                                    v-for="i in 5"
                                    :key="i"
                                    class="flex cursor-pointer items-center gap-2 rounded-md border border-zinc-200 py-0.5 pl-3 pr-0.5 dark:border-zinc-800"
                                >
                                    <span class="text-sm"
                                        >John Doe {{ i }}</span
                                    >
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="h-6 w-6"
                                    >
                                        <FontAwesomeIcon :icon="faX" />
                                    </Button>
                                </div>
                            </div>
                        </div>
                        <div
                            class="flex flex-col items-start justify-between gap-5 py-4 pb-2 md:flex-row md:items-start md:gap-20"
                        >
                            <div class="flex items-center gap-3">
                                <FontAwesomeIcon :icon="faInstagram" />
                                <span>Instagram</span>
                            </div>
                            <div
                                class="flex flex-wrap justify-start gap-2 md:justify-end"
                            >
                                <div
                                    v-for="i in 1"
                                    :key="i"
                                    class="flex cursor-pointer items-center gap-2 rounded-md border border-zinc-200 py-0.5 pl-3 pr-0.5 dark:border-zinc-800"
                                >
                                    <span class="text-sm"
                                        >John Doe {{ i }}</span
                                    >
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="h-6 w-6"
                                    >
                                        <FontAwesomeIcon :icon="faX" />
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
            <template v-else>
                <EmptyState
                    :icon="UserRoundX"
                    title="No connected accounts"
                    description="Select a platform to connect your account"
                />
            </template>
        </div>
    </AuthenticatedLayout>
</template>
