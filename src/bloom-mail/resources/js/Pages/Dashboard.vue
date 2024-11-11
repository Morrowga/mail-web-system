<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FloatMailButton from '@/PageComponents/FloatMailButton.vue';
import MailCreationDialog from '@/PageComponents/MailCreationDialog.vue';
import MailDetail from '@/PageComponents/MailDetail.vue';
import MailTable from '@/PageComponents/MailTable.vue';
import Pagination from '@/PageComponents/Pagination.vue';
import { Head,router } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';
import axios from 'axios';

const createDialogVisible = ref(false);

const props = defineProps(['templates', 'from'])

const loading = ref(false);  // Loading status

const mails = ref({});

const itemsPerPage = 10;

const selectedMail = ref(null);

const page = ref(1);

const totalPages = ref(1);

const isVisibleFloatButton = ref(false);

const handlePageChange = (newPage) => {
  page.value = newPage;
  fetchEmails(newPage);
};

const handleRowSelected = (row) => {
  selectedMail.value = row;
  if(row?.status == 'new')
  {
    markAsRead(row?.uid)
  }
};

const  isActiveRoute = (name) =>  {
    return route().current(name);
}


const removeRow = (row) => {
  selectedMail.value = null;
};

const fetchEmails = async (pageNumber = 1) => {
  loading.value = true;

  try {
    const response = await axios.get(`/mails/inbox`, {
      params: {
        page: pageNumber,
      },
    });

    mails.value = response.data.data;
    totalPages.value = response.data.last_page;
    page.value = response.data.current_page;
  } catch (error) {
    console.error('Error fetching emails:', error);
  } finally {
    loading.value = false;
  }
};


const markAsRead = async (uid) => {
    try {
        const response = await axios.post(`/mails/mark-as-read/${uid}`);
        if (response.data.status === 'success') {
            const mailItem = mails.value.find(mail => mail.uid === uid);
            console.log(mailItem);
            if (mailItem) {
                mailItem.status = 'read'; // Update status locally
            }
        }
    } catch (error) {
        console.error('Failed to mark as read:', error);
    }
};


onMounted(() => {
  fetchEmails()

  Echo.channel('mails')
    .listen('.mail-fetched', (event) => {
        console.log(event.mails);
        if(event.mails.length > 0 && page.value == 1)
        {
            mails.value = [...event.mails, ...mails.value];
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
                                <div class="mb-5 cursor-pointer">
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
                            <VCol cols="12" :lg="selectedMail ? 5 : 10"
                            >
                                <div class="mb-3">
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
                                    <MailTable :loading="loading" :data="mails" @rowSelected="handleRowSelected" />
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
                                lg="5"
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
