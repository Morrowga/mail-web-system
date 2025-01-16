<script setup>
import { ref, watch, computed, onMounted, defineEmits, defineProps } from 'vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import MailThread from './MailThread.vue';
import { useI18n } from 'vue-i18n';
import { getTranslatedStatus } from '@/Helper/status';
import ReplaceConfirmDialog from './ReplaceConfirmDialog.vue';

const page = usePage();

const props = defineProps({
    createDialog: Boolean,
    from: String,
    type: String,
    mailData: Object,
    threads: Array
});

const { t } = useI18n();

const currentActiveTemplateId = ref(null)
const  replaceDialog = ref(false);
const isDisabled = ref(false);

const typeOfMail = ref(props.type);

const form = useForm({
    subject: props?.mailData?.subject,
    from: page?.props?.from,
    template_id: props?.mailData?.template_id,
    to: props?.mailData?.sender,
    message_content: "",
    og_message_id: props?.mailData?.message_id,
    type: typeOfMail.value
});

console.log('hellotest', typeOfMail.value)

watch(
    () => typeOfMail.value,
    (newValue) => {
        emit('update:mailTypeEevent', newValue)
        form.type = newValue
    }
);

console.log(form)

const formattedDateTime = ref(null);
const emit = defineEmits(['update:dialog', 'cancelStatus', 'handleLoadThread', 'update:mailTypeEevent', 'minimizeReply']);

const onClose = () => {
    if(typeOfMail.value == 'reply')
    {
        emit('cancelStatus');
    }
    emit('handleLoadThread', props?.mailData?.id);
    emit('update:dialog', false);
};

const formatDateTime = () => {
  const currentDate = new Date();
  const formattedDate = currentDate.toISOString().split('T')[0].replace(/-/g, '/');
  const formattedTime = currentDate.toTimeString().split(' ')[0];
  formattedDateTime.value = `${formattedDate} ${formattedTime}`;
};

const formSubmit = () => {
    isDisabled.value = true;

    form.post(route('mails.reply-forward', props?.mailData?.id), {
        onSuccess: () => {
            form.reset();
            emit('handleLoadThread', props?.mailData?.id);
            emit('update:dialog', false);
            emit('update:mailTypeEevent', null)
            isDisabled.value = false;
        },
        onFinish: () => {
            isDisabled.value = false;
        },
        onError: (error) => {
            isDisabled.value = false;
            console.error("Form submission error:", error);
        },
    });
};


watch(() => props.mailData, (newMailData) => {
    if (newMailData) {
        // Update the form fields with the new mailData
        form.subject = newMailData.subject || "";
        form.template_id = newMailData.template_id || null;
        form.to = newMailData.sender || "";
        form.message_content = "";
        form.og_message_id = newMailData.message_id || null;
        form.type = typeOfMail.value || null;

        // Reset template selection and dialog state
        currentActiveTemplateId.value = null;
        replaceDialog.value = false;
    }
});

const itemProps = (item) =>  {
    console.log(item);
    return {
        title: item.text,
        value: item.value,
        disabled: item.disabled
    }
}

const formatTemplates = (responses) => {
  const categories = responses.reduce((acc, item) => {
    const categoryName = item.template_category.name;
    if (!acc[categoryName]) {
      acc[categoryName] = [];
    }
    acc[categoryName].push({ text: item.title, value: item.id });
    return acc;
  }, {});

  const formattedData = [];
  for (const [category, templates] of Object.entries(categories)) {
    formattedData.push({ text: category, disabled: true });
    formattedData.push(...templates);
  }

  return formattedData;
};

const modifiedTemplates = formatTemplates(page?.props?.templates);

const handleReplace = () =>
{
    let templateSelected = page?.props?.templates.find(
        (template) => template.id === currentActiveTemplateId.value
    );

    form.subject = templateSelected?.subject;
    form.message_content = templateSelected?.message_content;
}

