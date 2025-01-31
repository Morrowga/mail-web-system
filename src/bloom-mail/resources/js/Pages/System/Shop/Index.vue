<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppTable from '@/PageComponents/AppTable.vue';
import { Head,router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps(['shops'])

const { t, locale } = useI18n();

const tableHeaders = ref([
//    {
//      name: t('system.table.member_id'),
//      value: "id"
//    },
   {
     name: t('system.table.name'),
     value: "name"
   },
//    {
//      name: t('system.table.status'),
//      value: "status"
//    },
//    {
//      name: t('system.table.buyer_registration'),
//      value: "buyer_registration"
//    }
]);


</script>

<template>
    <Head :title="$t('system.nav.shop')" />

    <AuthenticatedLayout>
        <div class="bg-[#f2f4f6] py-6">
            <div class="">
                <h1
                    class="font-semibold leading-tight text-gray-800"
                >
                    {{$t('system.nav.shop')}}
                </h1>
                <div
                    class="overflow-hidden sm:rounded-lg"
                >
                    <div class="text-gray-900">
                        <div>
                            <div class="my-5">
                                <div class="mb-5">
                                    <VBtn color="primary" @click="router.get(route('shops.create'))" variant="filled" style="border: 2px solid #45B4D3;">
                                        {{  $t('system.nav.new_shop') }}
                                    </VBtn>
                                </div>
                                <AppTable
                                    :headers="tableHeaders"
                                    :data="props?.shops"
                                    :url="'shops'"
                                    :permission_name="'shop'"
                                    :tableTitle="$t('system.nav.shop_list')"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
