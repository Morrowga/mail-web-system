<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    login_id: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>
        <img src="/images/bloomlogo.png" />
        <form @submit.prevent="submit" class="mt-5">
            <VCard style="width: 500px;">
                <VCardText style="padding: 3rem;">
                <div>
                    <InputLabel for="email" :value="$t('auth.login_id')" />

                    <VTextField
                        variant="outlined"
                        id="text"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.login_id"
                        required
                        autofocus
                        autocomplete="username"
                    ></VTextField>

                    <InputError class="mt-2" :message="form.errors.email" />
                </div>


                <div>

                    <InputLabel for="password" :value="$t('auth.password')" />
                    <VTextField
                        variant="outlined"
                        id="password"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password"
                        autocomplete="current-password"
                        required
                    ></VTextField>

                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="mt-4 flex items-center justify-center mx-15">
                    <VBtn
                        type="submit"
                        style="background: #45B4D3; color: #fff; width: 100%;"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        {{$t('auth.login')}}
                    </VBtn>
                </div>
                <div class="mt-4 d-flex justify-center">
                    <label class="flex items-center">
                        <Checkbox name="remember" v-model:checked="form.remember" />
                        <span class="ms-2 text-sm text-gray-600"
                            >{{$t('auth.remember_me')}}</span
                        >
                    </label>
                </div>

                </VCardText>
            </VCard>
        </form>
    </GuestLayout>
</template>
