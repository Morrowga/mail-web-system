<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps(['user', 'roles'])

console.log(props?.user);

const form = useForm({
    name: props?.user?.name,
    email: props?.user?.email,
    password: null,
    password_confirmation: null,
    role_id: props?.user?.role_id
});

const formSubmit = () => {
    const isEdit = Boolean(props?.user);

    const routeLink = isEdit ? route('users.update', props.user.id) : route('users.store');
    const method = isEdit ? 'patch' : 'post';

    form[method](routeLink, {
        onSuccess: () => {
            form.reset();  // Reset the form upon success
        },
        onError: (error) => {
            console.error("Form submission error:", error); // Handle the error if needed
        },
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto sm:px-6 space-y-6 lg:px-8">
                <h1
                    class="font-semibold leading-tight text-gray-800"
                >
                    {{ Boolean(props?.user) ? $t('other.user_edit_title') :  $t('other.user_create_title') }}
                </h1>
                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8"
                >
                    <header>
                        <p class="text-sm text-gray-600">
                            {{ Boolean(props?.user) ? $t('other.user_edit') : $t('other.user_create') }}
                        </p>
                    </header>
                    <form @submit.prevent="formSubmit" class="my-3">
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

                        <div class="mt-4 w-50" v-if="props?.user == undefined">
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

                        <div class="mt-4 w-50" v-if="props?.user == undefined">
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

                        <div class="mt-4 w-50">
                            <InputLabel
                                for="role"
                                :value="$t('input.role')"
                            />
                            <VSelect
                                v-model="form.role_id"
                                :placeholder="'Select Role'"
                                variant="outlined" density="compact" required hide-details
                                :items="roles"
                                item-value="id"
                                clearable
                                class="mt-1"
                                item-title="name"
                            ></VSelect>

                            <InputError
                                class="mt-2"
                                :message="form.errors.role_id"
                            />
                        </div>

                        <div class="mt-10 flex items-center justify-end w-50">
                            <VBtn
                                prepend-icon="mdi-content-save-all-outline"
                                color="primary"
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                            >
                                {{ Boolean(props?.user) ? $t('buttons.update') : $t('buttons.registration')}}
                            </VBtn>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
