<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppTable from '@/PageComponents/AppTable.vue';
import { Head,router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps(['permissions'])

const { t, locale } = useI18n();

const tableHeaders = ref([
   {
     name: t('table.permission_name'),
     value: "name"
   },
   {
     name: t('table.permission_display_name'),
     value: "display"
   },
   {
     name: t('table.permission_description'),
     value: "description"
   }
]);

console.log(props?.permissions?.data);

</script>

<template>
    <Head :title="$t('nav.permissions')" />

    <AuthenticatedLayout>
        <div class="bg-[#f2f4f6] py-6">
            <div class="">
                <h1
                    class="font-semibold leading-tight text-gray-800"
                >
                    {{$t('nav.permissions')}}
                </h1>
                <div
                    class="overflow-hidden sm:rounded-lg"
                >
                    <div class="py-6 text-gray-900">
                        <div>
                            <div class="my-5">
                                <AppTable
                                    :headers="tableHeaders"
                                    :data="props?.permissions"
                                    :url="'permissions'"
                                    :tableTitle="$t('nav.permissions')"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
