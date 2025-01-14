<template>
    <div class="pa-4 text-center">
      <v-dialog
        v-model="dialog"
        max-width="500"
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
            title="Please Wait ! This Folder matching with mails..."
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

  // Props
  const props = defineProps({
    visible: {
      type: Boolean,
      required: true,
    },
  });

  // Reactive Dialog State
  const dialog = ref(props.visible);

  // Watch for Prop Changes
  watch(
    () => props.visible,
    (newVal) => {
      dialog.value = newVal;
      if (newVal) {
        setTimeout(() => {
          dialog.value = false;
        }, 4000);
      }
    }
  );

  // Emit Event on Close
  defineEmits(['update:visible']);
  watch(dialog, (newVal) => {
    if (!newVal) {
      // Emit event to inform parent component
      defineEmits(['update:visible'])(false);
    }
  });
  </script>
