<script setup>
import { defineProps } from 'vue';

// Define the props that this component accepts
const props = defineProps({
  headers: {
    type: Array,
    required: true,
  },
  data: {
    type: Array,
    required: true,
  },
});
</script>

<template>
    <VTable class="folder-table">
        <thead>
            <tr>
                <th v-for="(header, index) in headers" :key="index">{{ header.header }}</th>
                <th></th> <!-- For action buttons (Copy/Delete) if needed -->
            </tr>
        </thead>
        <tbody>
            <tr v-for="(item, rowIndex) in data" :key="rowIndex" :class="rowIndex % 2 === 0 ? 'back-gray' : 'back-white'">
                <td v-for="(header, colIndex) in headers" :key="colIndex">
                    {{ item[header.val] }} <!-- Assuming keys are in lower case -->
                </td>
                <td>
                    <span class="text-[#1b5d9b] font-[500]">Copy</span>
                    <span class="text-[#1b5d9b] font-[500]"> | </span>
                    <span class="text-red font-bold">Delete</span>
                </td>
            </tr>
        </tbody>
    </VTable>
</template>

<style scoped>
.folder-table thead{
    background-color: #7f7f7f;
    color: #fff;
    border: 1px solid #e6e6e6;
}

.back-white{
    background-color: #fff;
}

.back-gray{
    background-color: #e6e6e6;
}
</style>
