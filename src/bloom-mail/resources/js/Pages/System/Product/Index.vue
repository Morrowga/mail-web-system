<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppTable from '@/PageComponents/AppTable.vue';
import { Head,router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps(['products'])

const { t, locale } = useI18n();

const tableHeaders = ref([
//    {
//      name: t('system.table.member_id'),
//      value: "id"
//    },
   {
     name: t('system.table.shop_name'),
     value: "shop_name"
   },
   {
     name: t('system.table.treatment_begin_date'),
     value: "treatment_begin_date"
   },
   {
     name: t('system.table.product_detail'),
     value: "product_detail"
   },
   {
     name: t('system.table.price'),
     value: "price"
   },
   {
     name: t('system.table.sale_start_date'),
     value: "sale_start_date"
   },
   {
     name: t('system.table.sale_end_date'),
     value: "sale_end_date"
   },
   {
     name: t('system.table.status'),
     value: "status"
   },
   {
     name: t('system.table.purchase_no'),
     value: "purchase_no"
   },
//    {
//      name: t('system.table.buyer_registration'),
//      value: "buyer_registration"
//    }
]);


</script>

<template>
    <Head :title="$t('system.nav.product')" />

    <AuthenticatedLayout>
        <div class="bg-[#f2f4f6] py-6">
            <div class="">
                <h1
                    class="font-semibold leading-tight text-gray-800"
                >
                    {{$t('system.nav.product_list')}}
                </h1>
                <div
                    class="overflow-hidden sm:rounded-lg"
                >
                    <div class="text-gray-900">
                        <div>
                            <div class="my-5">
                                <div class="mb-5">
                                    <VBtn color="primary" @click="router.get(route('products.create'))" variant="filled" style="border: 2px solid #45B4D3;">
                                        新規お知らせ作成
                                    </VBtn>
                                </div>
                                <AppTable
                                    :headers="tableHeaders"
                                    :data="props?.products"
                                    :url="'products'"
                                    :permission_name="'product'"
                                    :tableTitle="$t('system.nav.product_list')"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
