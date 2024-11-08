<script setup>
  import { ref, watch, defineProps, defineEmits } from 'vue';

  const props = defineProps({
    totalPages: {
      type: Number,
      required: true,
    },
    currentPage: {
      type: Number,
      required: true,
    },
  });

  const emit = defineEmits(['pageChanged']);

  // Local ref for managing the current page internally
  const localCurrentPage = ref(props.currentPage);

  watch(
    () => props.currentPage,
    (newPage) => {
        localCurrentPage.value = newPage;  // Sync the currentPage prop to localCurrentPage
    }
    );

    // Emit the new page number when `localCurrentPage` is updated
    watch(localCurrentPage, (newPage) => {
     emit('pageChanged', newPage);
    });
  </script>


<template>
    <v-pagination
      v-model="localCurrentPage"
      :length="totalPages"
    />
  </template>

