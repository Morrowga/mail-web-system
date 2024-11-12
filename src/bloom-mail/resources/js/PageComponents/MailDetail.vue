<script setup>
import { ref } from 'vue';
import MailThread from './MailThread.vue';

const props = defineProps({
    mail: Object,
});

const emit = defineEmits();

const handleRemoveClick = () => {
  emit('handleRemoveRow');
};
</script>

<template>
    <VCardText>
        <div class="text-right cursor-pointer">
            <VIcon icon="mdi-close-box" @click="handleRemoveClick"></VIcon>
        </div>
        <VRow>
            <VCol cols="12" lg="2">
                <VIcon icon="mdi-trash-can-outline" />
            </VCol>
            <VCol cols="12" lg="6">
                <div class="icon-container">
                <div class="icon-wrapper">
                    <VIcon icon="mdi-arrow-left-top" class="icon-size" />
                    <div class="underline"></div>
                </div>
                <div class="icon-wrapper">
                    <VIcon icon="mdi-sync" class="icon-size" />
                    <div class="underline"></div>
                </div>
                <div class="icon-wrapper">
                    <VIcon icon="mdi-arrow-right-top" class="icon-size" />
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
        <div class="my-4 mx-3">
            <p class="mail-subject">{{ props?.mail?.subject }}</p>
            <div class="my-2">
                <p>{{ props?.mail?.name }}</p>
                <p >
                    {{ props?.mail?.from }}
                </p>
                <div class="d-flex justify-between">
                    <p>
                        Attn:
                        <br>
                        <br>
                        <span v-html="props?.mail?.body" style="white-space: pre-wrap;   word-break: break-word; overflow-wrap: break-word;"></span>
                    </p>
                    <div>
                        <VBtn prepend-icon="mdi-triangle-down" style="background-color: transparent; border: 2px solid #000; box-shadow: none;">{{ props?.mail?.status }}</VBtn>
                    </div>
                </div>
            </div>
            <hr style="opacity: 0.3;">

            <MailThread v-for="reply in props.mail.all_replies" :key="reply.id" :reply="reply" />
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
