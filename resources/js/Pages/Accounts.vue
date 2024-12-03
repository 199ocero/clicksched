<script setup lang="ts">
import EmptyState from '@/Components/EmptyState.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/shadcn/ui/avatar';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/shadcn/ui/card';
import { faBluesky } from '@fortawesome/free-brands-svg-icons';

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

import { UserRoundX } from 'lucide-vue-next';

import { Head } from '@inertiajs/vue3';

import DisconnectModal from '@/Components/DisconnectModal.vue';
import BlueskyConnectModal from '@/Components/Platforms/Bluesky/ConnectModal.vue';

interface Account {
    id: number;
    name: string;
    handle: string;
    profile_image_url: string;
    sociable_id: number;
    sociable_type: string;
    platform: string;
}

defineProps<{
    accounts: Account[];
}>();

const disconnectAccount = (account: Account) => {
    console.log(account);
};
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
                        <BlueskyConnectModal />
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
                    <div
                        v-if="
                            accounts.some(
                                (account) => account.platform === 'bluesky',
                            )
                        "
                        class="grid grid-cols-1 divide-y"
                    >
                        <div
                            class="flex flex-col items-start justify-between gap-5 py-4 pb-2 md:flex-row md:items-start md:gap-20"
                        >
                            <div class="flex items-center gap-3">
                                <FontAwesomeIcon :icon="faBluesky" />
                                <span>Bluesky</span>
                            </div>
                            <div
                                class="flex flex-wrap justify-start gap-2 md:justify-end"
                            >
                                <div
                                    v-for="account in accounts.filter(
                                        (a) => a.platform === 'bluesky',
                                    )"
                                    :key="account.id"
                                    class="flex items-center gap-2 rounded-md border border-zinc-200 py-2 pl-3 pr-0.5 dark:border-zinc-800"
                                >
                                    <Avatar class="h-6 w-6">
                                        <AvatarImage
                                            :src="account.profile_image_url"
                                            :alt="account.handle"
                                        />
                                        <AvatarFallback>
                                            {{ account.handle.charAt(0) }}
                                        </AvatarFallback>
                                    </Avatar>
                                    <span class="text-sm">
                                        {{ account.handle }}
                                    </span>
                                    <DisconnectModal :account="account" />
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
