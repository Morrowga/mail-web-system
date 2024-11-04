<script setup>
import { ref } from 'vue';
const props = defineProps({
});

const emit = defineEmits(['update:onOpenDialog', 'update:hideFloat']);

const handleClick = () => {
    emit('update:onOpenDialog', true);
    emit('update:hideFloat', false);
};
</script>


<template>
    <div>
      <!-- Your other content goes here -->

      <!-- Floating Button -->
      <VBtn class="floating-button" @click="handleClick">
        RE: [Ginza] xxx
      </VBtn>
    </div>
  </template>

  <style scoped>
  .floating-button {
    position: fixed;
    bottom: 0;
    right: 50px;
    width: auto;
    height: 50px;
    font-weight: bold !important;
    border-radius: 0;
    background-color: #e8e8e8;
    color: rgb(0,0,0,0.5);
    border-top: 2px solid rgba(0, 0, 0, 0.5);
    border-left: 2px solid rgba(0, 0, 0, 0.5);
    border-right: 2px solid rgba(0, 0, 0, 0.5);
    z-index: 1000;
  }
  </style>
