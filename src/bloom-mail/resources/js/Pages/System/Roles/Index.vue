<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppTable from '@/PageComponents/AppTable.vue';
import { Head,router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps(['roles'])

const { t, locale } = useI18n();

const tableHeaders = ref([
   {
     name: t('table.role_name'),
     value: "name"
   },
]);

console.log(props?.roles?.data);

</script>

<template>
    <Head :title="$t('nav.roles')" />

    <AuthenticatedLayout>
        <div class="bg-[#f2f4f6] h-screen py-6">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="mb-1 d-flex justify-between">
                    <h1
                        class="font-semibold leading-tight text-gray-800"
                    >
                        {{$t('nav.roles')}}
                    </h1>
                    <VBtn color="primary" @click="router.get(route('roles.create'))">
                        {{ $t('buttons.registration') }}
                    </VBtn>
                </div>
                <div
                    class="overflow-hidden sm:rounded-lg"
                >
                    <div class="py-6 text-gray-900">
                        <div>
                            <div class="my-5">
                                <AppTable
                                    :headers="tableHeaders"
                                    :data="props?.roles"
                                    :url="'roles'"
                                    :permission_name="'role'"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
