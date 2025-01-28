<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import WriteActionCard from '@/PageComponents/WriteActionCard.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps(['product'])

console.log(props?.user);

const form = useForm({
    name: props?.user?.name,
    login_id: props?.user?.login_id,
    password: null,
    password_confirmation: null,
    role_id: props?.user?.role_id
});

const formSubmit = () => {
    const isEdit = Boolean(props?.product);

    const routeLink = isEdit ? route('products.update', props.product.id) : route('products.store');
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
                   {{  $t('system.nav.product') }} > {{ Boolean(props?.product) ? $t('system.title.product_edit') :  $t('system.title.product_edit') }}
                </h1>
                <WriteActionCard :title="Boolean(props?.product) ? $t('system.title.product_edit') :  $t('system.title.product_edit')">
                    <VCardText class="mx-10">
                        <form @submit.prevent="formSubmit" class="my-3">
                            <div class="d-flex justify-between">
                                <div style="width: 20%">
                                    <InputLabel for="name" class="pt-3" :value="$t('input.name')" />
                                </div>

                                <div style="width: 80%">
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
                            </div>


                            <div class="mt-10 d-flex justify-between">
                                <div style="width: 20%;">
                                    <VBtn
                                        color="#727272"
                                        class="action-button-width"
                                    >
                                        {{ $t('system.buttons.back')}}
                                    </VBtn>
                                </div>
                                <div style="width: 80%;">
                                    <div class="d-flex justify-center">
                                        <VBtn
                                            color="#727272"
                                            class="submit-button-width"
                                            type="submit"
                                        >
                                            {{ $t('system.buttons.draft') }}
                                        </VBtn>
                                        <VBtn
                                            color="primary"
                                            class="submit-button-width mx-3"
                                            type="submit"
                                        >
                                            {{ $t('system.buttons.save')}}
                                        </VBtn>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </VCardText>
                </WriteActionCard>
                <!-- <VCard
                >
                    <VCardTitle>
                        <p class="text-sm text-gray-600">
                            {{ Boolean(props?.product) ? $t('system.title.product_edit') :  $t('system.title.product_edit') }}
                        </p>
                    </VCardTitle>

                </VCard> -->
            </div>
        </div>
    </AuthenticatedLayout>
</template>
