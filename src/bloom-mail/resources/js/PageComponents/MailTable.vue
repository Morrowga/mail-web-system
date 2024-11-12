<script setup>
import { getStatusColor } from '@/Helper/status';
import { ref } from 'vue';

const props = defineProps({
  data: {
    type: Array,
    required: true
  },
  headers: {
    type: Array,
    required: true 
  },
  pageType: String,
  loading: Boolean,
});

const emit = defineEmits();

const handleRowClick = (row) => {
  emit('rowSelected', row);
};
</script>

<template>
  <VTable class="status-table">
    <thead>
      <tr>
        <th class="header-cell" v-for="header in headers" :key="header.value">
          {{ header.name }}
        </th>
      </tr>
    </thead>

    <tbody class="tbody-container">
      <tr v-if="!data.length && !loading">
        <td :colspan="headers.length" class="text-center pt-5">
          No data available
        </td>
      </tr>

      <tr v-else v-for="(email, index) in data" :key="index" @click="handleRowClick(email)" class="cursor-pointer">
        <td v-for="header in headers" :key="header.value" :style="{ wordBreak: 'break-word', whiteSpace: 'pre-wrap' }">
          <template v-if="header.value === 'status' && pageType === 'inbox'">
            <VChip :style="'background:' + getStatusColor(email[header.value]) + '; color: #fff;'">
              {{ email[header.value] }}
            </VChip>
          </template>
          <template v-else>
            {{ email[header.value] }}
          </template>
        </td>
      </tr>
    </tbody>

    <!-- Overlay for Loading Spinner -->
    <div v-if="loading" class="loading-overlay">
      <v-progress-circular indeterminate color="blue"></v-progress-circular>
    </div>
  </VTable>
</template>

<style scoped>
.status-table {
  width: 100%;
  min-height: 70vh;
  border-collapse: collapse;
}

.status-table th,
.status-table td {
  padding: 10px;
  border: none;
  position: relative;
}

.header-cell {
  /* Add custom styles if needed */
}

.header-cell:not(:last-child)::after {
  content: "";
  position: absolute;
  top: 10%;
  bottom: 10%;
  right: 0;
  width: 2px;
  background-color: #ccc;
  opacity: 0.5;
}

.status-table th {
  border-bottom: 2px solid #ccc !important;
}

.status-table tr {
  border-bottom: 1px solid #eee;
}

/* Ensuring tbody can hold the overlay */
.tbody-container {
  position: relative;
}

/* The loading overlay styles */
.loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.7); /* Slight transparency */
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 999; /* Ensures the overlay stays above the table content */
}
</style>
