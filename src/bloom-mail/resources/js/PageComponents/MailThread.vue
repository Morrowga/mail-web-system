<template>
    <VCard :class="reply?.uid == null ? 'border-green mt-2' : 'border-red mt-2'">
        <VCardText>
            <div class="reply-item">
                <div class="reply-content">
                    <div class="d-flex justify-between">
                    <div>
                        <p>
                        Attn:
                        <br />
                        <div v-html="formattedReplyBody"></div>
                        </p>
                        <p class="ml-2">{{ reply.from }}</p>
                    </div>
                    <div>
                        <p>{{ reply.datetime }}</p>
                    </div>
                    </div>
                    <div class="ml-2 mt-5">
                    </div>
                </div>
            </div>
        </VCardText>
    </VCard>
  </template>

  <script setup>
  import { computed, defineProps } from 'vue';

  const props = defineProps({
    reply: Object
  });

  const formattedReplyBody = computed(() => {
  return props?.reply.body.replace(/\n/g, '<br>');
});
  </script>

  <style scoped>
  .reply-item {
    margin-top: 1rem;
    margin-bottom: 2rem;
    padding-left: 1.5rem;
  }

  .border-red
  {
    border: 1px solid red;
    border-color: red;
  }

  .border-green
  {
    border: 2px solid green;
    border-color: green;
  }

  .reply-content {
    margin-bottom: 1rem;
  }

  .nested-replies {
    margin-left: 1rem;
  }
  </style>
