<script setup lang="ts">
import BlueskyPreview from '@/Components/Previews/BlueskyPreview.vue';
import Tiptap from '@/Components/Tiptap.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/shadcn/ui/avatar';
import { Button } from '@/shadcn/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/shadcn/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/shadcn/ui/dialog';
import { Skeleton } from '@/shadcn/ui/skeleton';
import { Head, useForm } from '@inertiajs/vue3';
import { Check, Loader2 } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

import { SocialAccount, SocialAccounts } from '@/types/socialAccounts';

const props = defineProps<{
    accounts: SocialAccounts;
}>();

const tiptap = ref<InstanceType<typeof Tiptap> | null>(null);
const selectedAccountIds = ref<number[]>([]);
const selectedAccounts = ref<SocialAccount[]>([]);
const selectedPlatforms = ref<string[]>([]);
const isAccountsLoading = ref(props.accounts === null);

const form = useForm({
    social_account_ids: [] as number[],
    content: {} as object,
});

watch(
    selectedAccountIds,
    (newIds) => {
        form.social_account_ids = newIds;
    },
    { immediate: true },
);

const isContentEmpty = computed(() => {
    const content = form.content as {
        content?: { type: string; content?: any[] }[];
    };
    return (
        !content ||
        Object.keys(content).length === 0 ||
        !content.content ||
        content.content.length === 0 ||
        (content.content.length === 1 &&
            content.content[0].type === 'paragraph' &&
            (!content.content[0].content ||
                content.content[0].content.length === 0))
    );
});

watch(
    () => form.content,
    (newContent) => {
        if (!isContentEmpty.value) {
            form.clearErrors('content');
        }
    },
);

const toggleAccountSelection = (accountId: number, platformName: string) => {
    form.clearErrors('social_account_ids');
    const index = selectedAccountIds.value.indexOf(accountId);
    const account = props.accounts[platformName]?.find(
        (a) => a.id === accountId,
    );

    if (index > -1) {
        selectedAccountIds.value.splice(index, 1);

        const accountIndex = selectedAccounts.value.findIndex(
            (a) => a.id === accountId,
        );
        if (accountIndex > -1) {
            selectedAccounts.value.splice(accountIndex, 1);
        }

        const hasOtherAccounts = props.accounts[platformName]?.some((acc) =>
            selectedAccountIds.value.includes(acc.id),
        );

        if (!hasOtherAccounts) {
            const platformIndex = selectedPlatforms.value.indexOf(platformName);
            if (platformIndex > -1) {
                selectedPlatforms.value.splice(platformIndex, 1);
            }
        }
    } else {
        if (account) {
            selectedAccountIds.value.push(accountId);
            selectedAccounts.value.push(account);

            if (!selectedPlatforms.value.includes(platformName)) {
                selectedPlatforms.value.push(platformName);
            }
        }
    }
};

const getHtmlContent = computed(() => {
    if (tiptap.value) {
        return tiptap.value.getHtml();
    }
});

const isAccountSelected = (accountId: number) => {
    return selectedAccountIds.value.includes(accountId);
};

