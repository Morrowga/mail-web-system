<script setup>
import { defineProps, ref } from 'vue';
import { Head,router } from '@inertiajs/vue3';
import ConfirmDialog from './ConfirmDialog.vue';
import { useI18n } from 'vue-i18n';

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
  link: {
    type: String,
    required: true,
  }
});

const { t, locale } = useI18n();
const snackbarVisible = ref(false);
const snackbarMessage = ref("");

// Copy function
const copyToClipboard = (text) => {
  navigator.clipboard.writeText(text)
    .then(() => {
      snackbarMessage.value = t('other.text_copied');
      snackbarVisible.value = true;
    })
    .catch((error) => {
      snackbarMessage.value = t('other.failed_copy');
      snackbarVisible.value = true;
      console.error('Error copying text: ', error);
    });
}
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
                <td  @click="router.get(link + '/' + item.id + '/edit')" v-for="(header, colIndex) in headers" :key="colIndex">
                    {{ item[header.val] }} <!-- Assuming keys are in lower case -->
                </td>
                <td>
                    <span @click="copyToClipboard(item.search_character)" class="text-[#1b5d9b] font-[500] cursor-pointer">{{ $t('table.copy') }}</span>
                    <span class="text-[#1b5d9b] font-[500]"> | </span>
                    <ConfirmDialog :item="item" :routeUrl="'/templates'" />
                    <!-- <span class="text-red font-bold cursor-pointer" @click="confirmDelete">Delete</span> -->
                </td>
            </tr>
        </tbody>
    </VTable>
    <VSnackbar v-model="snackbarVisible" :timeout="3000" color="info">
      {{ snackbarMessage }}
      <template v-slot:actions>
        <VBtn text @click="snackbarVisible = false">{{ $t('buttons.close') }}</VBtn>
      </template>
    </VSnackbar>
</template>

<style scoped>
.folder-table thead{
    background-color: #7f7f7f;
    color: #fff;
    border: 1px solid #e6e6e6;
}

.back-white{
    background-color: #fff;
    cursor: pointer;
}

.back-gray{
    background-color: #e6e6e6;
    cursor: pointer;
}
</style>
