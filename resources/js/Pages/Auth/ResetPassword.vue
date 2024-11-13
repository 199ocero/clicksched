<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    email: string;
    token: string;
}>();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Reset Password" />

        <div class="flex flex-col space-y-2 text-center">
            <h1 class="text-2xl font-semibold tracking-tight">
                Reset Password
            </h1>
            <p class="text-sm text-muted-foreground">
                Enter your new password to reset your account
            </p>
        </div>

        <form @submit.prevent="submit" class="mt-8 space-y-6">
            <div class="space-y-4">
                <div class="space-y-2">
                    <InputLabel for="email" value="Email" />

                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="john.doe@example"
                    />

                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="space-y-2">
                    <InputLabel for="password" value="Password" />

                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                        placeholder="Enter your password"
                    />

                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="space-y-2">
                    <InputLabel
                        for="password_confirmation"
                        value="Confirm Password"
                    />

                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="Confirm your password"
                    />

                    <InputError
                        class="mt-2"
                        :message="form.errors.password_confirmation"
                    />
                </div>
            </div>

            <div class="flex w-full flex-col space-y-6">
                <PrimaryButton class="w-full" :disabled="form.processing">
                    Reset Password
                </PrimaryButton>

                <p class="text-center text-sm text-muted-foreground">
                    Remember your password?
                    <Link
                        :href="route('login')"
                        class="text-primary hover:underline"
                    >
                        Back to login
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>
