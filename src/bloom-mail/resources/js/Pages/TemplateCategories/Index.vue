<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { ref } from 'vue';

const form = useForm({
    login_id: '',
    password: '',
    remember: false,
});

const categories = ref([
    {name: 'Reservation', description: '-'},
    {name: 'Cancel', description: '-'},
    {name: 'New', description: '-'},
    {name: 'Contract', description: '-'},
    {name: 'Cancellation', description: '-'},
])

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
                        <div style="padding: 20px;">
                            <h1>Template Category</h1>
                            <VRow>
                                <VCol cols="12" lg="5">
                                    <h3 class="mt-2">Create a new template category</h3>
                                    <div class="mt-4">
                                        <InputLabel for="name" value="Name"/>
                                        <VTextField
                                            density="compact"
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
                                        <InputLabel for="description" value="Details" />
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
                                    <div>
                                        <VBtn color="customBtnColor" class="text-white text-capitalize">Create New Category</VBtn>
                                    </div>
                                </VCol>
                                <VCol cols="12" lg="7">
                                    <div class="d-flex justify-center">
                                        <div class="table-container">
                                            <table class="custom-table">
                                                <thead>
                                                    <tr>
                                                        <th class="checkbox-column px-5">
                                                            <div class="center-content">
                                                                <v-checkbox label="" class="d-inline-flex pt-2 text-[#000]" density="compact"></v-checkbox>
                                                            </div>
                                                        </th>
                                                        <th>Name</th>
                                                        <th>Description</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                <tr v-for="(item, index) in categories" :key="index">
                                                    <td class="checkbox-column px-5">
                                                        <div class="center-content">
                                                            <v-checkbox :label="''" density="compact" class="d-inline-flex pt-2 text-[#000]" ></v-checkbox>
                                                        </div>
                                                    </td>
                                                    <td class="name-column">
                                                    {{ item.name }}
                                                    </td>
                                                    <td class="description-column">
                                                    {{ item.description }}
                                                    </td>
                                                </tr>
                                                </tbody>

                                                <tfoot>
                                                <tr>
                                                    <th class="checkbox-column px-5">
                                                        <div class="center-content">
                                                            <v-checkbox label="" class="d-inline-flex pt-2 text-[#000]" density="compact"></v-checkbox>
                                                        </div>
                                                    </th>
                                                    <th>Name</th>
                                                    <th>Description</th>
                                                </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </VCol>
                            </VRow>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


<style scoped>
.table-container {
  width: 80%;
  overflow-x: auto;
  border: 1px solid #ddd;
}

.custom-table {
  width: 100%;
  border-collapse: collapse;
  border-spacing: 0;
}

.custom-table th,
.custom-table td {
  text-align: left;
  border-top: 1px solid #ddd;
  border-bottom: 1px solid #ddd;
  color: #0200fa !important;
}

.checkbox-column{
    width: 40px;
}

.custom-table thead th,
.custom-table tfoot th {
  background-color: #f0f0f0;
  font-weight: bold;
}
</style>
