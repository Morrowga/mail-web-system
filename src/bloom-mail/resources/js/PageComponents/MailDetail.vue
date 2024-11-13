<script setup>
import { onUnmounted, ref } from 'vue';
import MailThread from './MailThread.vue';
import ReplyForwardDialog from './ReplyForwardDialog.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import MailConfirmDialog from './MailConfirmDialog.vue';
import { useI18n } from 'vue-i18n';
import { getTranslatedStatus } from '@/Helper/status';
import axios from 'axios';

const props = defineProps({
    mail: Object,
    threads: Array,
    threadLoading: Boolean,
    pageType: String
});

const confirmDialog = ref(false);

const { t, locale } = useI18n();

const emit = defineEmits();

const mailType = ref(null);
const form = useForm({});

const createDialogVisible = ref(false);

const openDialog = (type) => {
    emit('changeMailStatus', props?.mail?.id)

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

const openConfirmDialog = () => {
    confirmDialog.value = true
}

const handleDelete = async () => {
    try {
        let url = props?.pageType == 'inbox' ? `/mails/delete/${props?.mail?.id}` : `/mails/sent/delete/${props?.mail?.id}`;

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


</script>

<template>
    <VCardText>
        <div class="text-right cursor-pointer">
            <VIcon icon="mdi-close-box" @click="handleRemoveClick"></VIcon>
        </div>
        <VRow>
            <VCol cols="12" lg="2">
                <VIcon icon="mdi-trash-can-outline" @click="openConfirmDialog()" />
            </VCol>
            <VCol cols="12" lg="6" v-if="props?.pageType == 'inbox'">
                <div class="icon-container">
                <div class="icon-wrapper">
                    <VIcon icon="mdi-arrow-left-top" @click="openDialog('reply')" class="icon-size cursor-pointer" />
                    <div class="underline"></div>
                </div>
                <div class="icon-wrapper">
                    <VIcon icon="mdi-sync" class="icon-size" @click="loadThread(props?.mail?.id)" />
                    <div class="underline"></div>
                </div>
                <div class="icon-wrapper">
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
                            {{ props?.mail?.from }}
                        </p>
                    </div>
                    <div class="mb-2">
                        <VBtn v-if="props?.pageType == 'inbox'" prepend-icon="mdi-triangle-down" style="background-color: transparent; border: 2px solid #000; box-shadow: none;">
                            {{ getTranslatedStatus(t, props?.mail?.status) }}
                        </VBtn>
                    </div>
                </div>
                <p v-if="props?.pageType == 'sent'" class="my-3">
                    Attn:
                    <br>
                    <br>
                    <span v-html="props?.mail?.body" style="white-space: pre-wrap;   word-break: break-word; overflow-wrap: break-word;"></span>
                </p>
            </div>
            <hr style="opacity: 0.3;">
            <div v-if="threadLoading" class="loading-overlay d-flex justify-center my-5">
                <v-progress-circular indeterminate color="blue"></v-progress-circular>
            </div>
            <div v-if="props?.pageType == 'inbox'">
                <MailThread v-for="reply in props?.threads" :key="reply.id" :reply="reply" />
            </div>
            <ReplyForwardDialog
                :createDialog="createDialogVisible"
                @update:dialog="createDialogVisible = $event"
                :type="mailType"
                :mailData="props?.mail"
                @cancelStatus="cancelStatus"
                @handleLoadThread="loadThread(props?.mail?.id)"
                :threads="props?.threads"
                :from="props?.from"
            />
            <MailConfirmDialog @handleDelete="handleDelete" :confirmDialog="confirmDialog" @update:dialog="confirmDialog = $event" />
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
