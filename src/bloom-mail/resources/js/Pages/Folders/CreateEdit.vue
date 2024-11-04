<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    search: '',
    method: ''
})

const methods = ref([
    'Exact match',
    'Partial match',
    'Front match',
    'Backward match',
]);

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
                            <VCard>
                                <VCardText>
                                    <VRow class="my-3">
                                        <VCol cols="12" class="py-0">
                                            <div class="d-flex justify-start">
                                                <div style="width: 17%; padding: 10px;">
                                                    <InputLabel for="search" value="Search Character"/>
                                                </div>
                                                <div style="width: 83%;">
                                                    <VTextField
                                                        density="compact"
                                                        variant="outlined"
                                                        id="search"
                                                        type="text"
                                                        class="mt-1 block w-full"
                                                        v-model="form.search"
                                                        required
                                                    ></VTextField>
                                                </div>
                                            </div>
                                            <InputError class="mt-2" :message="form.errors.search" />
                                        </VCol>
                                        <VCol cols="12" class="py-0">
                                            <div class="d-flex justify-start">
                                                <div style="width: 17%; padding: 10px;">
                                                    <InputLabel for="method" value="Method"/>
                                                </div>
                                                <div style="width: 83%;">
                                                    <v-radio-group
                                                    v-model="inline"
                                                    inline
                                                    >
                                                    <v-radio
                                                        v-for="(method, index) in methods"
                                                        :key="index"
                                                        :label="method"
                                                        class="text-[#000]"
                                                        :value="index"
                                                    ></v-radio>
                                                    </v-radio-group>
                                                </div>
                                            </div>
                                            <InputError class="mt-2" :message="form.errors.method" />
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
