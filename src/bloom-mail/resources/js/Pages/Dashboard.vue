<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MailLayout from '@/Layouts/MailLayout.vue';
import FloatMailButton from '@/PageComponents/FloatMailButton.vue';
import MailCreationDialog from '@/PageComponents/MailCreationDialog.vue';
import MailDetail from '@/PageComponents/MailDetail.vue';
import MailTable from '@/PageComponents/MailTable.vue';
import Pagination from '@/PageComponents/Pagination.vue';
import { Head,router, usePage } from '@inertiajs/vue3';

const { props } = usePage();

</script>

<template>
    <Head :title="$t('nav.home')" />

    <AuthenticatedLayout>
        <div class="py-6 text-gray-900" style="height: 100%;">
            <p>
                {{ $t('other.welcome') }}
                <!-- {{ props?.auth?.user?.name }} -->
            </p>
        </div>
    </AuthenticatedLayout>
</template>


<style scoped>
.folder-path {
  padding-left: 20px; /* Indent for folder structure */
}

.folder-item {
  margin: 5px 0; /* Space between folder items */
}

.sub-folder {
  padding-left: 20px; /* Further indent subfolders */
}

.transition-all {
  transition: all 0.5s ease-in-out;
}

.active-route
{
    font-weight: bold;
    color: #4891dc;
}
</style>
