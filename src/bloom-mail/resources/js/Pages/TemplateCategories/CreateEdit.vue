<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import MailLayout from '@/Layouts/MailLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps(['template_category']);
const { t, locale } = useI18n();

const form = useForm({
    name: props?.template_category?.name,
    detail: props?.template_category?.detail ?? '',
})

const formSubmit = () => {
    const isEdit = Boolean(props?.template_category);

    const routeLink = isEdit ? route('template-categories.update', props.template_category.id) : route('template-categories.store');
    const method = isEdit ? 'put' : 'post';

    form[method](routeLink, {
        onSuccess: () => {
            form.reset();
        },
        onError: (error) => {
            console.error("Form submission error:", error);
        },
    });
};

</script>

<template>
    <Head :title="$t('input.template_category')" />

    <MailLayout>
        <div class="bg-[#f2f4f6] h-screen">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        <div style="padding: 20px;">
                            <VForm @submit.prevent="formSubmit">
                                <VCard>
                                    <VCardTitle>
                                        {{ props?.template_category ? $t('other.edit_templatecategory') : $t('other.create_templatecategory')}}
                                    </VCardTitle>
                                    <VCardText>
                                        <VRow class="my-3">
                                            <VCol cols="12" lg="5">
                                                <div class="mt-4">
                                                    <InputLabel for="name" :value="$t('input.title')"/>
                                                    <VTextField
                                                        density="compact"
                                                        variant="outlined"
                                                        id="name"
                                                        type="text"
                                                        class="mt-1 block w-full"
                                                        v-model="form.name"
                                                        required
                                                    ></VTextField>

                                                    <InputError class="mb-2" :message="form.errors.name" />
                                                </div>
                                                <div>
                                                    <InputLabel for="description" :value="$t('input.details')" />
                                                    <VTextarea
                                                        variant="outlined"
                                                        id="name"
                                                        type="text"
                                                        class="mt-1 block w-full"
                                                        v-model="form.detail"
                                                        required
                                                    ></VTextarea>

                                                    <InputError class="mb-2" :message="form.errors.detail" />
                                                </div>
                                            </VCol>
                                        </VRow>
                                        <div>
                                            <VBtn prepend-icon="mdi-content-save-all-outline"  color="customBtnColor" type="submit" class="text-white text-capitalize">{{ props?.template_category ?   $t('buttons.update_category') : $t('buttons.create_category') }}</VBtn>
                                        </div>
                                    </VCardText>
                                </VCard>
                            </VForm>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MailLayout>
</template>