const onSubmit = () => {
    if (form.social_account_ids.length === 0) {
        form.setError(
            'social_account_ids',
            "Oops! You haven't selected any social media accounts to share your post. Please choose at least one platform to reach your audience.",
        );
        return;
    }

    if (isContentEmpty.value) {
        form.setError(
            'content',
            'Your post seems a bit empty. Share your thoughts, ideas, or update by adding some content before posting.',
        );
        return;
    }

    form.post(route('new-post.store'), {
        preserveScroll: true,
        onSuccess: () => {
            if (tiptap.value) {
                tiptap.value.clearContent();
            }
            selectedAccountIds.value = [];
            selectedAccounts.value = [];
            selectedPlatforms.value = [];
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="New Post" />

    <AuthenticatedLayout>
        <form
            @submit.prevent="onSubmit"
            class="flex flex-grow flex-col gap-2 overflow-y-auto lg:flex-row"
        >
            <Card class="order-2 w-full lg:order-1 lg:w-1/5">
                <CardHeader>
                    <CardTitle>
                        Accounts: {{ selectedAccountIds.length }}
                        <p
                            v-if="form.errors.social_account_ids"
                            class="mt-2 text-sm font-medium text-red-600 dark:text-red-400"
                        >
                            {{ form.errors.social_account_ids }}
                        </p>
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div v-if="isAccountsLoading" class="flex gap-1">
                        <Skeleton
                            class="h-10 w-10 flex-shrink-0 rounded-full"
                        />
                        <Skeleton
                            class="h-10 w-10 flex-shrink-0 rounded-full"
                        />
                        <Skeleton class="h-10 w-full rounded-full" />
                    </div>
                    <div
                        v-else-if="accounts"
                        v-for="(platformAccounts, platformName) in accounts"
                        :key="platformName"
                    >
                        <div class="grid gap-2">
                            <h3 class="text-sm font-semibold">
                                {{ platformAccounts[0]?.platform_title }}
                            </h3>

                            <div class="flex flex-wrap gap-1">
                                <div
                                    v-for="account in platformAccounts"
                                    :key="account.id"
                                >
                                    <div
                                        class="relative inline-block cursor-pointer"
                                        @click="
                                            toggleAccountSelection(
                                                account.id,
                                                String(platformName),
                                            )
                                        "
                                    >
                                        <Avatar class="h-8 w-8 rounded-full">
                                            <AvatarImage
                                                :src="account.profile_image_url"
                                                :alt="account.name"
                                            />
                                            <AvatarFallback>
                                                {{
                                                    account.name
                                                        .charAt(0)
                                                        .toUpperCase()
                                                }}
                                            </AvatarFallback>
                                        </Avatar>

                                        <div
                                            v-if="isAccountSelected(account.id)"
                                            class="absolute bottom-0 right-0 rounded-full bg-blue-600 p-1 dark:bg-blue-500"
                                        >
                                            <Check class="h-2 w-2 text-white" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
            <div class="order-1 flex w-full gap-2 lg:order-2">
                <Card class="w-full lg:w-1/2">
                    <CardHeader>
                        <CardTitle>Create Your Multi-Platform Post</CardTitle>
                        <CardDescription
                            >Craft a post that will resonate across your
                            connected social networks</CardDescription
                        >
                    </CardHeader>
                    <CardContent>
                        <input
                            type="hidden"
                            v-model="form.social_account_ids"
                        />
                        <Tiptap
                            ref="tiptap"
                            v-model="form.content"
                            :selectedPlatforms="selectedPlatforms"
                            placeholder="Share your thoughts, updates, or ideas 🤯..."
                        />
                        <p
                            v-if="form.errors.content"
                            class="text-sm font-medium text-red-600 dark:text-red-400"
                        >
                            {{ form.errors.content }}
                        </p>
                    </CardContent>
                    <CardFooter class="flex justify-end gap-2">
                        <Dialog>
                            <DialogTrigger as-child>
                                <Button
                                    variant="outline"
                                    type="button"
                                    class="block lg:hidden"
                                >
                                    View Post
                                </Button>
                            </DialogTrigger>
                            <DialogContent
                                class="max-h-[90dvh] grid-rows-[auto_minmax(0,1fr)_auto] p-0 sm:max-w-[600px]"
                            >
                                <DialogHeader class="p-6 pb-0">
                                    <DialogTitle
                                        >Cross-Platform Post
                                        Preview</DialogTitle
                                    >
                                    <DialogDescription>
                                        Verify your content across selected
                                        social media platforms before publishing
                                    </DialogDescription>
                                </DialogHeader>
                                <div
                                    class="flex flex-col gap-2 overflow-y-auto px-6 py-4"
                                >
                                    <template
                                        v-for="platform in selectedPlatforms"
                                        :key="platform"
                                    >
                                        <BlueskyPreview
                                            v-if="platform === 'bluesky'"
                                            :selectedAccounts="selectedAccounts"
                                            :htmlContent="
                                                String(getHtmlContent)
                                            "
                                        />
                                    </template>
                                    <template
                                        v-if="selectedPlatforms.length === 0"
                                    >
                                        <div class="flex flex-col gap-2">
                                            <div class="flex flex-row gap-1">
                                                <Skeleton
                                                    class="h-16 w-16 flex-shrink-0 rounded-full"
                                                />
                                                <div
                                                    class="flex w-full flex-col gap-2"
                                                >
                                                    <Skeleton
                                                        class="h-8 w-full rounded-full"
                                                    />
                                                    <Skeleton
                                                        class="h-4 w-1/2 rounded-full"
                                                    />
                                                </div>
                                            </div>
                                            <Skeleton
                                                class="h-60 w-full rounded-md"
                                            />
                                        </div>
                                    </template>
                                </div>
                            </DialogContent>
                        </Dialog>
                        <Button type="submit" :disabled="form.processing">
                            <Loader2
                                v-if="form.processing"
                                class="mr-2 h-4 w-4 animate-spin"
                            />
                            {{ form.processing ? 'Posting...' : 'Post Now' }}
                        </Button>
                    </CardFooter>
                </Card>
                <Card class="lg:max-w-1/2 hidden lg:block lg:w-1/2">
                    <CardHeader>
                        <CardTitle>Cross-Platform Post Preview</CardTitle>
                        <CardDescription>
                            Verify your content across selected social media
                            platforms before publishing
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div
                            class="flex max-h-[calc(100vh-250px)] flex-grow flex-col gap-2 overflow-y-auto p-2"
                        >
                            <template
                                v-for="platform in selectedPlatforms"
                                :key="platform"
                            >
                                <BlueskyPreview
                                    v-if="platform === 'bluesky'"
                                    :selectedAccounts="selectedAccounts"
                                    :htmlContent="String(getHtmlContent)"
                                />
                            </template>
                            <template v-if="selectedPlatforms.length === 0">
                                <div class="flex flex-col gap-2">
                                    <div class="flex flex-row gap-1">
                                        <Skeleton
                                            class="h-16 w-16 flex-shrink-0 rounded-full"
                                        />
                                        <div class="flex w-full flex-col gap-2">
                                            <Skeleton
                                                class="h-8 w-full rounded-full"
                                            />
                                            <Skeleton
                                                class="h-4 w-1/2 rounded-full"
                                            />
                                        </div>
                                    </div>
                                    <Skeleton class="h-60 w-full rounded-md" />
                                </div>
                            </template>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
