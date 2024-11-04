<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CustomizeTable from '@/PageComponents/CustomizeTable.vue';
import { Head,router } from '@inertiajs/vue3';
import { ref } from 'vue';

const spams = ref([
    {
        address: "dummy@example.com",
    },
    {
        address: "spam@spam.com",
    },
    {
        address: "error@error.jp",
    },
    {
        address: "ng@mail.co",
    },
])

const tableHeaders = ref([
   {
     header: "Address",
     val: "address"
   }
]);
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
                            <VBtn color="primary" @click="router.get(route('spams.create'))">
                                Spam Signups
                            </VBtn>
                            <div class="my-5">
                                <CustomizeTable
                                    :headers="tableHeaders"
                                    :data="spams"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
