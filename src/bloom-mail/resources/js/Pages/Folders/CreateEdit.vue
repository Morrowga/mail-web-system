<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import MailLayout from '@/Layouts/MailLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps(['folder']);
const { t, locale } = useI18n();

const form = useForm({
    name: props?.folder?.name,
    search_character: props?.folder?.search_character ?? '',
    method: props?.folder?.method ?? '',
})

const methods = ref([
    {
        name: t('input.exact_match'),
        value: 'exact_match'
    },
    {
        name: t('input.partial_match'),
        value: 'partial_match'
    },
    {
        name: t('input.front_match'),
        value: 'front_match'
    },
    {
        name: t('input.backward_match'),
        value: 'backward_match'
    },
]);


const formSubmit = () => {
    const isEdit = Boolean(props?.folder);

    const routeLink = isEdit ? route('folders.update', props.folder.id) : route('folders.store');
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
    <Head :title="$t('nav.folders')" />

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
                                        {{ props?.folder ? $t('other.edit_folder') : $t('other.create_folder')}}
                                    </VCardTitle>
                                    <VCardText>
                                        <VRow class="my-3">
                                            <VCol cols="12" class="py-0">
                                                <div class="d-flex justify-start">
                                                    <div style="width: 17%; padding: 10px;">
                                                        <InputLabel for="name" :value="$t('table.folder_name')"/>
                                                    </div>
                                                    <div style="width: 83%;">
                                                        <VTextField
                                                            density="compact"
                                                            variant="outlined"
                                                            id="name"
                                                            type="text"
                                                            class="mt-1 block w-full"
                                                            v-model="form.name"
                                                            required
                                                        ></VTextField>
                                                    </div>
                                                </div>
                                                <InputError class="mt-2" :message="form.errors.name" />
                                            </VCol>
                                            <VCol cols="12" class="py-0">
                                                <div class="d-flex justify-start">
                                                    <div style="width: 17%; padding: 10px;">
                                                        <InputLabel for="search" :value="$t('input.search_character')"/>
                                                    </div>
                                                    <div style="width: 83%;">
                                                        <VTextField
                                                            density="compact"
                                                            variant="outlined"
                                                            id="search"
                                                            type="text"
                                                            class="mt-1 block w-full"
                                                            v-model="form.search_character"
                                                            required
                                                        ></VTextField>
                                                    </div>
                                                </div>
                                                <InputError class="mt-2" :message="form.errors.search_character" />
                                            </VCol>
                                            <VCol cols="12" class="py-0">
                                                <div class="d-flex justify-start">
                                                    <div style="width: 17%; padding: 10px;">
                                                        <InputLabel for="method" :value="$t('input.method')"/>
                                                    </div>
                                                    <div style="width: 83%;">
                                                        <v-radio-group
                                                        v-model="form.method"
                                                        inline
                                                        >
                                                        <v-radio
                                                            v-for="(method, index) in methods"
                                                            :key="index"
                                                            :label="method.name"
                                                            class="text-[#000]"
                                                            :value="method.value"
                                                        ></v-radio>
                                                        </v-radio-group>
                                                    </div>
                                                </div>
                                                <InputError class="mb-2" :message="form.errors.method" />
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
    </MailLayout>
</template>
