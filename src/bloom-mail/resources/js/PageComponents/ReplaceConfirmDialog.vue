<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    confirmDialog: Boolean,
});

const dialog = ref(props.confirmDialog);

const emit = defineEmits(['update:dialog']);

const onClose = () => {
    dialog.value = false;
    emit('update:dialog', false);
}

const submitReplace = () => {
    emit('handleReplace');
    onClose()
}


watch(() => props.confirmDialog, (newVal) => {
    dialog.value = newVal;
});
</script>


<template>
    <v-dialog v-model="dialog" max-width="500">
      <template v-slot:default>
        <v-card>
          <v-card-title>{{ $t('other.confirmation') }}</v-card-title>
          <v-card-text>
            <div>
              <p>
                    {{ $t('other.replace_text') }}
              </p>
            </div>
          </v-card-text>

          <v-card-actions class="d-flex justify-center">
            <v-btn :text="$t('buttons.yes')" color="green" @click="submitReplace"></v-btn>
            <v-btn :text="$t('buttons.no')" color="primary" @click="onClose"></v-btn>
          </v-card-actions>
        </v-card>
      </template>
    </v-dialog>
  </template>
