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

// Local ref to manage the current page internally
const localCurrentPage = ref(props.currentPage);

// Watch for changes in `props.currentPage` and update `localCurrentPage` accordingly
watch(
  () => props.currentPage,
  (newPage) => {
    localCurrentPage.value = newPage;
  }
);

// Emit a new page number when `localCurrentPage` is updated by pagination button clicks
const handlePageChange = (newPage) => {
  emit('pageChanged', newPage);
};
</script>

<template>
  <!-- Use Vuetify pagination component with @update:modelValue to trigger page changes -->
  <v-pagination
    :model-value="localCurrentPage"
    :length="totalPages"
    @update:modelValue="handlePageChange" 
  />
</template>
