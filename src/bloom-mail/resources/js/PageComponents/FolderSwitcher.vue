<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    folderSwithcerDialog: Boolean,
    mail: {
        type: Object,
        required: true,
    },
});

const { t, locale } = useI18n();

const errorMessage = ref('');
const successMessage = ref('');

const page = usePage()

const loading = ref(false);

const folders = page.props.folders

const dialog = ref(props.folderSwithcerDialog);

const form = useForm({
    folder_id: null,
    mail_id: props?.mail?.id ?? '',
})


const emit = defineEmits(['update:dialog', 'reloadMails', 'handleLoadThread']);

const onClose = () => {
    dialog.value = false;
    emit('update:dialog', false);
    emit('reloadMails');
}

const mailFolders = ref(props.mail.folders);

const submitSwitch = async () => {
    if(form.folder_id == null)
    {
        errorMessage.value = 'Please select a folder';
        return;
    }

    loading.value = true;
     try {
        const response = await axios.post(`/mails/folder-switch`, form);
        if(response.data.status == 'success')
        {
            mailFolders.value = [];
            mailFolders.value.push(folders.find(folder => folder.id === form.folder_id));

            loading.value = false;
            errorMessage.value = '';
            successMessage.value = 'Mail moved successfully to the destination folder';
            emit('handleLoadThread', props.mail.id);
        }
    } catch (error) {
        loading.value = false;
        successMessage.value = '';
        if (error.response && error.response.status === 400) {
            errorMessage.value = error.response.data.message || 'Bad Request. Please try again.';
        } else {
            errorMessage.value = 'An error occurred. Please try again later.';
        }

    }
}


watch(() => props.folderSwithcerDialog, (newVal) => {
    dialog.value = newVal;
});
</script>


<template>
    <v-dialog v-model="dialog" max-width="700" @click:outside="onClose">
      <template v-slot:default>
        <v-card style="padding: 1rem; border-radius: 20px;">
          <v-card-title>
            <div class="d-flex justify-between">
                <h3> {{ $t('other.folder_switcher') }}</h3>
                <div class="icon-border text-center">
                    <VIcon
                        icon="mdi-close"
                        class="close-icon"
                        style="color: #a5a5a5; font-weight: bold;"
                        @click="onClose"
                    ></VIcon>
                </div>
            </div>
          </v-card-title>
          <v-card-text>
            <div class="d-flex justify-center">
                <div  style="width: 50%;" >
                    <InputLabel> {{ $t('other.current_folder') }} </InputLabel>
                </div>
                <div style="width: 50%;" class="mb-5 mx-5">
                    {{
                        mailFolders
                            ? mailFolders?.map(folder => folder.name).join(', ')
                            : 'Inbox Mail'
                    }}
                </div>
            </div>
            <div class="d-flex justify-center">
                <div  style="width: 50%;" >
                    <InputLabel> {{ $t('other.destination_folder') }} </InputLabel>
                </div>
                <div style="width: 50%;" class="mb-5 mx-5">
                    <VAutocomplete
                        v-model="form.folder_id"
                        :placeholder="'フォルダー'"
                        variant="outlined" density="compact"
                        required
                        hide-details
                        :items="folders"
                        item-value="id"
                        clearable
                        item-title="name"
                    ></VAutocomplete>
                    <InputError class="my-2" :message="errorMessage" />
                </div>
            </div>

            <div class="text-left" v-if="successMessage != ''">
                <p style="color: green;">{{ successMessage }}</p>
            </div>
            <div class="d-flex justify-center mt-10">
                <VBtn color="primary" style="
                    border-radius: 20px;
                    width: 25vh;
                    height: 6vh;
                    font-size: 1rem;"
                    @click="submitSwitch"
                    :disabled="loading"
                >
                        {{ loading ? 'Processing...' : $t('buttons.switch') }}
                </VBtn>
            </div>
          </v-card-text>
          <!-- <v-card-actions class="d-flex justify-center"> -->
          <!-- </v-card-actions> -->
        </v-card>
      </template>
    </v-dialog>
  </template>
