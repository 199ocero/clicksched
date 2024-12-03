<script setup lang="ts">
import { Button } from '@/shadcn/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/shadcn/ui/dialog';
import { faX } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { router } from '@inertiajs/vue3';
import { Loader2 } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps<{
    account: {
        id: number;
        name: string;
        handle: string;
        profile_image_url: string;
        sociable_id: number;
        sociable_type: string;
        platform: string;
    };
}>();

const isLoading = ref(false);

const getPlatformName = (platform: string) => {
    switch (platform) {
        case 'bluesky':
            return 'Bluesky';
        default:
            return platform;
    }
};

const disconnectAccount = (account: any) => {
    isLoading.value = true;
    router.post(
        route('bluesky.destroy'),
        {
            account_id: account.id,
        },
        {
            onSuccess: () => {
                isOpen.value = false;
            },
            onFinish: () => {
                isLoading.value = false;
            },
            onError: () => {
                isLoading.value = false;
            },
        },
    );
};

const isOpen = ref(false);
</script>

<template>
    <Dialog v-model:open="isOpen">
        <DialogTrigger as-child>
            <Button variant="ghost" size="icon" class="h-6 w-6">
                <FontAwesomeIcon
                    :icon="faX"
                    class="text-red-600 dark:text-red-400"
                />
            </Button>
        </DialogTrigger>
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle
                    >Disconnect
                    {{ getPlatformName(account.platform) }} Account</DialogTitle
                >
                <DialogDescription>
                    By disconnecting this
                    {{ getPlatformName(account.platform) }} account, you'll no
                    longer be able to use its features in ClickSched. This
                    action can be reversed by reconnecting later.
                </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <DialogClose as-child>
                    <Button
                        type="button"
                        variant="outline"
                        :disabled="isLoading"
                    >
                        Keep Connected
                    </Button>
                </DialogClose>
                <Button
                    variant="destructive"
                    @click="disconnectAccount(account)"
                    :disabled="isLoading"
                >
                    <Loader2
                        v-if="isLoading"
                        class="mr-2 h-4 w-4 animate-spin"
                    />
                    {{ isLoading ? 'Disconnecting...' : 'Disconnect Account' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
