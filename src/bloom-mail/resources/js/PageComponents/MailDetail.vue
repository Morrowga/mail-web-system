<script setup>
import { onMounted, onUnmounted, ref, watch } from 'vue';
import MailThread from './MailThread.vue';
import ReplyForwardDialog from './ReplyForwardDialog.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import MailConfirmDialog from './MailConfirmDialog.vue';
import { useI18n } from 'vue-i18n';
import { getTranslatedStatus } from '@/Helper/status';
import axios from 'axios';
import { permissionGrant } from '@/Helper/permissionUtils';

const props = defineProps({
    mail: Object,
    threads: Array,
    threadLoading: Boolean,
    updateThreadLoading: Boolean,
    replyDialogVisible: Boolean,
    pageType: String,
    isVisibleReplyFloatButton: Boolean
});

const confirmDialog = ref(false);

console.log(props?.pageType)

const { t, locale } = useI18n();

const floatVisibility = ref(props.isVisibleReplyFloatButton)

const createDialogVisible = ref(props.replyDialogVisible);

watch(
    () => props.replyDialogVisible,
    (newValue) => {
        createDialogVisible.value = newValue;
    }
);

watch(
    createDialogVisible,
    (newValue) => {
        emit('update:replyDialog', newValue);
    }
);

const page = usePage();

const permissions = page?.props?.auth?.user?.permissions

const emit = defineEmits();

const mailType = ref(null);

const statusOptions = ref([
    {
    name: t('table.' + props?.mail?.status),
    value: props?.mail?.status
  },
  {
    name: t('table.under_review'),
    value: "under_review"
  },
  {
    name: t('table.pending'),
    value: "pending"
  },
  {
    name: t('table.resolved'),
    value: "resolved"
  },
  {
    name: t('table.confirmed'),
    value: "confirmed"
  }
]);

const selectedStatus = ref(t('table.' + props?.mail?.status) || '');
const selectedConfirmType = ref('delete');

const form = useForm({});

const openDialog = (type) => {
    if (type === 'reply' && props?.mail?.status !== 'resolved' && props?.mail?.status !== 'confirmed') {
        emit('changeMailStatus', props?.mail?.id);
    }

    mailType.value = type;
    createDialogVisible.value = true;
}

const cancelStatus = () => {
    emit('cancelMailStatus', props?.mail?.id)
}

const handleRemoveClick = () => {
  emit('handleRemoveRow');
};

const loadThread = (id) => {
    emit('getThreads', id)
}

const updateThread = (id) => {
    emit('updateThreads', id)
}

const openConfirmDialog = (type) => {
    selectedConfirmType.value = type;
    confirmDialog.value = true
}

watch(() => props?.mail?.status, (newStatus) => {
  selectedStatus.value = t('table.' + newStatus);
});

const handleDelete = async () => {
    try {
        let url;

        if (props?.pageType == 'inbox' || props?.pageType == 'inbox_folder') {
            url = `/mails/delete/${props?.mail?.id}`;
        } else if (props?.pageType == 'trash') {
            url = `/mails/delete-forever/${props?.mail?.id}`;
        } else {
            url = `/mails/sent/delete/${props?.mail?.id}`;
        }

        form.delete(url, {
            onSuccess: () => {
                emit('handleRemoveRow');
                emit('fetchagain');
            },
            onError: (error) => {
                console.error("Form submission error:", error);
            },
        });
    } catch (error) {
        console.error('Failed to mark as read:', error);
    }
}

const handleRedo = async () => {
    try {
        let url = `/mails/redo/${props?.mail?.id}`

        form.post(url, {
            onSuccess: () => {
                emit('handleRemoveRow');
                emit('fetchagain');
            },
            onError: (error) => {
                console.error("Form submission error:", error);
            },
        });
    } catch (error) {
        console.error('Failed to mark as read:', error);
    }
}


const changeStatus = () => {
  axios
    .post(`/mails/change-status/${props?.mail?.id}`,
        {
            status: selectedStatus.value,
        }
    )
    .then((response) => {
      console.log('Status confirmed successfully', response.data);
    })
    .catch((error) => {
      console.error('Error canceling status', error);
    });
};

const handleStatusChange = () => {
    changeStatus()
};


const minimizeReply = () => {
    emit('replyMinimize')
    createDialogVisible.value = false
};

</script>

