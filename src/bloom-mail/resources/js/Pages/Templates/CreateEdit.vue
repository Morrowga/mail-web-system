<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps(['template_categories', 'folders', 'template'])

const form = useForm({
    title: props?.template?.title ?? '',
    subject: props?.template?.subject ?? '',
    folder_id: props?.template?.folder_id ?? null,
    template_category_id: props?.template?.template_category_id ?? null,
    message_content: props?.template?.message_content ?? ''
})

const formSubmit = () => {
    const isEdit = Boolean(props?.template);

    const routeLink = isEdit ? route('templates.update', props.template.id) : route('templates.store');
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
    <Head :title="$t('nav.template')" />

    <AuthenticatedLayout>
        <div class="bg-[#f2f4f6] h-screen">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        <div style="padding: 20px;">
                            <h2 class="font-medium">{{ props?.template == null ? $t('other.template_create') : $t('other.template_update') }}</h2>
                            <VForm @submit.prevent="formSubmit">
                                <VCard class="ma-4">
                                    <VCardText>
                                        <VRow class="my-3">
                                            <VCol cols="12" class="py-0">
                                                <div class="d-flex justify-start">
                                                    <div style="width: 17%; padding: 10px;">
                                                        <InputLabel for="Title" :value="$t('input.title')"/>
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
                                                        <InputError class="mb-2" :message="form.errors.title" />
                                                    </div>
                                                </div>
                                            </VCol>
                                            <VCol cols="12" class="py-0">
                                                <div class="d-flex justify-start">
                                                    <div style="width: 17%; padding: 10px;">
                                                        <InputLabel for="subject" :value="$t('input.subject')"/>
                                                    </div>
                                                    <div style="width: 83%;">
                                                        <VTextField
                                                            density="compact"
                                                            variant="outlined"
                                                            placeholder="Re:"
                                                            id="subject"
                                                            type="text"
                                                            class="mt-1 block w-full"
                                                            v-model="form.subject"
                                                            required
                                                        ></VTextField>
                                                        <InputError class="mb-2" :message="form.errors.subject" />
                                                    </div>
                                                </div>
                                            </VCol>
                                            <VCol cols="12" class="py-0">
                                                <div class="d-flex justify-start">
                                                    <div style="width: 17%; padding: 10px;">
                                                        <InputLabel for="folder" :value="$t('input.folder')"/>
                                                    </div>
                                                    <div style="width: 83%;">
                                                        <VSelect
                                                            :placeholder="$t('input.select_folder')"
                                                            v-model="form.folder_id"
                                                            class="mt-1"
                                                            variant="outlined" density="compact" required hide-details
                                                            :items="props?.folders"
                                                            item-value="id"
                                                            item-title="search_character"
                                                        ></VSelect>
                                                        <InputError class="my-2" :message="form.errors.folder_id" />
                                                    </div>
                                                </div>
                                            </VCol>
                                            <VCol cols="12" class="py-0 mt-5">
                                                <div class="d-flex justify-start">
                                                    <div style="width: 17%; padding: 10px;">
                                                        <InputLabel for="template_category_id" :value="$t('input.template_category')"/>
                                                    </div>
                                                    <div style="width: 83%;">
                                                        <VSelect
                                                            v-model="form.template_category_id"
                                                            class="mt-1"
                                                            :placeholder="$t('input.select_template_category')"
                                                            variant="outlined" density="compact" required hide-details
                                                            :items="props?.template_categories"
                                                            item-value="id"
                                                            item-title="name"
                                                        ></VSelect>
                                                        <InputError class="my-2" :message="form.errors.template_category_id" />
                                                    </div>
                                                </div>
                                            </VCol>
                                            <VCol cols="12" class="py-0 mt-5">
                                                <div>
                                                    <div style="width: 17%; padding: 10px;">
                                                        <InputLabel for="message_content" :value="$t('input.message_content')"/>
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
                                                        <InputError class="mb-2" :message="form.errors.message_content" />
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
