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
import { permissionGrant } from '@/Helper/permissionUtils';
import FloatReplyButton from '@/PageComponents/FloatReplyButton.vue';

const createDialogVisible = ref(false);

const connectionStatus = ref("Connecting to WebSocket...");

const connectionFlag = ref(false)

const connectionColor = ref('gray');

const props = defineProps(['templates', 'from', 'auth'])

const isVisibleReplyFloatButton = ref(false);

const replyDialogVisible = ref(false);

const { t, locale } = useI18n();

const pageType = ref('inbox');

const searchForm = ref({});

const label = ref(t('other.new_message'));

const loading = ref(false);  // Loading status

const mails = ref({});

const folders = ref({});

const permissions = props?.auth?.user?.permissions

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
    inbox_folder: [
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
const updateThreadLoading = ref(false);
const currentTime = new Date().getTime();
const floatMails = ref([]);
const newFloatMails = ref([])
const selectedNewMail = ref(null)

const page = ref(1);

const totalPages = ref(1);

const isVisibleFloatButton = ref(false);

const handlePageChange = (newPage) => {
  page.value = newPage;
  if(pageType.value == 'inbox_folder')
  {
    fetchEmailsWithFolderId()
  } else {
    fetchEmails(newPage, searchForm.value);
  }

};

const handleRowSelected = (row) => {
  selectedMail.value = row;

//   if(row?.status == 'replying')
//   {
//     cancelMailStatus(row?.id)
//   }

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
//   removeRow()

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
//   removeRow()

  loading.value = true;

  try {
    const response = await axios.get(`/mails/fetch/folder/` + selectedFolder.value, {
      params: {
        page: page.value ?? 1,
        page_type: pageType.value,
        ...searchForm.value
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
    return;
    // try {
    //     const response = await axios.post(`/mails/mark-as-read/${id}`);
    // } catch (error) {
    //     console.error('Failed to mark as read:', error);
    // }
};

const getHistories = async (id) => {
  const storageKey = `histories_${id}`;

  const storedData = JSON.parse(localStorage.getItem(storageKey));

  if (storedData && (currentTime - storedData.timestamp) < 3600000) {
    selectedHistories.value = storedData.data;

    console.log(selectedHistories.value)

    updateHistories(id);
  } else {
    // If no data in localStorage or data is expired, fetch from backend
    if (['inbox', 'trash', 'inbox_folder'].includes(pageType.value)) {
        selectedHistories.value = {};
      threadLoading.value = true;

      try {
        const response = await axios.get(`/mails/histories/${id}`);

        if (response.data.status === 'success') {
          selectedHistories.value = response.data.data;

            console.log(selectedHistories.value)
          // Store the response in localStorage with a timestamp
          const dataToStore = {
            data: response.data.data,
            timestamp: currentTime,  // Store the current timestamp
          };
          localStorage.setItem(storageKey, JSON.stringify(dataToStore));

          console.log('Stored histories in localStorage');
        }
      } catch (error) {
        console.error('Failed to get histories:', error);
      } finally {
        threadLoading.value = false;
      }
    }
  }
};

const updateHistories = async (id) => {
    const storageKey = `histories_${id}`;

    if (['inbox', 'trash', 'inbox_folder'].includes(pageType.value)) {
        updateThreadLoading.value = true;

      try {
        const response = await axios.get(`/mails/histories/${id}`);

        if (response.data.status === 'success') {
          selectedHistories.value = response.data.data;

          // Store the response in localStorage with a timestamp
          const dataToStore = {
            data: response.data.data,
            timestamp: currentTime,  // Store the current timestamp
          };
          localStorage.setItem(storageKey, JSON.stringify(dataToStore));

          console.log('Stored histories in localStorage');
        }
      } catch (error) {
        console.error('Failed to get histories:', error);
      } finally {
        updateThreadLoading.value = false;
      }
    }
};

const cancelMailStatus = (id) => {
    console.log(id);
  if(selectedMail.value.status != 'resolved' || selectedMail.value.status == 'confirmed')
  {
    axios
    .post(`/mails/cancel-status/${id}`)
    .then((response) => {
        const mail = mails.value.find(mail => mail.id === selectedMail.value.id);

        mail.status = response.data.status
        selectedMail.value.status = response.data.status

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
        const mail = mails.value.find(mail => mail.id === selectedMail.value.id);

        mail.status = response.data.status
        selectedMail.value.status = response.data.status

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

  if(type == 'inbox_folder')
  {
    fetchEmailsWithFolderId()
  } else {
    fetchEmails()
  }
};

const goToSpam = () => {
  router.get('/spams');
};

function checkInternetConnection() {
    if (navigator.onLine) {
        connectionStatus.value = 'Connected to the internet...';
        connectionColor.value = 'green';

        setTimeout(() => {
            connectionFlag.value = true;
        }, 3000);

        console.log('Connected to the internet');
    } else {
        connectionStatus.value = `No internet connection... Retrying`;
        connectionFlag.value = false;
        connectionColor.value = 'red';
        console.log('No internet connection');
    }
}

window.addEventListener('online', () => {
    console.log('Browser went online');
    checkInternetConnection();
});

window.addEventListener('offline', () => {
    console.log('Browser went offline');
    checkInternetConnection();
});

onMounted(() => {
  let storedMails = JSON.parse(localStorage.getItem('newFloatMails')) || [];
  let replyStoredMails = JSON.parse(localStorage.getItem('floatMails')) || [];

  newFloatMails.value = storedMails;
  floatMails.value = replyStoredMails;

  isVisibleFloatButton.value = newFloatMails.value.length > 0 ? true : false;
  isVisibleReplyFloatButton.value = floatMails.value.length > 0 ? true : false;

  fetchEmails()

  if (Echo.connector.pusher) {
        const pusher = Echo.connector.pusher;
        console.log(pusher.connection.state)

        if(pusher.connection.state == 'connected')
        {
            connectionStatus.value = 'Connected to WebSocket';
            connectionColor.value = 'green';

            setTimeout(() => {
                connectionFlag.value = true;
                checkInternetConnection();
            }, 3000);

        } else if(pusher.connection.state == 'disconnected')
        {
            connectionStatus.value = 'Disconnected from WebSocket';
            connectionFlag.value = false;
            connectionColor.value = 'red'

        } else if(pusher.connection.state == 'connecting')
        {
            connectionStatus.value = 'Connecting to WebSocket...';
            connectionFlag.value = true;
            connectionColor.value = 'gray'
        }
    }

  Echo.channel('mails')
    .listen('.mail-fetched', (event) => {
        console.log(event.mails);
        let result = event.mails;
        if(result?.new == 1 && page.value == 1 && (pageType.value == 'inbox'  || pageType.value == 'inbox_folder' ))
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
      const { mail_id, new_status, person_in_charge, count_data, folders_data} = event;

      const mail = mails.value.find(mail => mail.id === mail_id);

      if (mail) {
        mail.status = new_status;
        if(selectedMail.value != null)
        {
            if(selectedMail.value.id == mail_id)
            {
                selectedMail.value.status = new_status
            }
        }

        if(mail.status == 'resolved')
        {
            mail.person_in_charge = person_in_charge ?? ''
            if(selectedMail.value != null)
            {
                selectedMail.value.person_in_charge = person_in_charge ?? ''
            }
        }

        countData.value.inbox = count_data
        folders.value = folders_data

        console.log(folders.value);
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
    if ((form.folder_id == null || form.folder_id == '') && pageType.value == 'inbox')
    {
        console.log('searching all')

        fetchEmails()
    } else {
        console.log('searching with folder id')
        setPageType('inbox_folder', form.folder_id ? form.folder_id : selectedFolder.value)

        fetchEmailsWithFolderId()
    }
}

const changeNewMailValue = (id) => {
    const mail = newFloatMails.value.find(mail => mail.id === id); // Find the mail with the given id
    if (mail) {
        selectedNewMail.value = mail; // Set the selected mail to the found item
    }
}

const pushValueToNewEmails = (item) => {
    let storedMails = JSON.parse(localStorage.getItem('newFloatMails')) || [];

    const index = storedMails.findIndex(mail => mail.id === item.id);

    if (index !== -1) {
        storedMails[index] = item;
    } else {
        storedMails.push(item);
    }

    if (storedMails.length > 5) {
        storedMails.shift();
    }

    localStorage.setItem('newFloatMails', JSON.stringify(storedMails));

    newFloatMails.value = storedMails;

    console.log(newFloatMails.value);
}

const changeMailDetail = (item) => {
    selectedMail.value = item
    replyDialogVisible.value = true
}

const replyMinimize = () => {
    if (!floatMails.value.some(mail => mail.id === selectedMail.value.id)) {
        const storedMails = JSON.parse(localStorage.getItem('floatMails')) || [];
        storedMails.push(selectedMail.value);

        if (storedMails.length > 5) {
            storedMails.shift();
        }

        localStorage.setItem('floatMails', JSON.stringify(storedMails));

        floatMails.value = storedMails;
    }

    isVisibleReplyFloatButton.value = true;
};

const removeNewMail = (id) => {
    const index = newFloatMails.value.findIndex(mail => mail.id === id);

    if (index !== -1) {
        newFloatMails.value.splice(index, 1);

        if (selectedNewMail.value && selectedNewMail.value.id === id) {
            selectedNewMail.value = null;
        }

        let storedMails = JSON.parse(localStorage.getItem('newFloatMails')) || [];

        storedMails = storedMails.filter(mail => mail.id !== id);

        localStorage.setItem('newFloatMails', JSON.stringify(storedMails));
    }
};

const removeAllNewMail = () => {
    console.log('Removing all new mails');
    newFloatMails.value = [];
    selectedNewMail.value = null;
    localStorage.removeItem('newFloatMails');
}

const removeAllReplyMail = () => {
    floatMails.value = [];
    selectedMail.value = null;
    localStorage.removeItem('floatMails');
}

const updateStatus = (id, status) => {
    const mail = mails.value.find(mail => mail.id === id);

    mail.status = status
    selectedMail.value.status = status
}

const setNullToSelectedNewMail = () => {
    selectedNewMail.value = null
}
const removeMail = (item) => {
    const index = floatMails.value.findIndex(mail => mail.id === item.id);

    if (index !== -1) {
        floatMails.value.splice(index, 1);

        const storedMails = JSON.parse(localStorage.getItem('floatMails')) || [];
        const updatedMails = storedMails.filter(mail => mail.id !== item.id);

        localStorage.setItem('floatMails', JSON.stringify(updatedMails));
    }
};


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
                            <VCol cols="12" lg="2"  class="sticky-left">
                               <!-- Inbox -->
                                <div class="mb-5 cursor-pointer" @click="setPageType('inbox', null)">
                                    <p :class="{ 'active-route': pageType === 'inbox' }">
                                        {{ $t('nav.inbox') }} ({{ countData?.inbox ?? 0 }})
                                    </p>
                                </div>

                                <div v-for="folder in folders" :key="folder.id" class="ml-4">
                                    <!-- Top-level folder -->
                                    <div class="cursor-pointer" @click="setPageType('inbox_folder', folder.id)">
                                        <p :class="{ 'active-route': pageType === 'inbox_folder' && folder.id === selectedFolder }">├{{ folder.name }} ({{ folder.mails_count ?? 0 }})</p>
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
                                <div class="my-10 cursor-pointer" @click="goToSpam" v-if="permissionGrant(permissions, 'spam_read')">
                                    <p :class="{ 'active-route': pageType === 'spam' }">{{$t('nav.spam')}}</p>
                                </div>

                                <!-- Trash Can -->
                                <div class="my-10 cursor-pointer" @click="setPageType('trash', null)">
                                    <p :class="{ 'active-route': pageType === 'trash' }">{{$t('nav.trash') }} ( {{ countData?.trash ?? 0 }} )</p>
                                </div>
                            </VCol>
                            <VCol cols="12" class="scrollable-column" :lg="selectedMail ? 5 : 10"
                            >
                                <div class="d-flex justify-start">
                                    <div class="mb-3" v-if="permissionGrant(permissions, 'mail_create')">
                                    <MailCreationDialog
                                        :label="label"
                                        @update:labelValue="label = $event"
                                        :createDialog="createDialogVisible"
                                        :selectedNewMail="selectedNewMail"
                                        :floatButton="isVisibleFloatButton"
                                        @setNullToSelectedNewMail="setNullToSelectedNewMail"
                                        @update:dialog="createDialogVisible = $event"
                                        @update:visibleFloat="isVisibleFloatButton = $event"
                                        @pushValueToNewEmails="pushValueToNewEmails"
                                        :templates="props?.templates"
                                        :from="props?.from"
                                        @fetchMail="fetchEmails"
                                    />
                                    </div>
                                    <div class="d-flex align-center mx-3" v-if="!connectionFlag">
                                        <v-progress-circular
                                        indeterminate
                                        color="primary"
                                        size="20"
                                        class="mr-2"
                                        ></v-progress-circular>
                                        <span :style="'color:' + connectionColor + '; font-style: italic;'">{{ connectionStatus }}</span>
                                    </div>
                                </div>

                                <div>
                                <VCard style="border-radius: 20px;">
                                    <MailTable :headers="headers[pageType]" :mail="selectedMail" :pageType="pageType" :loading="loading" :data="mails" @rowSelected="handleRowSelected" />
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
                                class="sticky-right"
                            >
                                <VCard class="mt-5" style="border-radius: 20px;">
                                    <MailDetail
                                        @getThreads="getHistories"
                                        @updateThreads="updateHistories"
                                        :pageType="pageType"
                                        :isVisibleReplyFloatButton="isVisibleReplyFloatButton"
                                        @update:hideFloat="isVisibleReplyFloatButton = $event"
                                        :replyDialogVisible="replyDialogVisible"
                                        @update:replyDialog="replyDialogVisible = $event"
                                        :threads="selectedHistories"
                                        :threadLoading="threadLoading"
                                        :updateThreadLoading="updateThreadLoading"
                                        :mail="selectedMail"
                                        @updateStatus="updateStatus"
                                        @replyMinimize="replyMinimize"
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
                v-if="isVisibleFloatButton && newFloatMails.length > 0"
                @update:onOpenDialog="createDialogVisible = $event"
                @update:hideFloat="isVisibleFloatButton = $event"
                :newFloatMails="newFloatMails"
                @removeAllNewMail="removeAllNewMail"
                @removeNewMail="removeNewMail"
                @changeNewMailValue="changeNewMailValue"
            />

            <FloatReplyButton
                :label="'Replies'"
                :floatMails="floatMails"
                v-if="isVisibleReplyFloatButton && floatMails.length > 0"
                @changeMailDetail="changeMailDetail"
                @removeMail="removeMail"
                @handleRowSelected="handleRowSelected"
                @removeAllReplyMail="removeAllReplyMail"
                @cancelMailStatus="cancelMailStatus"
                @update:hideFloat="isVisibleReplyFloatButton = $event"
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

.sticky-left {
    position: sticky;
    top: 0; /* Fix it at the top of the container */
    z-index: 10; /* Ensure it stays above other content */
}

/* Make the third column (Mail Detail) sticky */
.sticky-right {
    position: sticky;
    top: 0; /* Fix it at the top of the container */
    z-index: 10; /* Ensure it stays above other content */
}

/* Allow second column to scroll */
.scrollable-column {
    overflow-y: auto;
    max-height: calc(100vh - 20px); /* Make sure to adjust to your specific height requirements */
}
</style>
