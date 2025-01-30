<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppTable from '@/PageComponents/AppTable.vue';
import { Head,router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps(['notifications'])

const { t, locale } = useI18n();

const tableHeaders = ref([
//    {
//      name: t('system.table.member_id'),
//      value: "id"
//    },
   {
     name: t('system.table.title'),
     value: "title"
   },
   {
     name: t('system.table.content'),
     value: "content"
   },
   {
     name: t('system.table.type'),
     value: "type"
   },
   {
     name: t('system.table.start_time'),
     value: "start_time"
   },
   {
     name: t('system.table.end_time'),
     value: "end_time"
   },
   {
     name: t('system.table.status'),
     value: "status"
   },
//    {
//      name: t('system.table.buyer_registration'),
//      value: "buyer_registration"
//    }
]);


</script>

<template>
    <Head :title="$t('system.nav.notification')" />

    <AuthenticatedLayout>
        <div class="bg-[#f2f4f6] py-6">
            <div class="">
                <h1
                    class="font-semibold leading-tight text-gray-800"
                >
                    {{$t('system.nav.notification_list')}}
                </h1>
                <div
                    class="overflow-hidden sm:rounded-lg"
                >
                    <div class="text-gray-900">
                        <div>
                            <div class="my-5">
                                <div class="mb-5">
                                    <VBtn color="primary" @click="router.get(route('notifications.create'))" variant="filled" style="border: 2px solid #45B4D3;">
                                        {{  $t('system.nav.new_notification') }}
                                    </VBtn>
                                </div>
                                <AppTable
                                    :headers="tableHeaders"
                                    :data="props?.notifications"
                                    :url="'notifications'"
                                    :permission_name="'noti'"
                                    :tableTitle="$t('system.nav.notification_list')"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
