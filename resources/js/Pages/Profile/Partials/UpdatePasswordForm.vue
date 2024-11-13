<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/shadcn/ui/card';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref<HTMLInputElement | null>(null);
const currentPasswordInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value?.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value?.focus();
            }
        },
    });
};
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>Update Password</CardTitle>
        </CardHeader>
        <CardContent>
            <p class="mb-4 text-gray-600 dark:text-gray-400">
                Ensure your account is using a long, random password to stay
                secure.
            </p>
            <form @submit.prevent="updatePassword" class="space-y-6">
                <div>
                    <InputLabel
                        for="current_password"
                        value="Current Password"
                    />
                    <TextInput
                        id="current_password"
                        ref="currentPasswordInput"
                        v-model="form.current_password"
                        type="password"
                        class="mt-1 block max-w-xl"
                        autocomplete="current-password"
                        placeholder="Enter your current password"
                    />
                    <InputError
                        :message="form.errors.current_password"
                        class="mt-2"
                    />
                </div>
                <div>
                    <InputLabel for="password" value="New Password" />
                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block max-w-xl"
                        autocomplete="new-password"
                        placeholder="Enter your new password"
                    />
                    <InputError :message="form.errors.password" class="mt-2" />
                </div>
                <div>
                    <InputLabel
                        for="password_confirmation"
                        value="Confirm Password"
                    />
                    <TextInput
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        class="mt-1 block max-w-xl"
                        autocomplete="new-password"
                        placeholder="Confirm your new password"
                    />
                    <InputError
                        :message="form.errors.password_confirmation"
                        class="mt-2"
                    />
                </div>
                <div class="flex items-center gap-4">
                    <PrimaryButton :disabled="form.processing"
                        >Save</PrimaryButton
                    >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-green-600 dark:text-green-400"
                    >
                        Saved.
                    </p>
                </div>
            </form>
        </CardContent>
    </Card>
</template>
