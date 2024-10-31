<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';

const form = useForm({
    login_id: '',
    password: '',
    remember: false,
});

</script>

<template>
    <Head title="Templates" />

    <AuthenticatedLayout>
        <div class="bg-[#f2f4f6] h-screen">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        <div>
                            <h1>Template Category</h1>
                            <VRow>
                                <VCol cols="12" lg="6">
                                    <p class="mt-2">Create a new template category</p>
                                    <div class="mt-4">
                                        <InputLabel for="name" value="Name" />
                                        <VTextField
                                            variant="outlined"
                                            id="name"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="form.name"
                                            required
                                        ></VTextField>

                                        <InputError class="mt-2" :message="form.errors.name" />
                                    </div>
                                    <div>
                                        <InputLabel for="description" value="Description" />
                                        <VTextarea
                                            variant="outlined"
                                            id="name"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="form.description"
                                            required
                                        ></VTextarea>

                                        <InputError class="mt-2" :message="form.errors.description" />
                                    </div>
                                </VCol>
                                <VCol cols="12" lg="6">
                                    <VCard>
                                        <VCardText>
                                            <p>Category Lists</p>
                                        </VCardText>
                                    </VCard>
                                </VCol>
                            </VRow>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
