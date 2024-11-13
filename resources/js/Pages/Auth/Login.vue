<script setup lang="ts">
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="flex flex-col space-y-2 text-center">
            <h1 class="text-2xl font-semibold tracking-tight">Welcome back</h1>
            <p class="text-muted-foreground text-sm">
                Enter your credentials to sign in to your account
            </p>
        </div>

        <div
            v-if="status"
            class="mt-4 rounded-md bg-green-50 p-4 text-sm text-green-600 dark:bg-green-900/50"
        >
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="mt-8 space-y-6">
            <div class="space-y-4">
                <div class="space-y-2">
                    <InputLabel for="email" value="Email" />
                    <TextInput
                        id="email"
                        type="email"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="name@example.com"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="space-y-2">
                    <InputLabel for="password" value="Password" />
                    <TextInput
                        id="password"
                        type="password"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        placeholder="Enter your password"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center space-x-2">
                        <Checkbox
                            name="remember"
                            v-model:checked="form.remember"
                        />
                        <span class="text-muted-foreground text-sm"
                            >Remember me</span
                        >
                    </label>

                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-primary text-sm hover:underline"
                    >
                        Forgot password?
                    </Link>
                </div>
            </div>

            <PrimaryButton class="w-full" :disabled="form.processing">
                Sign In
            </PrimaryButton>

            <p class="text-muted-foreground text-center text-sm">
                Don't have an account?
                <Link
                    :href="route('register')"
                    class="text-primary hover:underline"
                >
                    Create an account
                </Link>
            </p>
        </form>
    </GuestLayout>
</template>
