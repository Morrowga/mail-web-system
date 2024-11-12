<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch } from 'vue';
import MailThread from './MailThread.vue';

const page = usePage();

const props = defineProps({
    createDialog: Boolean,
    from: String,
    type: String,
    mailData: Object,
    threads: Array
});

// Reactive reference for mail type
const mail_type_value = computed(() => props.type);

// Watch for changes in props.type and update mail_type_value accordingly
watch(() => props.type, (newType) => {
    mail_type_value.value = newType;
});


const formattedDateTime = ref(null);

const emit = defineEmits(['update:dialog']);

const minimizeDialog = () => {
    emit('update:dialog', false);
};

const onClose = () => {
    emit('update:dialog', false);
};

const form = useForm({
    subject: props?.mailData?.subject,
    from: page?.props?.from,
    to: props?.mailData?.sender,
    message_content: "",
    og_message_id: props?.mailData?.message_id,
    type: mail_type_value
});

const formatDateTime = () => {
  const currentDate = new Date();
  const formattedDate = currentDate.toISOString().split('T')[0].replace(/-/g, '/');
  const formattedTime = currentDate.toTimeString().split(' ')[0];
  formattedDateTime.value = `${formattedDate} ${formattedTime}`;
};

const formSubmit = () => {
    form.post(route('mails.reply-forward', props?.mailData?.id), {
        onSuccess: () => {
            form.reset();
            onClose();
        },
        onError: (error) => {
            console.error("Form submission error:", error);
        },
    });
};

onMounted(() => {
  formatDateTime();
});
</script>

<template>
    <!-- Bind VDialog with props.createDialog and emit update on close -->
    <VDialog v-model="props.createDialog" max-width="1250" @update:modelValue="(val) => emit('update:dialog', val)">
        <VForm @submit.prevent="formSubmit">
            <VCard>
                <VCardTitle class="d-flex justify-end align-center">
                    <div class="icon-border d-flex justify-center align-items-center">
                        <VIcon
                            icon="mdi-minus"
                            class="minimize-icon"
                            style="color: #a5a5a5; font-weight: bold;"
                            @click="minimizeDialog"
                        ></VIcon>
                    </div>
                    <div class="icon-border text-center">
                        <VIcon
                            icon="mdi-close"
                            class="close-icon"
                            style="color: #a5a5a5; font-weight: bold;"
                            @click="onClose"
                        ></VIcon>
                    </div>
                </VCardTitle>

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
                        <VCol cols="12" md="12" lg="12" sm="12">
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
                                            <InputError class="mt-1" :message="form.errors.subject" />
                                        </div>
                                    </div>
                                </VCol>
                                <VCol cols="4">
                                    <div class="d-flex justify-between align-items-center" style="height: 100%;">
                                        <div style="width: 50%; align-self: flex-end;">
                                            <span class="font-bold">Sent date & time</span>
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
                                            <InputLabel value="From" for="from" />
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
                                                type="email"
                                                required
                                                readonly
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
                                            <span class="font-bold">Status</span>
                                        </div>
                                        <div style="width: 50%; align-self: flex-end;" class="text-capitalize">
                                            {{ props?.mailData?.status }}
                                        </div>
                                    </div>
                                </VCol>
                            </VRow>
                            <!-- <VRow >
                                <VCheckbox label="Attach the original email" reverse hide-details></VCheckbox>
                            </VRow> -->
                            <VDivider class="mt-2" />
                            <VCol cols="12" md="12" lg="12" sm="12" style="height: 40vh; overflow-y: auto;" >
                                <MailThread v-for="reply in props?.threads" :key="reply.id" :reply="reply" />
                            </VCol>
                            <VDivider class="mt-2" />
                            <VRow>
                                <VCol cols="9" class="mt-3">
                                    <div class="d-flex justify-between align-items-center" style="height: 100%; align-items: center;">
                                        <div style="width: 100%;">
                                            <InputLabel value="Message Content" class="mb-3" for="message" />
                                            <VTextarea
                                            v-model="form.message_content"
                                            variant="outlined" density="compact" required hide-details
                                            ></VTextarea>
                                            <InputError class="mt-1" :message="form.errors.message_content" />
                                        </div>
                                    </div>
                                </VCol>
                                <VCol cols="3">
                                    <div class="mx-5 my-5 d-flex justify-center" style="align-items: center; height: 100%;">
                                        <VBtn prepend-icon="mdi-email-arrow-right" type="submit" color="primary" text="Send" style="background-color: #f2c228; font-size: 15px; color: #fff !important; width: 100%; height: 30%;"></VBtn>
                                    </div>
                                </VCol>
                            </VRow>
                        </VCol>
                    </VRow>
                </VCardText>
            </VCard>
        </VForm>
    </VDialog>
</template>

<style scoped>
.close-icon,
.minimize-icon {
  cursor: pointer;
  color: #757575;
  transition: color 0.3s ease;
}

.close-icon:hover,
.minimize-icon:hover {
  color: #000;
}

.icon-border {
    border: 2px solid #a5a5a5;
    margin: 5px;
    font-size: 13px;
}

.spamtrashbtn {
    font-size: 11px;
    width: 20px;
    height: 20px;
    box-shadow: none;
    border: 2px solid #a5a5a5;
}
</style>
