<script setup>
import { permissionGrant } from '@/Helper/permissionUtils';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppTable from '@/PageComponents/AppTable.vue';
import { Head,router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps(['users', 'auth'])

const { t, locale } = useI18n();

const permissions = props?.auth?.user?.permissions

const tableHeaders = ref([
   {
     name: t('table.name'),
     value: "name"
   },
   {
     name: t('auth.login_id'),
     value: "login_id"
   },
   {
     name: t('table.role_name'),
     value: "role_name"
   },
]);

console.log(props?.users)

</script>

<template>
    <Head :title="$t('nav.users')" />

    <AuthenticatedLayout>
        <div class="bg-[#f2f4f6] py-6">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="mb-1 d-flex justify-between">
                    <h1
                        class="font-semibold leading-tight text-gray-800"
                    >
                        {{$t('nav.users')}}
                    </h1>
                    <VBtn color="primary" v-if="permissionGrant(permissions, 'account_createdit')" @click="router.get(route('users.create'))">
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
                                    :data="props?.users"
                                    :url="'users'"
                                    :permission_name="'account'"
                                    :tableTitle="$t('nav.users')"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
