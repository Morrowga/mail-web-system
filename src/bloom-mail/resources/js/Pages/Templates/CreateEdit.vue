<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    title: '',
    subject: 'Re:',
    folder: '',
    message_content: ''
})

</script>

<template>
    <Head title="Folders - Modification" />

    <AuthenticatedLayout>
        <div class="bg-[#f2f4f6] h-screen">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        <div style="padding: 20px;">
                            <h2 class="font-medium">Template Editing</h2>
                            <VCard class="ma-4">
                                <VCardText>
                                    <VRow class="my-3">
                                        <VCol cols="12" class="py-0">
                                            <div class="d-flex justify-start">
                                                <div style="width: 17%; padding: 10px;">
                                                    <InputLabel for="Title" value="Title"/>
                                                </div>
                                                <div style="width: 83%;">
                                                    <VTextField
                                                        density="compact"
                                                        variant="outlined"
                                                        id="Title"
                                                        type="text"
                                                        class="mt-1 block w-full"
                                                        v-model="form.title"
                                                        required
                                                    ></VTextField>
                                                </div>
                                            </div>
                                            <InputError class="mt-2" :message="form.errors.title" />
                                        </VCol>
                                        <VCol cols="12" class="py-0">
                                            <div class="d-flex justify-start">
                                                <div style="width: 17%; padding: 10px;">
                                                    <InputLabel for="subject" value="Subject"/>
                                                </div>
                                                <div style="width: 83%;">
                                                    <VTextField
                                                        density="compact"
                                                        variant="outlined"
                                                        id="subject"
                                                        type="text"
                                                        class="mt-1 block w-full"
                                                        v-model="form.subject"
                                                        required
                                                    ></VTextField>
                                                </div>
                                            </div>
                                            <InputError class="mt-2" :message="form.errors.subject" />
                                        </VCol>
                                        <VCol cols="12" class="py-0">
                                            <div class="d-flex justify-start">
                                                <div style="width: 17%; padding: 10px;">
                                                    <InputLabel for="folder" value="Folder"/>
                                                </div>
                                                <div style="width: 83%;">
                                                    <VTextField
                                                        density="compact"
                                                        variant="outlined"
                                                        id="folder"
                                                        type="text"
                                                        class="mt-1 block w-full"
                                                        v-model="form.folder"
                                                        required
                                                    ></VTextField>
                                                </div>
                                            </div>
                                            <InputError class="mt-2" :message="form.errors.folder" />
                                        </VCol>
                                        <VCol cols="12" class="py-0">
                                            <div>
                                                <div style="width: 17%; padding: 10px;">
                                                    <InputLabel for="message_content" value="Message Content"/>
                                                </div>
                                                <div style="width: 100%;">
                                                    <VTextarea
                                                        density="compact"
                                                        variant="outlined"
                                                        id="message_content"
                                                        type="text"
                                                        class="mt-1 block w-full"
                                                        v-model="form.message_content"
                                                        required
                                                    ></VTextarea>
                                                </div>
                                            </div>
                                            <InputError class="mt-2" :message="form.errors.message_content" />
                                        </VCol>
                                    </VRow>
                                    <div>
                                        <VBtn color="customBtnColor" class="text-white text-capitalize">Registration</VBtn>
                                    </div>
                                </VCardText>
                            </VCard>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