<template>
    <VCardText>
        <div class="text-right cursor-pointer">
            <VIcon icon="mdi-close-box" @click="handleRemoveClick"></VIcon>
        </div>
        <VRow>
            <VCol cols="12" lg="2">
                <VIcon icon="mdi-trash-can-outline" v-if="permissionGrant(permissions, 'mail_delete')" @click="openConfirmDialog('delete')" />
                <VIcon icon="mdi-redo" class="ml-2" v-if="props?.pageType == 'trash'"@click="openConfirmDialog('redo')" />
            </VCol>
            <VCol cols="12" lg="6" v-if="props?.pageType == 'inbox' || props?.pageType == 'inbox_folder'">
                <div class="icon-container">
                <div class="icon-wrapper" v-if="permissionGrant(permissions, 'mail_reply')">
                    <VIcon icon="mdi-arrow-left-top" @click="openDialog('reply')" class="icon-size cursor-pointer" />
                    <div class="underline"></div>
                </div>
                <div class="icon-wrapper">
                    <VIcon icon="mdi-sync" class="icon-size" @click="updateThread(props?.mail?.id)" />
                    <div class="underline"></div>
                </div>
                <div class="icon-wrapper" v-if="permissionGrant(permissions, 'mail_forward')">
                    <VIcon icon="mdi-arrow-right-top" @click="openDialog('forward')" class="icon-size cursor-pointer" />
                    <div class="underline"></div>
                </div>
                </div>
            </VCol>
            <VCol cols="12" lg="4">
                <div>
                    <p>{{ props?.mail?.datetime }}</p>
                </div>
            </VCol>
        </VRow>
        <div class="my-4 mx-3" style="height: 70vh; overflow-y: auto;">
            <p class="mail-subject">{{ props?.mail?.subject }}</p>
            <div class="my-2">

                <div class="d-flex justify-between">
                    <div>
                        <p>{{ props?.mail?.name }}</p>
                        <p >
                            {{ props?.mail?.sender }}
                        </p>
                        <p v-if="props?.mail?.status == 'resolved'" class="my-3">
                            {{ $t('input.person_in_charge_text') }}: {{ props?.mail?.person_in_charge ?? '' }}
                        </p>
                    </div>
                    <div class="mb-2" v-if="(props?.pageType == 'inbox' || props?.pageType == 'inbox_folder') && props?.mail?.status != 'confirmed'">
                        <VSelect
                        v-model="selectedStatus"
                        :items="statusOptions"
                        @update:modelValue="handleStatusChange"
                        variant="outlined"
                        item-title="name"
                        item-value="value"
                        density="compact"
                        required
                        hide-details
                        >
                        </VSelect>
                    </div>
                    <div class="mb-2" v-if="(props?.pageType == 'inbox' || props?.pageType == 'inbox_folder') && props?.mail?.status == 'confirmed'">
                        <VBtn prepend-icon="mdi-triangle-down" style="background-color: transparent; border: 2px solid #000; box-shadow: none;">
                            {{ getTranslatedStatus(t, props?.mail?.status) }}
                        </VBtn>
                    </div>
                </div>
                <p v-if="props?.pageType == 'sent'" class="my-3">
                    <span v-html="props?.mail?.body" style="white-space: pre-wrap; word-break: break-word; overflow-wrap: break-word;"></span>
                </p>
            </div>
            <hr style="opacity: 0.3;" v-if="props?.pageType != 'sent'">
            <div v-if="threadLoading" class="loading-overlay d-flex justify-center my-5">
                <v-progress-circular indeterminate color="blue"></v-progress-circular>
            </div>
            <div v-if="['inbox', 'trash', 'inbox_folder'].includes(props?.pageType)">
                <MailThread v-for="reply in props?.threads" :key="reply.id" :reply="reply" />
            </div>
            <div v-if="props?.updateThreadLoading">
                <v-skeleton-loader
                :loading="true"
                type="list-item-two-line"
                >
                <v-list-item
                    lines="two"
                    subtitle="Subtitle"
                    title="Title"
                    rounded
                ></v-list-item>
                </v-skeleton-loader>
            </div>
            <ReplyForwardDialog
                :createDialog="createDialogVisible"
                @update:dialog="createDialogVisible = $event"
                :type="mailType"
                @update:visibleFloat="floatVisibility = $event"
                @update:mailTypeEevent="mailType = $event"
                :mailData="props?.mail"
                @cancelStatus="cancelStatus"
                @minimizeReply="minimizeReply"
                @handleLoadThread="updateThread(props?.mail?.id)"
                :threads="props?.threads"
                :from="props?.from"
            />
            <MailConfirmDialog @handleDelete="handleDelete" :pageType="props?.pageType" @handleRedo="handleRedo" :selectedConfirmType="selectedConfirmType"  :confirmDialog="confirmDialog" @update:dialog="confirmDialog = $event" />
        </div>
    </VCardText>
</template>

<style scoped>
.icon-container
{
  display: flex; /* Aligns icons in a row */
  justify-content: flex-start; /* Aligns items to the start without extra space */
  align-items: center; /* Centers icons vertically */
}

.icon-wrapper
{
  text-align: center; /* Centers icon and underline */
  margin: 0; /* Remove margins around the icon wrapper */
  padding-left: 8px;
  padding-right: 8px;
  padding-top: 5px;
}

.underline
{
  height: 2px; /* Thickness of the underline */
  background-color: rgb(0,0,0,0.3); /* Color of the underline */
  margin-top: 0; /* No space between icon and underline */
  width: 100%; /* Underline width */
}

.icon-size
{
    font-size: 27px;
    font-weight: medium;
    opacity: 0.4;
    color: #000;
}

.mail-subject{
    font-weight: bold;
    font-size: 15px;
}
</style>
