<script setup lang="ts">
import { Button } from '@/shadcn/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/shadcn/ui/card';
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
} from '@/shadcn/ui/drawer';
import { FormItem } from '@/shadcn/ui/form';
import { Input } from '@/shadcn/ui/input';
import { Label } from '@/shadcn/ui/label';
import { useForm, usePage } from '@inertiajs/vue3';
import { createReusableTemplate, useMediaQuery } from '@vueuse/core';
import { ref, watch } from 'vue';

const isOpen = ref(false);

const [UseTemplate, GridForm] = createReusableTemplate();
const isDesktop = useMediaQuery('(min-width: 768px)');

const form = useForm({
    password: '',
});

const onSubmit = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            const page = usePage();
            const flash = page.props.flash as Record<string, any>;
            if (!flash.error) {
                isOpen.value = false;
            } else {
                isOpen.value = true;
            }
        },
        onFinish: () => {
            form.reset();
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
    <Card>
        <CardHeader>
            <CardTitle>Delete Account</CardTitle>
            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                Once your account is deleted, all of its resources and data will
                be permanently deleted. Before deleting your account, please
                download any data or information that you wish to retain.
            </p>
        </CardHeader>

        <CardContent>
            <UseTemplate>
                <form
                    :class="['grid gap-5', !isDesktop ? 'px-5' : '']"
                    @submit.prevent="onSubmit"
                >
                    <FormItem>
                        <Label
                            :class="
                                form.errors.password &&
                                'text-red-600 dark:text-red-400'
                            "
                        >
                            Password
                            <span class="text-red-600 dark:text-red-400"
                                >*</span
                            >
                        </Label>
                        <Input
                            type="password"
                            v-model="form.password"
                            placeholder="Enter your password"
                            autofocus
                        />
                        <p
                            v-if="form.errors.password"
                            class="text-sm font-medium text-red-600 dark:text-red-400"
                        >
                            {{ form.errors.password }}
                        </p>
                    </FormItem>
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Submitting...' : 'Submit' }}
                    </Button>
                </form>
            </UseTemplate>
            <Dialog v-if="isDesktop" v-model:open="isOpen">
                <DialogTrigger asChild>
                    <Button variant="destructive">Delete Account</Button>
                </DialogTrigger>
                <DialogContent class="sm:max-w-[600px]">
                    <DialogHeader>
                        <DialogTitle
                            >Are you sure you want to delete your
                            account?</DialogTitle
                        >
                        <DialogDescription>
                            Once your account is deleted, all of its resources
                            and data will be permanently deleted. Please enter
                            your password to confirm you would like to
                            permanently delete your account.
                        </DialogDescription>
                    </DialogHeader>
                    <GridForm />
                </DialogContent>
            </Dialog>

            <Drawer v-else v-model:open="isOpen">
                <DialogTrigger asChild>
                    <Button variant="destructive">Delete Account</Button>
                </DialogTrigger>
                <DrawerContent>
                    <DrawerHeader class="text-left">
                        <DrawerTitle
                            >Are you sure you want to delete your
                            account?</DrawerTitle
                        >
                        <DrawerDescription>
                            Once your account is deleted, all of its resources
                            and data will be permanently deleted. Please enter
                            your password to confirm you would like to
                            permanently delete your account.
                        </DrawerDescription>
                    </DrawerHeader>
                    <GridForm />
                    <DrawerFooter class="pt-2">
                        <DrawerClose as-child>
                            <Button variant="outline">Cancel</Button>
                        </DrawerClose>
                    </DrawerFooter>
                </DrawerContent>
            </Drawer>
        </CardContent>
    </Card>
</template>
