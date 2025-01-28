<template>
    <VCard class="write-action-card">
      <VCardTitle style="background-color: #f0f2f6;">
        <h4>{{ title }}</h4>
      </VCardTitle>
      <div>
        <slot></slot>
      </div>
    </VCard>
  </template>

  <script setup>
  import { defineProps } from 'vue';

  const props = defineProps({
    title: String,
  });
  </script>

<style scoped>
.write-action-card{
    border-width: 1px !important;
}
</style>
