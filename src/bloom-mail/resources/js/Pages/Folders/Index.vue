<script setup>
import { permissionGrant } from '@/Helper/permissionUtils';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MailLayout from '@/Layouts/MailLayout.vue';
import CustomizeTable from '@/PageComponents/CustomizeTable.vue';
import { Head,router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps(['folders', 'auth'])
const { t, locale } = useI18n();
const permissions = props?.auth?.user?.permissions

const routeUrl = ref('/folders')

const tableHeaders = ref([
    {
     header: t('table.folder_name'),
     val: "name"
   },
   {
     header: t('table.search'),
     val: "search_character"
   },
   {
    header: t('table.method'),
    val: "method"
   }
]);

// Sample methods for the select input
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
                            <VBtn color="primary" v-if="permissionGrant(permissions, 'folder_createdit')" @click="router.get(route('folders.create'))">{{ $t('buttons.create_folder') }}</VBtn>
                            <div class="my-5">
                                <CustomizeTable
                                    :link="routeUrl"
                                    :headers="tableHeaders"
                                    :data="props?.folders"
                                    :permission_name="'folder'"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MailLayout>
</template>
