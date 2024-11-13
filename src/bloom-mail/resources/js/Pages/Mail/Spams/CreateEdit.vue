<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps(['spam'])

const form = useForm({
    mail_address: props?.spam?.mail_address,
})

const formSubmit = () => {
    const isEdit = Boolean(props?.spam);

    const routeLink = isEdit ? route('spams.update', props.spam.id) : route('spams.store');
    const method = isEdit ? 'put' : 'post';

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
    <Head :title="$t('nav.spam')" />

    <AuthenticatedLayout>
        <div class="bg-[#f2f4f6] h-screen">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        <div style="padding: 20px;">
                            <h2 class="font-medium">{{ props?.spam ? $t('other.edit_spam') : $t('other.create_spam') }}</h2>
                            <VForm @submit.prevent="formSubmit">
                                <VCard class="ma-4">
                                    <VCardText>
                                        <VRow class="my-3">
                                            <VCol cols="12" class="py-0">
                                                <div class="d-flex justify-start">
                                                    <div style="width: 17%; padding: 10px;">
                                                        <InputLabel for="address" :value="$t('input.address')"/>
                                                    </div>
                                                    <div style="width: 83%;">
                                                        <VTextField
                                                            density="compact"
                                                            variant="outlined"
                                                            id="address"
                                                            type="email"
                                                            class="mt-1 block w-full"
                                                            v-model="form.mail_address"
                                                            required
                                                        ></VTextField>
                                                        <InputError class="mb-2" :message="form.errors.mail_address" />
                                                    </div>
                                                </div>
                                            </VCol>
                                        </VRow>
                                        <div>
                                            <VBtn color="customBtnColor" type="submit" class="text-white text-capitalize">{{ $t('buttons.registration') }}</VBtn>
                                        </div>
                                    </VCardText>
                                </VCard>
                            </VForm>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
