<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
createDialog: Boolean,
floatButton: Boolean
});
const dialog = ref(props.createDialog);

const emit = defineEmits(['update:dialog', 'update:visibleFloat']);

watch(() => props.createDialog, (newVal) => {
    dialog.value = newVal;
});

const minimizeDialog = () => {
    dialog.value = false;
    emit('update:visibleFloat', true);
    emit('update:dialog', false);
};

const onClose = () => {
    dialog.value = false;
    emit('update:dialog', false);
    emit('update:visibleFloat', false);
}

const onOpen = () => {
    dialog.value = true;
    emit('update:visibleFloat', false);
    emit('update:dialog', true);
}

const form = useForm({
    subject: "",
    from: "",
    to: "",
    template: "Texas",
    cc: "",
    bcc: "",
    message_content: ""
})
</script>
<template>
    <div class="text-left">
        <VBtn
            prepend-icon="mdi-email-outline"
            color="primary"
           @click="onOpen"
        >
            Mail Box
        </VBtn>
    </div>

    <VDialog v-model="dialog" max-width="1250">
        <VCard>
        <!-- Card Header with Icons -->
        <VCardTitle class="d-flex justify-end align-center">
            <VSpacer></VSpacer>

            <!-- Minimize Icon -->
            <div class="icon-border d-flex justify-center align-items-center">
                <VIcon
                    icon="mdi-minus"
                    class="minimize-icon"
                    style="color: #a5a5a5; font-weight: bold;"
                    @click="minimizeDialog"
                ></VIcon>
            </div>

            <!-- Close Icon -->
             <div class="icon-border text-center">
                <VIcon
                    icon="mdi-close"
                    class="close-icon"
                    style="color: #a5a5a5; font-weight: bold;"
                    @click="onClose"
                ></VIcon>
             </div>
        </VCardTitle>

        <!-- Card Content -->
        <VCardText>
            <VRow dense>
                <VCol cols="12" md="12" sm="6">
                    <VDivider />
                    <div class="pb-2 pt-1 text-right">
                        <VBtn class="spamtrashbtn">Spam</VBtn>
                        <VBtn class="mx-2 spamtrashbtn">Trash</VBtn>
                    </div>
                    <VDivider />
                </VCol>
                <VCol cols="12" md="12" sm="6">
                    <VRow>
                        <VCol cols="8">
                            <div class="d-flex justify-between align-items-center" style="height: 100%;">
                                <div style="width: 10%; align-self: flex-end;">
                                    <InputLabel value="Subject" for="subject" />
                                </div>
                                <div style="width: 90%;">
                                    <VTextField
                                        variant="plain"
                                        density="compact"
                                        required
                                        v-model="form.subject"
                                        hide-details
                                    ></VTextField>
                                </div>
                                <InputError class="mt-1" :message="form.errors.subject" />
                            </div>
                        </VCol>
                        <VCol cols="4">
                            <div class="d-flex justify-between align-items-center" style="height: 100%;">
                                <div style="width: 50%; align-self: flex-end;">
                                    <span class="font-bold">Sent date & time</span>
                                </div>
                                <div style="width: 50%; align-self: flex-end;">
                                    2024/08/23 13:37:10
                                </div>
                            </div>
                        </VCol>
                    </VRow>
                    <VDivider class="mt-1" />
                    <VRow>
                        <VCol cols="8">
                            <div class="d-flex justify-between align-items-center" style="height: 100%;">
                                <div style="width: 10%; align-self: flex-end;">
                                    <InputLabel value="From" for="from" />
                                </div>
                                <div style="width: 90%;">
                                    <VTextField
                                        v-model="form.from"
                                        variant="plain"
                                        density="compact"
                                        required
                                        hide-details
                                    ></VTextField>
                                </div>
                                <InputError class="mt-1" :message="form.errors.from" />
                            </div>
                        </VCol>
                        <VCol cols="4">
                            <div class="d-flex justify-between align-items-center" style="height: 100%;">
                                <div style="width: 50%; align-self: flex-end;">
                                    <span class="font-bold">Person in charge</span>
                                </div>
                                <div style="width: 50%; align-self: flex-end;">
                                    ちは
                                </div>
                            </div>
                        </VCol>
                    </VRow>
                    <VDivider class="mt-1" />
                    <VRow>
                        <VCol cols="8">
                            <div class="d-flex justify-between align-items-center" style="height: 100%;">
                                <div style="width: 10%; align-self: flex-end;">
                                    <InputLabel value="To" for="to" />
                                </div>
                                <div style="width: 90%;">
                                    <VTextField
                                        variant="plain"
                                        density="compact"
                                        required
                                        hide-details
                                        v-model="form.to"
                                    ></VTextField>
                                </div>
                                <InputError class="mt-1" :message="form.errors.to" />
                            </div>
                        </VCol>
                        <VCol cols="4">
                            <div class="d-flex justify-between align-items-center" style="height: 100%;">
                                <div style="width: 50%; align-self: flex-end;">
                                    <span class="font-bold">Status</span>
                                </div>
                                <div style="width: 50%; align-self: flex-end;">
                                    ちは
                                </div>
                            </div>
                        </VCol>
                    </VRow>
                    <VRow >
                        <VCheckbox label="Attach the original email" reverse hide-details></VCheckbox>
                    </VRow>
                    <VDivider class="mt-2" />
                    <VRow>
                        <VCol cols="8" class="mt-3">
                            <div class="d-flex justify-between align-items-center" style="height: 100%; align-items: center;">
                                <div style="width: 10%; align-items: center;">
                                    <InputLabel value="Template" for="template" />
                                </div>
                                <div style="width: 90%;">
                                    <VSelect
                                    v-model="form.template"
                                    variant="outlined" density="compact" required hide-details
                                    :items="['California', 'Colorado', 'Florida', 'Georgia', 'Texas', 'Wyoming']"
                                    ></VSelect>
                                </div>
                                <InputError class="mt-1" :message="form.errors.template" />
                            </div>
                        </VCol>
                    </VRow>
                    <VDivider class="mt-2" />
                    <VRow>
                        <VCol cols="8" class="mt-3">
                            <div class="d-flex justify-between align-items-center" style="height: 100%; align-items: center;">
                                <div style="width: 10%; align-items: center;">
                                    <InputLabel value="To" for="to" />
                                </div>
                                <div style="width: 90%;">
                                    <VTextField
                                    v-model="form.to"
                                    variant="outlined" density="compact" required hide-details
                                    ></VTextField>
                                </div>
                                <InputError class="mt-1" :message="form.errors.to" />
                            </div>
                        </VCol>
                    </VRow>
                    <VDivider class="mt-2" />
                    <VRow>
                        <VCol cols="8" class="mt-3">
                            <div class="d-flex justify-between align-items-center" style="height: 100%; align-items: center;">
                                <div style="width: 10%; align-items: center;">
                                    <InputLabel value="CC" for="cc" />
                                </div>
                                <div style="width: 90%;">
                                    <VTextField
                                    v-model="form.cc"
                                    variant="outlined" density="compact" required hide-details
                                    ></VTextField>
                                </div>
                                <InputError class="mt-1" :message="form.errors.cc" />
                            </div>
                        </VCol>
                    </VRow>
                    <VDivider class="mt-2" />
                    <VRow>
                        <VCol cols="8" class="mt-3">
                            <div class="d-flex justify-between align-items-center" style="height: 100%; align-items: center;">
                                <div style="width: 10%; align-items: center;">
                                    <InputLabel value="BCC" for="bcc" />
                                </div>
                                <div style="width: 90%;">
                                    <VTextField
                                    v-model="form.bcc"
                                    variant="outlined" density="compact" required hide-details
                                    ></VTextField>
                                </div>
                                <InputError class="mt-1" :message="form.errors.bcc" />
                            </div>
                        </VCol>
                    </VRow>
                    <VDivider class="mt-2" />
                    <VRow>
                        <VCol cols="8" class="mt-3">
                            <div class="d-flex justify-between align-items-center" style="height: 100%; align-items: center;">
                                <div style="width: 10%; align-items: center;">
                                    <InputLabel value="Subject" for="subject" />
                                </div>
                                <div style="width: 90%;">
                                    <VTextField
                                    v-model="form.subject"
                                    variant="outlined" density="compact" required hide-details
                                    ></VTextField>
                                </div>
                                <InputError class="mt-1" :message="form.errors.subject" />
                            </div>
                        </VCol>
                    </VRow>
                    <VDivider class="mt-2" />
                    <VRow>
                        <VCol cols="12" class="mt-3">
                            <div class="d-flex justify-between align-items-center" style="height: 100%; align-items: center;">
                                <div style="width: 100%;">
                                    <InputLabel value="Message Content" class="mb-3" for="message" />
                                    <VTextarea
                                    v-model="form.message"
                                    variant="outlined" density="compact" required hide-details
                                    ></VTextarea>
                                </div>
                                <InputError class="mt-1" :message="form.errors.message" />
                            </div>
                        </VCol>
                    </VRow>
                </VCol>
            </VRow>
        </VCardText>

        <!-- Card Actions -->
        <VCardActions>
            <VSpacer></VSpacer>
            <!-- <VBtn text="Close" variant="plain" @click="dialog = false"></VBtn> -->
            <VBtn prepend-icon="mdi-email-arrow-right" color="primary" text="Send" style="background-color: #f2c228; font-size: 15px; color: #fff !important;" @click="onClose"></VBtn>

        </VCardActions>
        </VCard>
    </VDialog>
  </template>

<style scoped>
.close-icon,
.minimize-icon
{
  cursor: pointer;
  color: #757575;
  transition: color 0.3s ease;
}

.close-icon:hover,
.minimize-icon:hover
{
  color: #000;
}

.icon-border
{
    border: 2px solid #a5a5a5;
    margin: 5px;
    font-size: 13px;
}

.spamtrashbtn
{
    font-size: 11px;
    width: 20px;
    height: 20px;
    box-shadow: none;
    border: 2px solid #a5a5a5;
}
</style>
