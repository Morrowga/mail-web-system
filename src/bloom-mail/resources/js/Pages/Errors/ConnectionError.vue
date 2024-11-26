<script setup>
import MailLayout from '@/Layouts/MailLayout.vue';
import CustomizeTable from '@/PageComponents/CustomizeTable.vue';
import { Head,router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t, locale } = useI18n();

</script>

<template>
    <Head :title="'Connection Failed'" />

    <MailLayout>
        <div class="bg-[#f2f4f6] h-screen">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden sm:rounded-lg"
                >
                   <div>
                        <h1>Mail Server Connection was failed ! Please try again later</h1>
                   </div>
                </div>
            </div>
        </div>
    </MailLayout>
</template>
