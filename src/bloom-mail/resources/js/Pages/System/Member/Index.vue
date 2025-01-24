<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppTable from '@/PageComponents/AppTable.vue';
import { Head,router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps(['members'])

const { t, locale } = useI18n();

const tableHeaders = ref([
   {
     name: t('system.table.member_id'),
     value: "member_id"
   },
   {
     name: t('system.table.name'),
     value: "name"
   },
   {
     name: t('system.table.email'),
     value: "email"
   },
   {
     name: t('system.table.age'),
     value: "age"
   },
   {
     name: t('system.table.tel'),
     value: "tel"
   }
]);


</script>

<template>
    <Head :title="$t('system.nav.membership')" />

    <AuthenticatedLayout>
        <div class="bg-[#f2f4f6] h-screen py-6">
            <div class="mx-auto sm:px-6 lg:px-8">
                <h1
                    class="font-semibold leading-tight text-gray-800"
                >
                    {{$t('system.nav.membership')}}
                </h1>
                <div
                    class="overflow-hidden sm:rounded-lg"
                >
                    <div class="py-6 text-gray-900">
                        <div>
                            <div class="my-5">
                                <AppTable
                                    :headers="tableHeaders"
                                    :data="props?.members"
                                    :url="'members'"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
