<script setup lang="ts">
import { Button } from '@/shadcn/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/shadcn/ui/dialog';
import {
    Drawer,
    DrawerClose,
    DrawerContent,
    DrawerDescription,
    DrawerFooter,
    DrawerHeader,
    DrawerTitle,
    DrawerTrigger,
} from '@/shadcn/ui/drawer';
import { FormItem } from '@/shadcn/ui/form';
import { Input } from '@/shadcn/ui/input';
import { Label } from '@/shadcn/ui/label';
import { faBluesky } from '@fortawesome/free-brands-svg-icons';
import { useForm, usePage } from '@inertiajs/vue3';
import { createReusableTemplate, useMediaQuery } from '@vueuse/core';
import { Loader2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

const [UseTemplate, GridForm] = createReusableTemplate();
const isDesktop = useMediaQuery('(min-width: 768px)');
const isOpen = ref(false);

const form = useForm({
    handle: '',
    app_password: '',
});

const onSubmit = () => {
    form.post(route('bluesky.store'), {
        preserveScroll: true,
        onSuccess: () => {
            const page = usePage();
            const flash = page.props.flash as Record<string, any>;
            if (!flash.error) {
                isOpen.value = false;
                form.reset();
            } else {
                isOpen.value = true;
            }
        },
    });
};

watch(isOpen, (newVal) => {
    if (!newVal) {
        form.reset();
        form.clearErrors();
    }
});
</script>

<template>
    <UseTemplate>
        <form
            :class="['grid gap-5', !isDesktop ? 'px-5' : '']"
            @submit.prevent="onSubmit"
        >
            <FormItem>
                <Label
                    :class="
                        form.errors.handle && 'text-red-600 dark:text-red-400'
                    "
                >
                    Handle
                    <span class="text-red-600 dark:text-red-400">*</span>
                </Label>
                <Input
                    type="text"
                    v-model="form.handle"
                    placeholder="e.g. example.bsky.social or example.com"
                    autofocus
                />
                <p class="text-sm text-muted-foreground">
                    Your Bluesky handle is your unique identifier for your
                    account. It's the part before the
                    <code class="font-semibold text-blue-600 dark:text-blue-400"
                        >.bsky.social</code
                    >
                    or your custom domain.
                </p>
                <p
                    v-if="form.errors.handle"
                    class="text-sm font-medium text-red-600 dark:text-red-400"
                >
                    {{ form.errors.handle }}
                </p>
            </FormItem>

            <FormItem>
                <Label
                    :class="
                        form.errors.app_password &&
                        'text-red-600 dark:text-red-400'
                    "
                >
                    App Password
                    <span class="text-red-600 dark:text-red-400">*</span>
                </Label>
                <Input
                    type="password"
                    v-model="form.app_password"
                    placeholder="Enter your app password"
                />
                <p class="text-sm text-muted-foreground">
                    Your app password is used to authenticate your account with
                    Bluesky. It's a unique password that you can create in your
                    Bluesky
                    <a
                        href="https://bsky.app/settings/app-passwords"
                        class="font-semibold text-blue-600 underline dark:text-blue-400"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        account settings </a
                    >. Rest assured, your app password will be securely stored
                    and encrypted.
                </p>
                <p
                    v-if="form.errors.app_password"
                    class="text-sm font-medium text-red-600 dark:text-red-400"
                >
                    {{ form.errors.app_password }}
                </p>
            </FormItem>

            <Button type="submit" :disabled="form.processing">
                <Loader2
                    v-if="form.processing"
                    class="mr-2 h-4 w-4 animate-spin"
                />
                {{ form.processing ? 'Submitting...' : 'Submit' }}
            </Button>
        </form>
    </UseTemplate>

    <Dialog v-if="isDesktop" v-model:open="isOpen">
        <DialogTrigger as-child>
            <Button variant="outline" class="flex items-center gap-2">
                <FontAwesomeIcon :icon="faBluesky" />
                Bluesky
            </Button>
        </DialogTrigger>
        <DialogContent class="sm:max-w-[600px]">
            <DialogHeader>
                <DialogTitle>Connect Bluesky Account</DialogTitle>
                <DialogDescription>
                    Enter your Bluesky handle and app password to connect your
                    account.
                </DialogDescription>
            </DialogHeader>
            <GridForm />
        </DialogContent>
    </Dialog>

    <Drawer v-else v-model:open="isOpen">
        <DrawerTrigger as-child>
            <Button variant="outline" class="flex items-center gap-2">
                <FontAwesomeIcon :icon="faBluesky" />
                Bluesky
            </Button>
        </DrawerTrigger>
        <DrawerContent>
            <DrawerHeader class="text-left">
                <DrawerTitle>Connect Bluesky Account</DrawerTitle>
                <DrawerDescription>
                    Enter your Bluesky handle and app password to connect your
                    account.
                </DrawerDescription>
            </DrawerHeader>
            <GridForm />
            <DrawerFooter class="pt-2">
                <DrawerClose as-child>
                    <Button variant="outline"> Cancel </Button>
                </DrawerClose>
            </DrawerFooter>
        </DrawerContent>
    </Drawer>
</template>
