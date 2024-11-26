<script setup>
import MailLayout from '@/Layouts/MailLayout.vue';
import CustomizeTable from '@/PageComponents/CustomizeTable.vue';
import { Head,router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps(['spams'])

const routeUrl = ref('/spams')

const { t, locale } = useI18n();

const tableHeaders = ref([
   {
     header: t('input.address'),
     val: "mail_address"
   }
]);
</script>

<template>
    <Head :title="$t('nav.spam')" />

    <MailLayout>
        <div class="bg-[#f2f4f6] h-screen">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        <div style="padding: 20px;">
                            <VBtn color="primary" @click="router.get(route('spams.create'))">
                                {{ $t('buttons.spam_signup') }}
                            </VBtn>
                            <div class="my-5">
                                <CustomizeTable
                                    :headers="tableHeaders"
                                    :data="props?.spams"
                                    :link="routeUrl"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MailLayout>
</template>
