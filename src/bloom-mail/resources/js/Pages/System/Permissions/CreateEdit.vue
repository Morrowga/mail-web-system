<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps(['permission'])

const form = useForm({
    name: props?.permission?.name ?? '',
    display: props?.permission?.display ?? '',
    description: props?.permission?.description ?? '',
})

const formSubmit = () => {
    const isEdit = Boolean(props?.permission);

    const routeLink = isEdit ? route('permissions.update', props.permission.id) : route('permissions.store');
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
    <Head :title="$t('nav.permissions')" />

    <AuthenticatedLayout>
        <div class="bg-[#f2f4f6] h-screen py-6">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden sm:rounded-lg"
                >
                    <div class="py-6 text-gray-900">
                        <div>
                            <h2 class="font-medium">{{ props?.role ? $t('other.edit_permission') : $t('other.create_permission') }}</h2>
                            <VForm @submit.prevent="formSubmit">
                                <VCard class="my-4">
                                    <VCardText>
                                        <VRow class="my-3">
                                            <VCol cols="12" class="py-0">
                                                <div class="d-flex justify-start">
                                                    <div style="width: 17%; padding: 10px;">
                                                        <InputLabel for="address" :value="'Name'"/>
                                                    </div>
                                                    <div style="width: 30%;">
                                                        <VTextField
                                                            density="compact"
                                                            variant="outlined"
                                                            id="name"
                                                            readonly
                                                            type="text"
                                                            class="mt-1 block w-full"
                                                            v-model="form.name"
                                                            disabled
                                                            required
                                                        ></VTextField>
                                                        <InputError class="mb-2" :message="form.errors.name" />
                                                    </div>
                                                </div>
                                            </VCol>
                                            <VCol cols="12" class="py-0">
                                                <div class="d-flex justify-start">
                                                    <div style="width: 17%; padding: 10px;">
                                                        <InputLabel for="display" :value="'Display Name'"/>
                                                    </div>
                                                    <div style="width: 30%;">
                                                        <VTextField
                                                            density="compact"
                                                            variant="outlined"
                                                            id="display"
                                                            type="text"
                                                            class="mt-1 block w-full"
                                                            v-model="form.display"
                                                            required
                                                        ></VTextField>
                                                        <InputError class="mb-2" :message="form.errors.display" />
                                                    </div>
                                                </div>
                                            </VCol>
                                            <VCol cols="12" class="py-0">
                                                <div class="d-flex justify-start">
                                                    <div style="width: 17%; padding: 10px;">
                                                        <InputLabel for="description" :value="'Description'"/>
                                                    </div>
                                                    <div style="width: 50%;">
                                                        <VTextarea
                                                            density="compact"
                                                            variant="outlined"
                                                            id="description"
                                                            type="text"
                                                            class="mt-1 block w-full"
                                                            v-model="form.description"
                                                            required
                                                        ></VTextarea>
                                                        <InputError class="mb-2" :message="form.errors.description" />
                                                    </div>
                                                </div>
                                            </VCol>
                                        </VRow>
                                        <div class="text-right">
                                            <VBtn color="primary" prepend-icon="mdi-content-save-all-outline"  type="submit" class="text-white text-capitalize">{{ props?.permission ? $t('buttons.update') : $t('buttons.registration') }}</VBtn>
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
