<template>
    <div class="pa-4 text-center">
      <v-dialog
        v-model="dialog"
        max-width="600"
        persistent
      >
        <v-list
          class="py-2"
          color="primary"
          elevation="12"
          rounded="lg"
        >
          <v-list-item
            prepend-icon="$vuetify-outline"
            :title="$t('other.matchingfolder')"
          >
            <template v-slot:prepend>
              <div class="pe-4">
                <v-icon icon="mdi-folder-sync" color="primary" size="x-large"></v-icon>
              </div>
            </template>

            <template v-slot:append>
              <v-progress-circular
                color="primary"
                indeterminate="disable-shrink"
                size="16"
                width="2"
              ></v-progress-circular>
            </template>
          </v-list-item>
        </v-list>
      </v-dialog>
    </div>
  </template>

  <script setup>
  import { ref, watch,defineEmits } from 'vue';
  import { useI18n } from 'vue-i18n';

  // Props
  const props = defineProps({
    visible: {
      type: Boolean,
      required: true,
    },
  });

    const { t, locale } = useI18n();

  // Reactive Dialog State
  const dialog = ref(props.visible);
  const emit = defineEmits(['update:visible']);

  // Watch for Prop Changes
  watch(
    () => props.visible,
    (newVal) => {
      dialog.value = newVal;
      if (newVal) {
        setTimeout(() => {
          dialog.value = false;
          emit('update:visible', false);
        }, 4000);
      }
    }
  );

  watch(dialog, (newVal) => {
    if (!newVal) {
      // Emit event to inform parent component
      emit('update:visible', false);
    }
  });
  </script>
