<template>
    <VContainer class="d-flex justify-center">
      <VDialog v-model="modal" width="290px">
        <template v-slot:activator="{ on, attrs }">
          <VTextField
            v-model="formattedDate"
            label="Select a date"
            prepend-icon="mdi-calendar"
            readonly
            v-bind="attrs"
            v-on="on"
          />
        </template>

        <VCard>
          <VDatePicker v-model="selectedDate" @input="modal = false"></VDatePicker>
        </VCard>
      </VDialog>
    </VContainer>
  </template>

  <script setup>
  import { ref, computed } from "vue";

  const modal = ref(false);
  const selectedDate = ref(null);

  // Format the date before displaying
  const formattedDate = computed(() =>
    selectedDate.value ? new Date(selectedDate.value).toLocaleDateString() : ""
  );
  </script>
