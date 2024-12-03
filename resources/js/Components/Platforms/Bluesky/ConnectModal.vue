<script lang="ts" setup>
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
import {
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/shadcn/ui/form';
import { Input } from '@/shadcn/ui/input';
import { faBluesky } from '@fortawesome/free-brands-svg-icons';
import { router, usePage } from '@inertiajs/vue3';
import { createReusableTemplate, useMediaQuery } from '@vueuse/core';
import { Loader2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

import { toTypedSchema } from '@vee-validate/zod';
import { useForm } from 'vee-validate';
import * as z from 'zod';

import { vAutoAnimate } from '@formkit/auto-animate/vue';

const domainRegex = /^[A-Za-z0-9-]{1,63}\.[A-Za-z]{2,6}$/;
const blueskyHandleRegex = /^[a-zA-Z0-9.-]+\.bsky\.social$/;

const formSchema = toTypedSchema(
    z.object({
        handle: z
            .string()
            .min(1, 'Handle is required')
            .refine(
                (value) =>
                    domainRegex.test(value) || blueskyHandleRegex.test(value),
                {
                    message:
                        'The handle must be a valid Bluesky handle (e.g., example.bsky.social) or a valid domain (e.g., example.com)',
                },
            ),
        app_password: z.string().min(8, {
            message: 'App password must be at least 8 characters long',
        }),
    }),
);

const isLoading = ref(false);
const { handleSubmit, meta, errors, values } = useForm({
    validationSchema: formSchema,
});

const isSubmitDisabled = computed(() => {
    return !meta.value.valid || isLoading.value;
});

const onSubmit = handleSubmit((values) => {
    const page = usePage();
    isLoading.value = true;
    router.post(route('bluesky.store'), values, {
        onSuccess: () => {
            const flash = page.props.flash as Record<string, any>;
            if (!flash.error) {
                isOpen.value = false;
            } else {
                isOpen.value = true;
            }
        },
        onFinish: () => {
            isLoading.value = false;
        },
        onError: () => {
            isLoading.value = false;
        },
    });
});

const [UseTemplate, GridForm] = createReusableTemplate();
const isDesktop = useMediaQuery('(min-width: 768px)');

const isOpen = ref(false);
</script>

<template>
    <UseTemplate>
        <form
            :class="['grid gap-5', !isDesktop ? 'px-5' : '']"
            @submit="onSubmit"
        >
            <FormField v-slot="{ componentField }" name="handle">
                <FormItem v-auto-animate>
                    <FormLabel
                        :hasServerError="
                            $page.props.errors &&
                            $page.props.errors.handle != null
                        "
                    >
                        Handle
                        <span class="text-red-600 dark:text-red-400">*</span>
                    </FormLabel>
                    <FormControl>
                        <Input
                            type="text"
                            placeholder="e.g. example.bsky.social or example.com"
                            autofocus
                            v-bind="componentField"
                        />
                    </FormControl>
                    <FormDescription>
                        Your Bluesky handle is your unique identifier for your
                        account. It's the part before the
                        <code
                            class="font-semibold text-blue-600 dark:text-blue-400"
                            >.bsky.social</code
                        >
                        or your custom domain.
                    </FormDescription>
                    <FormMessage :serverError="$page.props.errors.handle" />
                </FormItem>
            </FormField>
            <FormField v-slot="{ componentField }" name="app_password">
                <FormItem v-auto-animate>
                    <FormLabel
                        :hasServerError="
                            $page.props.errors &&
                            $page.props.errors.app_password != null
                        "
                        >App Password
                        <span class="text-red-600 dark:text-red-400"
                            >*</span
                        ></FormLabel
                    >
                    <FormControl>
                        <Input
                            type="password"
                            placeholder="Enter your app password"
                            v-bind="componentField"
                        />
                    </FormControl>
                    <FormDescription>
                        Your app password is used to authenticate your account
                        with Bluesky. It's a unique password that you can create
                        in your Bluesky
                        <a
                            href="https://bsky.app/settings/app-passwords"
                            class="font-semibold text-blue-600 underline dark:text-blue-400"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            account settings </a
                        >. Rest assured, your app password will be securely
                        stored and encrypted.
                    </FormDescription>
                    <FormMessage
                        :serverError="$page.props.errors.app_password"
                    />
                </FormItem>
            </FormField>
            <Button type="submit" :disabled="isSubmitDisabled">
                <Loader2 v-if="isLoading" class="mr-2 h-4 w-4 animate-spin" />
                {{ isLoading ? 'Submitting...' : 'Submit' }}
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
