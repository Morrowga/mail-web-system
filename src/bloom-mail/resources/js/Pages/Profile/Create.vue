<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('profile.store.account'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                {{ $t('nav.account_registration') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8"
                >
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">
                            {{ $t('nav.account_registration') }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-600">
                            {{ $t('other.account_registration_text') }}
                        </p>
                    </header>
                    <form @submit.prevent="submit" class="my-3">
                        <div class="w-50">
                            <InputLabel for="name" :value="$t('input.name')" />

                            <TextInput
                                id="name"
                                type="text"
                                density="compact"
                                class="mt-1 block w-full"
                                v-model="form.name"
                                required
                                autofocus
                                autocomplete="name"
                            />

                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div class="mt-4 w-50">
                            <InputLabel for="email" :value="$t('input.email')" />

                            <TextInput
                                id="email"
                                type="email"
                                density="compact"
                                class="mt-1 block w-full"
                                v-model="form.email"
                                required
                                autocomplete="username"
                            />

                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <div class="mt-4 w-50">
                            <InputLabel for="password" :value="$t('auth.password')" />

                            <TextInput
                                id="password"
                                type="password"
                                density="compact"
                                class="mt-1 block w-full"
                                v-model="form.password"
                                required
                                autocomplete="new-password"
                            />

                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <div class="mt-4 w-50">
                            <InputLabel
                                for="password_confirmation"
                                :value="$t('input.confirm_password')"
                            />

                            <TextInput
                                id="password_confirmation"
                                type="password"
                                density="compact"
                                class="mt-1 block w-full"
                                v-model="form.password_confirmation"
                                required
                                autocomplete="new-password"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.password_confirmation"
                            />
                        </div>

                        <div class="mt-4 flex items-center justify-start">
                            <PrimaryButton
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                            >
                                {{ $t('buttons.registration' )}}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
