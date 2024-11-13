<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { useForm } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
createDialog: Boolean,
floatButton: Boolean,
templates: Array,
from: String,
label: String
});

const { t, locale } = useI18n();

const dialog = ref(props.createDialog);
const formattedDateTime = ref(null);

const emit = defineEmits(['update:dialog', 'update:visibleFloat', 'update:labelValue']);

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
    from: props?.from,
    to: "",
    template_id: "",
    cc: "",
    bcc: "",
    message_content: ""
})

const handleSubjectChange = (event) => {
  emit('update:labelValue', event.target.value);
};

const formatDateTime = () => {
  const currentDate = new Date();

  const formattedDate = currentDate.toISOString().split('T')[0].replace(/-/g, '/');

  const formattedTime = currentDate.toTimeString().split(' ')[0];

  formattedDateTime.value = `${formattedDate} ${formattedTime}`;
};

const formSubmit = () => {
    form.post(route('mails.store'), {
        onSuccess: () => {
            form.reset();
            emit('fetchMail')
            onClose();
        },
        onError: (error) => {
            console.error("Form submission error:", error); // Handle the error if needed
        },
    });
};


onMounted(() => {
  formatDateTime();
});


</script>
<template>
    <div class="text-left">
        <VBtn
            prepend-icon="mdi-email-outline"
            color="primary"
           @click="onOpen"
        >
            {{ $t('buttons.mailbox') }}
        </VBtn>
    </div>

    <VDialog v-model="dialog" max-width="1250">
        <VForm @submit.prevent="formSubmit">
            <VCard>
                <!-- Card Header with Icons -->
                <VCardTitle class="d-flex justify-end align-center">
                    <div class="text-right">
                        <VBtn class="spamtrashbtn">{{ $t('buttons.spam') }}</VBtn>
                        <VBtn class="mx-2 spamtrashbtn">{{ $t('buttons.trash') }}</VBtn>
                    </div>
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
                        <!-- <VCol cols="12" md="12" sm="6">
                            <VDivider />
                            <div class="pb-2 pt-1 text-right">
                                <VBtn class="spamtrashbtn">Spam</VBtn>
                                <VBtn class="mx-2 spamtrashbtn">Trash</VBtn>
                            </div>
                            <VDivider />
                        </VCol> -->
                        <VCol cols="12" md="12" sm="6">
                            <VRow>
                                <VCol cols="8">
                                    <div class="d-flex justify-between align-items-center" style="height: 100%;">
                                        <div style="width: 10%; align-self: flex-end;">
                                            <InputLabel :value="$t('input.subject')" for="subject" />
                                        </div>
                                        <div style="width: 90%;">
                                            <VTextField
                                                variant="plain"
                                                density="compact"
                                                required
                                                v-model="form.subject"
                                                 @change="handleSubjectChange"
                                                hide-details
                                            ></VTextField>
                                            <InputError class="mt-1" :message="form.errors.subject" />
                                        </div>
                                    </div>
                                </VCol>
                                <VCol cols="4">
                                    <div class="d-flex justify-between align-items-center" style="height: 100%;">
                                        <div style="width: 50%; align-self: flex-end;">
                                            <span class="font-bold">{{ $t('input.sent_datetime') }}</span>
                                        </div>
                                        <div style="width: 50%; align-self: flex-end;">
                                            {{formattedDateTime}}
                                        </div>
                                    </div>
                                </VCol>
                            </VRow>
                            <VDivider class="mt-1" />
                            <VRow>
                                <VCol cols="8">
                                    <div class="d-flex justify-between align-items-center" style="height: 100%;">
                                        <div style="width: 10%; align-self: flex-end;">
                                            <InputLabel :value="$t('input.from')" for="from" />
                                        </div>
                                        <div style="width: 90%;">
                                            <VTextField
                                                v-model="form.from"
                                                variant="plain"
                                                type="email"
                                                density="compact"
                                                required
                                                readonly
                                                hide-details
                                            ></VTextField>
                                            <InputError class="mt-1" :message="form.errors.from" />
                                        </div>
                                    </div>
                                </VCol>
                                <VCol cols="4">
                                    <div class="d-flex justify-between align-items-center" style="height: 100%;">
                                        <div style="width: 50%; align-self: flex-end;">
                                            <span class="font-bold">{{ $t('input.person_in_charge')}}</span>
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
                                            <InputLabel :value="$t('input.to')" for="to" />
                                        </div>
                                        <div style="width: 90%;">
                                            <VTextField
                                                variant="plain"
                                                density="compact"
                                                type="email"
                                                required
                                                hide-details
                                                v-model="form.to"
                                            ></VTextField>
                                            <InputError class="mt-1" :message="form.errors.to" />
                                        </div>
                                    </div>
                                </VCol>
                                <VCol cols="4">
                                    <div class="d-flex justify-between align-items-center" style="height: 100%;">
                                        <div style="width: 50%; align-self: flex-end;">
                                            <span class="font-bold">{{ $t('input.status') }}</span>
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
                            <VDivider class="" />
                            <VRow>
                                <VCol cols="8" class="mt-3">
                                    <div class="d-flex justify-between align-items-center" style="height: 100%; align-items: center;">
                                        <div style="width: 10%; align-items: center;">
                                            <InputLabel :value="$t('input.template')" for="template" />
                                        </div>
                                        <div style="width: 90%;">
                                            <VSelect
                                            placeholder="Select Template"
                                            v-model="form.template_id"
                                            variant="outlined" density="compact" required hide-details
                                            :items="props?.templates"
                                            item-title="title"
                                            item-value="id"
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
                                            <InputLabel :value="$t('input.to')" for="to" />
                                        </div>
                                        <div style="width: 90%;">
                                            <VTextField
                                            v-model="form.to"
                                            variant="outlined" density="compact" required hide-details
                                            ></VTextField>
                                        </div>
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
                                            <InputError class="mt-1" :message="form.errors.cc" />
                                        </div>
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
                                            variant="outlined" density="compact" hide-details
                                            ></VTextField>
                                            <InputError class="mt-1" :message="form.errors.bcc" />
                                        </div>
                                    </div>
                                </VCol>
                            </VRow>
                            <VDivider class="mt-2" />
                            <VRow>
                                <VCol cols="8" class="mt-3">
                                    <div class="d-flex justify-between align-items-center" style="height: 100%; align-items: center;">
                                        <div style="width: 10%; align-items: center;">
                                            <InputLabel :value="$t('input.subject')" for="subject" />
                                        </div>
                                        <div style="width: 90%;">
                                            <VTextField
                                            v-model="form.subject"
                                            variant="outlined" density="compact" hide-details
                                            ></VTextField>
                                        </div>
                                    </div>
                                </VCol>
                            </VRow>
                            <VDivider class="mt-2" />
                            <VRow>
                                <VCol cols="10" class="mt-3">
                                    <div class="d-flex justify-between align-items-center" style="height: 100%; align-items: center;">
                                        <div style="width: 100%;">
                                            <InputLabel :value="$t('input.message_content')" class="mb-3" for="message" />
                                            <VTextarea
                                            v-model="form.message_content"
                                            variant="outlined" density="compact" required hide-details
                                            ></VTextarea>
                                            <InputError class="mt-1" :message="form.errors.message_content" />
                                        </div>
                                    </div>
                                </VCol>
                                <VCol cols="2">
                                    <div class="mx-5 my-5 d-flex justify-center" style="align-items: center; height: 100%;">
                                        <VBtn prepend-icon="mdi-email-arrow-right" type="submit" color="primary" :text="$t('buttons.send')" style="background-color: #f2c228; font-size: 15px; color: #fff !important; width: 100%; height: 30%;"></VBtn>
                                    </div>
                                </VCol>
                            </VRow>
                        </VCol>
                    </VRow>
                </VCardText>

                <!-- Card Actions -->
                </VCard>
        </VForm>
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
    font-size: 10px;
    width: 35%;
    height: 3vh;
    box-shadow: none;
    border: 2px solid #a5a5a5;
}
</style>
