<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CustomizeTable from '@/PageComponents/CustomizeTable.vue';
import { Head,router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps(['folders'])
const routeUrl = ref('/folders')

const tableHeaders = ref([
   {
     header: "Search",
     val: "search_character"
   },
   {
     header: "Method",
     val: "method"
   }
]);

// Sample methods for the select input
</script>

<template>
    <Head title="Folders" />

    <AuthenticatedLayout>
        <div class="bg-[#f2f4f6] h-screen">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        <div style="padding: 20px;">
                            <VBtn color="primary" @click="router.get(route('folders.create'))">Folder</VBtn>
                            <div class="my-5">
                                <CustomizeTable
                                    :link="routeUrl"
                                    :headers="tableHeaders"
                                    :data="props?.folders"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
