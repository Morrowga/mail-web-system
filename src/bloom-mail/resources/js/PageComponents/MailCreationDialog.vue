<script setup>
import { ref } from 'vue';

const dialog = ref(false);

</script>
<template>
    <div class="text-left">
      <VDialog
        v-model="dialog"
        max-width="600"
      >
        <template v-slot:activator="{ props: activatorProps }">
          <VBtn
            prepend-icon="mdi-mail"
            color="primary"
            v-bind="activatorProps"
          >
            Mail Box
         </VBtn>
        </template>

        <VCard
          title="Mail"
        >
          <VCardText>
            <VRow dense>
              <VCol
                cols="12"
                md="12"
                sm="6"
              >
                <VTextField
                  label="First name*"
                  required
                ></VTextField>
              </VCol>
            </VRow>
          </VCardText>

          <VDivider></VDivider>

          <VCardActions>
            <VSpacer></VSpacer>

            <VBtn
              text="Close"
              variant="plain"
              @click="dialog = false"
            ></VBtn>

            <VBtn
              color="primary"
              text="Save"
              variant="tonal"
              @click="dialog = false"
            ></VBtn>
          </VCardActions>
        </VCard>
      </VDialog>
    </div>
  </template>
