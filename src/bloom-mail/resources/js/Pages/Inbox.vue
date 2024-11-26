<script setup>
import FloatMailButton from '@/PageComponents/FloatMailButton.vue';
import MailCreationDialog from '@/PageComponents/MailCreationDialog.vue';
import MailDetail from '@/PageComponents/MailDetail.vue';
import MailTable from '@/PageComponents/MailTable.vue';
import Pagination from '@/PageComponents/Pagination.vue';
import { Head,router } from '@inertiajs/vue3';
import { computed, initCustomFormatter, onMounted, onUnmounted, ref } from 'vue';
import axios from 'axios';
import { useI18n } from 'vue-i18n';
import MailLayout from '@/Layouts/MailLayout.vue';

const createDialogVisible = ref(false);

const props = defineProps(['templates', 'from'])

const { t, locale } = useI18n();

const pageType = ref('inbox');

const searchForm = ref({});

const label = ref(t('other.new_message'));

const loading = ref(false);  // Loading status

const mails = ref({});

const folders = ref({});

const headers = ref({
    inbox: [
        {
            name: t('table.status'),
            value: "status"
        },
        {
            name: t('table.sender'),
            value: "sender"
        },
        {
            name: t('table.subject'),
            value: "subject"
        },
        {
            name: t('table.datetime'),
            value: "datetime"
        }
    ],
    sent: [
        {
            name: t('table.sender'),
            value: "sender"
        },
        {
            name: t('table.subject'),
            value: "subject"
        },
        {
            name: t('table.datetime'),
            value: "datetime"
        }
    ],
    trash: [
        {
            name: t('table.sender'),
            value: "sender"
        },
        {
            name: t('table.subject'),
            value: "subject"
        },
        {
            name: t('table.datetime'),
            value: "datetime"
        }
    ],
});
const countData = ref({})

const itemsPerPage = 10;

const selectedMail = ref(null);
const selectedHistories = ref({})
const selectedFolder = ref(null);
const threadLoading = ref(false);

const page = ref(1);

const totalPages = ref(1);

const isVisibleFloatButton = ref(false);

const handlePageChange = (newPage) => {
  page.value = newPage;
  fetchEmails(newPage, searchForm.value);
};

const handleRowSelected = (row) => {
  selectedMail.value = row;

  if(row?.status == 'replying')
  {
    cancelMailStatus(row?.id)
  }

  getHistories(row.id)
  if(row?.status == 'new')
  {
    markAsRead(row?.id)
  }
};

const removeRow = (row) => {
  selectedMail.value = null;
};

const fetchEmails = async () => {
  removeRow()

  loading.value = true;

  try {
    const response = await axios.get(`/mails/fetch`, {
      params: {
        page: page.value ?? 1,
        page_type: pageType.value,
        ...searchForm.value
      },
    });

    mails.value = response.data.data.data;
    folders.value = response.data.folders
    console.log(folders.value);
    totalPages.value = response.data.data.last_page;
    page.value = response.data.data.current_page;
    countData.value = response.data
  } catch (error) {
    console.error('Error fetching emails:', error);
  } finally {
    loading.value = false;
  }
};

const fetchEmailsWithFolderId = async () => {
  removeRow()

  loading.value = true;

  try {
    const response = await axios.get(`/mails/fetch/folder/` + selectedFolder.value, {
      params: {
        page: page.value ?? 1
      },
    });

    mails.value = response.data.data.data;
    folders.value = response.data.folders
    totalPages.value = response.data.data.last_page;
    page.value = response.data.data.current_page;
    countData.value = response.data
  } catch (error) {
    console.error('Error fetching emails:', error);
  } finally {
    loading.value = false;
  }
};



const markAsRead = async (id) => {
    try {
        const response = await axios.post(`/mails/mark-as-read/${id}`);
        if (response.data.status === 'success') {
            const mailItem = mails.value.find(mail => mail.id === id);
            console.log(mailItem);
            if (mailItem) {
                mailItem.status = 'read'; // Update status locally
            }
        }
    } catch (error) {
        console.error('Failed to mark as read:', error);
    }
};

const getHistories = async (id) => {

  if(pageType.value == 'inbox' || pageType.value == 'trash')
  {
    selectedHistories.value = {}
    threadLoading.value = true;

        try {
            const response = await axios.get(`/mails/histories/${id}`);
            if (response.data.status === 'success') {
                selectedHistories.value = response.data.data;
            }
        } catch (error) {
            console.error('Failed to get histories:', error);
        } finally {
            threadLoading.value = false;
        }
  }
};


const cancelMailStatus = (id) => {
  if(selectedMail.value.status != 'resolved' || selectedMail.value.status == 'confirmed')
  {
    axios
    .post(`/mails/cancel-status/${id}`)
    .then((response) => {
      console.log('Status canceled successfully', response.data);
    })
    .catch((error) => {
      console.error('Error canceling status', error);
    });
  }
};

const changeMailStatus = (id) => {
  axios
    .post(`/mails/change-reply/${id}`)
    .then((response) => {
      console.log('Status changed successfully', response.data);
    })
    .catch((error) => {
      console.error('Error changing status', error);
    });
};

const setPageType = (type, folder_id = null) => {
  pageType.value = type;
  mails.value = {}
  selectedFolder.value = folder_id
  page.value = 1

  console.log(selectedFolder.value)
  if(folder_id != null)
  {
    fetchEmailsWithFolderId()
  } else {
    fetchEmails()
  }
};

