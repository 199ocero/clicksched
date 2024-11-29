<script setup lang="ts">
import Checkbox from '@/Components/Checkbox.vue';
import GoogleIcon from '@/Components/GoogleIcon.vue';
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

const googleLogin = () => {
    window.location.href = route('google.login');
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="flex flex-col space-y-2 text-center">
            <h1 class="text-2xl font-semibold tracking-tight">Welcome back</h1>
            <p class="text-sm text-muted-foreground">
                Enter your credentials to sign in to your account
            </p>
        </div>

        <div
            v-if="status"
            class="mt-4 rounded-md bg-green-50 p-4 text-sm text-green-600 dark:bg-green-900/50"
        >
            {{ status }}
        </div>

        <div class="mt-8 space-y-6">
            <form @submit.prevent="submit">
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
                            <span class="text-sm text-muted-foreground"
                                >Remember me</span
                            >
                        </label>

                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-sm text-primary hover:underline"
                        >
                            Forgot password?
                        </Link>
                    </div>
                    <PrimaryButton class="w-full" :disabled="form.processing">
                        Sign In
                    </PrimaryButton>
                </div>
            </form>
            <div class="mt-4 flex items-center justify-center space-x-2">
                <div class="flex-grow border-t border-zinc-300"></div>
                <span class="text-sm text-muted-foreground">or</span>
                <div class="flex-grow border-t border-zinc-300"></div>
            </div>

            <PrimaryButton
                variant="outline"
                @click="googleLogin"
                class="w-full"
            >
                <GoogleIcon class="h-5 w-5" />
                Sign in with Google
            </PrimaryButton>

            <p class="text-center text-sm text-muted-foreground">
                Don't have an account?
                <Link
                    :href="route('register')"
                    class="text-primary hover:underline"
                >
                    Create an account
                </Link>
            </p>
        </div>
    </GuestLayout>
</template>
