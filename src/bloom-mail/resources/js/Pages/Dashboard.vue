<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FloatMailButton from '@/PageComponents/FloatMailButton.vue';
import MailCreationDialog from '@/PageComponents/MailCreationDialog.vue';
import MailDetail from '@/PageComponents/MailDetail.vue';
import MailTable from '@/PageComponents/MailTable.vue';
import Pagination from '@/PageComponents/Pagination.vue';
import { Head,router } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const createDialogVisible = ref(false);

const props = defineProps(['mails', 'templates', 'from'])

const mails = ref(props?.mails);

const itemsPerPage = 10;

const selectedMail = ref(null);

const page = ref(1);

const totalPages = computed(() => Math.ceil(props?.mails?.length / itemsPerPage));

const isVisibleFloatButton = ref(false);

const handlePageChange = (newPage) => {
  page.value = newPage;
  console.log(page.value);
};

const paginatedMails = computed(() => {
  const start = (page.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return mails.value.slice(start, end);
});

const handleRowSelected = (row) => {
  selectedMail.value = row;
};

const  isActiveRoute = (name) =>  {
    return route().current(name);
}


const removeRow = (row) => {
  selectedMail.value = null;
};


onMounted(() => {
  Echo.channel('mails')
    .listen('.mail-fetched', (event) => {
        console.log(event.mails);
        console.log(event);
        if(event.mails != null)
        {
            const newMails = Array.isArray(event.mails) ? event.mails : [event.mails];

            mails.value = [...newMails, ...mails.value];

            console.log(mails.value);

            totalPages.value = Math.ceil(mails.value.length / itemsPerPage);
        }
    })
    .error((error) => {
      console.error('Broadcast error:', error);
    });
});


onUnmounted(() => {
    Echo.leaveChannel('mails');
    console.log('disconnected')
});
</script>

<template>
    <Head title="Inbox" />

    <AuthenticatedLayout>
        <div class="bg-[#f2f4f6]">
            <div class="mx-auto sm:px-6 lg:px-5">
                <div
                    class="overflow-hidden sm:rounded-lg"
                >
                    <div class="py-6 text-gray-900" style="height: 100%;">
                        <VRow>
                            <VCol cols="12" lg="2">
                                <div class="my-5 cursor-pointer">
                                    <p :class="{ 'active-route': isActiveRoute('dashboard') }">
                                        Inbox ( {{ mails?.length ?? 0  }} )
                                    </p>
                                    <!-- <div class="folder-path">
                                        <p class="folder-item">├ Folder 1 (50)</p>
                                        <div class="sub-folder">
                                        <p class="folder-item">　├ Folder 2 (30)</p>
                                        <p class="folder-item">　└ Folder 3 (20)</p>
                                        </div>
                                    </div> -->
                                </div>
                                <div class="my-10 cursor-pointer">
                                    <p>Sent Mail</p>
                                </div>
                                <div class="my-10 cursor-pointer">
                                    <p>Draf Email</p>
                                </div>
                                <div class="my-10 cursor-pointer">
                                    <p @click="router.get('/spams')" style="cursor: pointer;">Spam</p>
                                </div>
                                <div class="my-10 cursor-pointer">
                                    <p>Trash Can</p>
                                </div>
                            </VCol>
                            <VCol cols="12" :lg="selectedMail ? 6 : 10"
                            >
                                <div class="mt-5 mb-3">
                                <MailCreationDialog
                                    :createDialog="createDialogVisible"
                                    :floatButton="isVisibleFloatButton"
                                    @update:dialog="createDialogVisible = $event"
                                    @update:visibleFloat="isVisibleFloatButton = $event"
                                    :templates="props?.templates"
                                    :from="props?.from"
                                />
                                </div>

                                <div>
                                <VCard style="border-radius: 20px;">
                                    <MailTable :data="paginatedMails" @rowSelected="handleRowSelected" />
                                    <Pagination
                                        :totalPages="totalPages"
                                        :currentPage="page"
                                        @pageChanged="handlePageChange"
                                    />
                                </VCard>
                                </div>
                            </VCol>

                            <VCol
                                v-if="selectedMail"
                                cols="12"
                                lg="4"
                            >
                                <VCard class="mt-5" style="border-radius: 20px;">
                                <MailDetail :mail="selectedMail" @handleRemoveRow="removeRow" />
                                </VCard>
                            </VCol>
                        </VRow>
                    </div>
                </div>
            </div>
            <FloatMailButton
                v-if="isVisibleFloatButton"
                @update:onOpenDialog="createDialogVisible = $event"
                @update:hideFloat="isVisibleFloatButton = $event"
            />
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