const goToSpam = () => {
  router.get('/spams');
};

onMounted(() => {
  fetchEmails()

  Echo.channel('mails')
    .listen('.mail-fetched', (event) => {
        console.log(event.mails);
        let result = event.mails;
        if(result?.new == 1 && page.value == 1 && pageType.value == 'inbox')
        {
            if(selectedFolder.value == null)
            {
                fetchEmails()
            } else {
                fetchEmailsWithFolderId()
            }
        }
    })
    .error((error) => {
      console.error('Broadcast error:', error);
    });

    Echo.channel('mail-status')
    .listen('.mail-status-changed', (event) => {
      console.log('Mail status changed:', event);
      const { mail_id, new_status, person_in_charge } = event;

      const mail = mails.value.find(mail => mail.id === mail_id);

      if (mail) {
        mail.status = new_status;
        if(selectedMail.value != null)
        {
            selectedMail.value.status = new_status
        }

        if(mail.status == 'resolved')
        {
            mail.person_in_charge = person_in_charge ?? ''
            if(selectedMail.value != null)
            {
                selectedMail.value.person_in_charge = person_in_charge ?? ''
            }
        }

      }

      console.log(`Mail ${mail_id} status changed to ${new_status}`);
    })
    .error((error) => {
      console.error('Broadcast error:', error);
    });
});

const handleSearch = (form) => {
    console.log(form)
    searchForm.value = form

    fetchEmails()
}

onUnmounted(() => {
    Echo.leaveChannel('mails');
    console.log('disconnected')
});
</script>

<template>
    <Head :title="$t('nav.home')" />

    <MailLayout @onSearch="handleSearch">
        <div class="bg-[#f2f4f6]">
            <div class="mx-auto sm:px-6 lg:px-5">
                <div
                    class="overflow-hidden sm:rounded-lg"
                >
                    <div class="py-6 text-gray-900" style="height: 100%;">
                        <VRow>
                            <VCol cols="12" lg="2">
                               <!-- Inbox -->
                                <div class="mb-5 cursor-pointer" @click="setPageType('inbox', null)">
                                    <p :class="{ 'active-route': pageType === 'inbox' }">
                                        {{ $t('nav.inbox') }} ( {{ countData?.inbox ?? 0 }} )
                                    </p>
                                </div>

                                <div v-for="folder in folders" :key="folder.id" class="ml-4">
                                    <!-- Top-level folder -->
                                    <div class="cursor-pointer" @click="setPageType('inbox', folder.id)" v-if="folder.mails_count > 0">
                                        <p :class="{ 'active-route': pageType === 'inbox' && folder.id === selectedFolder }">├{{ folder.name }} ( {{ folder.mails_count }} )</p>
                                    </div>
                                </div>

                                <!-- Sent Mail -->
                                <div class="my-10 cursor-pointer" @click="setPageType('sent', null)">
                                    <p :class="{ 'active-route': pageType === 'sent' }">
                                        {{ $t('nav.sent') }}  ( {{ countData?.sent ?? 0 }} )
                                     </p>
                                </div>

                                <!-- Draft Email -->
                                <!-- <div class="my-10 cursor-pointer" @click="setPageType('draft', null)">
                                    <p :class="{ 'active-route': pageType === 'draft' }">{{ $t('nav.draft') }} </p>
                                </div> -->

                                <!-- Spam: Navigates to Spam route -->
                                <div class="my-10 cursor-pointer" @click="goToSpam">
                                    <p :class="{ 'active-route': pageType === 'spam' }">{{$t('nav.spam')}}</p>
                                </div>

                                <!-- Trash Can -->
                                <div class="my-10 cursor-pointer" @click="setPageType('trash', null)">
                                    <p :class="{ 'active-route': pageType === 'trash' }">{{$t('nav.trash') }} ( {{ countData?.trash ?? 0 }} )</p>
                                </div>
                            </VCol>
                            <VCol cols="12" :lg="selectedMail ? 5 : 10"
                            >
                                <div class="mb-3">
                                <MailCreationDialog
                                    :label="label"
                                    @update:labelValue="label = $event"
                                    :createDialog="createDialogVisible"
                                    :floatButton="isVisibleFloatButton"
                                    @update:dialog="createDialogVisible = $event"
                                    @update:visibleFloat="isVisibleFloatButton = $event"
                                    :templates="props?.templates"
                                    :from="props?.from"
                                    @fetchMail="fetchEmails"
                                />
                                </div>

                                <div>
                                <VCard style="border-radius: 20px;">
                                    <MailTable :headers="headers[pageType]" :pageType="pageType" :loading="loading" :data="mails" @rowSelected="handleRowSelected" />
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
                                    <MailDetail
                                        @getThreads="getHistories"
                                        :pageType="pageType"
                                        :threads="selectedHistories"
                                        :threadLoading="threadLoading"
                                        :mail="selectedMail"
                                        @handleRemoveRow="removeRow"
                                        @fetchagain="fetchEmails"
                                        @changeMailStatus="changeMailStatus"
                                        @cancelMailStatus="cancelMailStatus"
                                    />
                                </VCard>
                            </VCol>
                        </VRow>
                    </div>
                </div>
            </div>
            <FloatMailButton
                :label="label"
                v-if="isVisibleFloatButton"
                @update:onOpenDialog="createDialogVisible = $event"
                @update:hideFloat="isVisibleFloatButton = $event"
            />
        </div>
    </MailLayout>
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
