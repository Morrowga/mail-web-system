<script setup>
import { getStatusColor } from '@/Helper/status';
import { ref } from 'vue';

const props = defineProps({
  data: {
    type: Array,
    required: true
  }
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
          <th class="header-cell">Status</th>
          <th class="header-cell">Sender</th>
          <th class="header-cell">Subject</th>
          <th>Date Time</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(email, index) in props?.data" @click="handleRowClick(email)" :key="index" class="cursor-pointer">
          <td>
            <VChip :style="'background:' + getStatusColor(email.status) + '; color: #fff;'">
                {{ email.status }}
            </VChip>
          </td>
          <td
            style="word-break: break-word;
            white-space: pre-wrap;">
            {{ email.sender }}
          </td>
          <td>{{ email.subject }}</td>
          <td>{{ email.date + ' ' + email.time }}</td>
        </tr>
      </tbody>
    </VTable>
  </template>

  <style scoped>
  .status-table {
    width: 100%; /* Makes the table take full width */
    border-collapse: collapse; /* Ensures there are no gaps between table cells */
  }

  .status-table th,
  .status-table td {
    padding: 10px; /* Adds padding to table cells */
    border: none; /* Removes all borders */
    position: relative; /* Enables absolute positioning for the divider */
  }

  .header-cell {
    /* This class is applied to header cells */
  }

  .header-cell:not(:last-child)::after {
    content: ""; /* Creates a pseudo-element for the vertical divider */
    position: absolute; /* Positions it relative to the header cell */
    top: 10%; /* Start the line 10% down from the top */
    bottom: 10%; /* End the line 10% from the bottom */
    right: 0; /* Aligns it to the right edge of the cell */
    width: 2px; /* Sets the width of the line */
    background-color: #ccc; /* Sets the color of the line */
    opacity: 0.5;
  }

  .status-table th {
    border-bottom: 2px solid #ccc !important; /* Adds a horizontal border at the bottom of headers */
  }

  .status-table tr {
    border-bottom: 1px solid #eee; /* Adds a horizontal line between rows */
  }
  </style>
