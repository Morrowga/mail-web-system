<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import LoadingProgress from '@/Components/LoadingProgress.vue';
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
    extra_search: props?.folder?.extra_searches ?? [], // Initialize as an array
})

console.log(props?.folder?.extra_searches);

const addCondition = () => {
    form.extra_search.push({
        is_exclude: false,
        search_character: '',
        method: '',
    });
};

const showProgress = ref(false);

// Remove a condition by index
const removeCondition = (index) => {
    form.extra_search.splice(index, 1);
};

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

    showProgress.value = true;

    form[method](routeLink, {
        onSuccess: () => {
            form.reset();  // Reset the form upon success
            showProgress.value = false;

        },
        onError: (error) => {
            showProgress.value = false;
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
                                            <VCol cols="12" class="py-0">
                                                <VBtn color="primary" @click="addCondition">+ {{ $t('input.add_condition') }}</VBtn>
                                                <div v-for="(condition, index) in form.extra_search" :key="index" class="my-2 px-3" style="border: 1px solid gray; border-radius: 10px;">
                                                    <div class="d-flex justify-between">
                                                    <VCheckbox
                                                        v-model="condition.is_exclude"
                                                        :id="'is_exclude-' + index"
                                                        :label="$t('input.is_exclude')"
                                                    />
                                                    <div class="py-2" style="cursor: pointer;" @click="removeCondition(index)">
                                                        <VIcon icon="mdi-close"></VIcon>
                                                    </div>
                                                    </div>
                                                    <div class="d-flex justify-start">
                                                    <div style="width: 17%; padding: 10px;">
                                                        <InputLabel for="search" :value="$t('input.search_character')" />
                                                    </div>
                                                    <div style="width: 83%;">
                                                        <VTextField
                                                        v-model="condition.search_character"
                                                        density="compact"
                                                        variant="outlined"
                                                        id="search"
                                                        type="text"
                                                        class="mt-1 block w-full"
                                                        required
                                                        />
                                                    </div>
                                                    </div>
                                                    <div class="d-flex justify-start">
                                                    <div style="width: 17%; padding: 10px;">
                                                        <InputLabel for="method" :value="$t('input.method')" />
                                                    </div>
                                                    <div style="width: 83%;">
                                                        <v-radio-group
                                                        v-model="condition.method"
                                                        inline
                                                        >
                                                        <v-radio
                                                            v-for="(method, index) in methods"
                                                            :key="index"
                                                            :label="method.name"
                                                            :value="method.value"
                                                        />
                                                        </v-radio-group>
                                                    </div>
                                                    </div>
                                                </div>
                                                </VCol>
                                        </VRow>
                                        <div>
                                            <VBtn color="customBtnColor" prepend-icon="mdi-content-save-all-outline"  type="submit" class="text-white text-capitalize">{{ props?.folder ? $t('buttons.update') : $t('buttons.registration') }}</VBtn>
                                        </div>
                                    </VCardText>
                                    <LoadingProgress :visible="showProgress" @update:visible="showProgress = $event" />
                                </VCard>
                            </VForm>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MailLayout>
</template>
