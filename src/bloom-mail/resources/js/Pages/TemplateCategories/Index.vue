<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { onMounted, ref } from 'vue';
import MailLayout from '@/Layouts/MailLayout.vue';
import { permissionGrant } from '@/Helper/permissionUtils';
import CustomizeTable from '@/PageComponents/CustomizeTable.vue';
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
const props = defineProps(['template_categories','auth']);

const permissions = props?.auth?.user?.permissions
const { t, locale } = useI18n();

const tableHeaders = ref([
    {
     header: t('table.folder_name'),
     val: "name"
   },
   {
    header: t('input.details'),
    val: "detail"
   }
]);


const routeUrl = ref('/template-categories')

const form = useForm({
    name: '',
    detail: '',
});
</script>

<template>
    <Head :title="$t('nav.template_categories')" />

    <MailLayout>
        <div class="bg-[#f2f4f6] h-screen">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        <div style="padding: 20px;">
                            <h1 class="my-2">{{ $t('nav.template_categories') }}</h1>
                            <VBtn color="primary" v-if="permissionGrant(permissions, 'templatecategory_createdit')" @click="router.get(route('template-categories.create'))">{{ $t('buttons.registration') }}</VBtn>
                            <div class="my-5">
                                <CustomizeTable
                                    :link="routeUrl"
                                    :headers="tableHeaders"
                                    :data="props?.template_categories"
                                    :permission_name="'templatecategory'"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MailLayout>
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


