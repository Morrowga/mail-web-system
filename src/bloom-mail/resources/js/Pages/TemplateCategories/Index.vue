<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { onMounted, ref } from 'vue';

const props = defineProps(['template_categories']);
const form = useForm({
    name: '',
    detail: '',
});

const categories = ref([
    {name: 'Reservation', description: '-'},
    {name: 'Cancel', description: '-'},
    {name: 'New', description: '-'},
    {name: 'Contract', description: '-'},
    {name: 'Cancellation', description: '-'},
])

const formSubmit = () => {
    form.post(route('template-categories.store'),{
        onSuccess: () => {
            form.reset();
        },
        onError: (error) => {
        },
    })
}

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
                            <VForm @submit.prevent="formSubmit">
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

                                            <InputError class="mb-2" :message="form.errors.name" />
                                        </div>
                                        <div>
                                            <InputLabel for="description" value="Details" />
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
                                        <div>
                                            <VBtn color="customBtnColor" type="submit" class="text-white text-capitalize">Create New Category</VBtn>
                                        </div>
                                    </VCol>
                                    <VCol cols="12" lg="7">
                                        <div class="d-flex justify-center">
                                            <div class="table-container">
                                                <table class="custom-table">
                                                <!-- Fixed header -->
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

                                                <!-- Scrollable tbody within a fixed-height container -->
                                                <tbody class="scrollable-tbody">
                                                    <tr v-for="(item, index) in template_categories" :key="index">
                                                    <td class="checkbox-column px-5">
                                                        <div class="center-content">
                                                        <v-checkbox :label="''" density="compact" class="d-inline-flex pt-2 text-[#000]"></v-checkbox>
                                                        </div>
                                                    </td>
                                                    <td class="name-column">
                                                        {{ item.name }}
                                                    </td>
                                                    <td class="description-column">
                                                        {{ item.detail }}
                                                    </td>
                                                    </tr>
                                                </tbody>

                                                <!-- Fixed footer -->
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
                            </VForm>
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
  border: 1px solid #ddd;
  overflow: hidden;
}

.custom-table {
  width: 100%;
  border-collapse: collapse;
  table-layout: fixed; /* Ensures consistent column widths */
}

.custom-table thead,
.custom-table tfoot {
  display: table;
  width: 100%;
  table-layout: fixed;
  background-color: #f0f0f0;
}

.scrollable-tbody {
  display: block;
  max-height: 400px; /* Adjust for desired scrollable height */
  overflow-y: auto;
  width: 100%;
}

.scrollable-tbody tr {
  display: table;
  width: 100%;
  table-layout: fixed;
}

.custom-table th,
.custom-table td {
  text-align: left;
  padding: 8px;
  border-top: 1px solid #ddd;
  border-bottom: 1px solid #ddd;
  color: #0200fa !important;
}

.checkbox-column {
  width: 40px; /* Fixed width for checkbox column */
}

.center-content {
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>