const onTemplateChange = (templateId) => {
    if(form.message_content == '')
    {
        currentActiveTemplateId.value = templateId;
        handleReplace()
    } else {
        currentActiveTemplateId.value = templateId;
        replaceDialog.value = true
    }
}

const minimizeDialog = () =>
{
    emit('update:visibleFloat', true);
    emit('update:dialog', false);
    emit('minimizeReply')
};

onMounted(() => {
  formatDateTime();
});

watch(() => props.type, (newType) => {
    typeOfMail.value = newType
    form.type = newType;
});
</script>

<template>
    <!-- Bind VDialog with props.createDialog and emit update on close -->
    <VDialog v-model="props.createDialog" max-width="1250" @update:modelValue="(val) => emit('update:dialog', val)" persistent>
        <VForm @submit.prevent="formSubmit">
            <VCard>
                <VCardTitle class="d-flex justify-between align-center">
                    <div>
                        <h3>
                            {{ props?.type == 'reply' ? $t('other.reply_form') : $t('other.forward_form')}}
                        </h3>
                    </div>
                    <div class="d-flex justify-end">
                        <div class="icon-border d-flex justify-center align-items-center" v-if="props?.type == 'reply'">
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
                    </div>
                </VCardTitle>

                <VCardText>
                    <VRow dense>
                        <VCol cols="12" md="12" lg="12" sm="12">
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
                                    <div class="d-flex justify-between align-items-center" style="height: 100%;" v-if="props?.mailData?.status == 'resolved'">
                                        <div style="width: 50%; align-self: flex-end;">
                                            <span class="font-bold">{{ $t('input.person_in_charge') }}</span>
                                        </div>
                                        <div style="width: 50%; align-self: flex-end;">
                                            {{ props?.mailData?.person_in_charge ?? '-'}}
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
                                            <span class="font-bold">{{ $t('input.status') }}</span>
                                        </div>
                                        <div style="width: 50%; align-self: flex-end;" class="text-capitalize">
                                            {{ getTranslatedStatus(t, props?.mailData?.status)}}
                                        </div>
                                    </div>
                                </VCol>
                            </VRow>
                            <VDivider class="mt-2" />
                            <VRow>
                                <VCol cols="8" class="mt-3">
                                    <div class="d-flex justify-between align-items-center" style="height: 100%; align-items: center;">
                                        <div style="width: 20%; align-items: center;">
                                            <InputLabel :value="$t('input.template')" for="template" />
                                        </div>
                                        <div style="width: 80%;">
                                            <VSelect
                                            placeholder="テンプレートを選択"
                                            v-model="form.template_id"
                                            variant="outlined" density="compact"
                                            required hide-details
                                            :items="modifiedTemplates"
                                            :item-props="itemProps"
                                            @update:model-value="onTemplateChange"
                                            ></VSelect>
                                        </div>
                                        <!-- <div style="width: 20%;" class="mx-3">
                                            <v-select
                                            variant="outlined"
                                            density="compact"
                                            required
                                            hide-details
                                            v-model="typeOfMail"
                                            :items="[
                                                { text: '返信', value: 'reply' },
                                                { text: '転送', value: 'forward' }
                                            ]"
                                            item-text="text"
                                            item-value="value"
                                            ></v-select>
                                        </div> -->
                                        <InputError class="mt-1" :message="form.errors.template" />
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
                                            <InputLabel :value="$t('input.message_content')" class="mb-3" for="message" />
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
                                        <VBtn prepend-icon="mdi-email-arrow-right" type="submit" :disabled="isDisabled" color="primary" :text="$t('buttons.send')" style="background-color: #f2c228; font-size: 15px; color: #fff !important; width: 100%; height: 30%;"></VBtn>
                                    </div>
                                </VCol>
                            </VRow>
                        </VCol>
                    </VRow>
                </VCardText>
            </VCard>
        </VForm>
        <ReplaceConfirmDialog @handleReplace="handleReplace" :confirmDialog="replaceDialog" @update:dialog="replaceDialog = $event" />
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

</style>w
