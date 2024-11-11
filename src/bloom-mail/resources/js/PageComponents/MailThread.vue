<template>
    <div class="reply-item">
      <div class="reply-content">
        <div class="d-flex justify-between">
          <div>
            <p>
              Attn:
              <br />
              <span v-html="reply.body"></span>
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
      <hr style="opacity: 0.3;">

      <!-- Recursively render each nested reply -->
      <div v-if="reply.all_replies && reply.all_replies.length" class="nested-replies">
        <div>
            <MailThread v-for="nestedReply in reply?.all_replies" :key="nestedReply.id" :reply="nestedReply" />
        </div>
      </div>
    </div>
  </template>

  <script setup>
  import { defineProps } from 'vue';

  const props = defineProps({
    reply: Object
  });
  </script>

  <style scoped>
  .reply-item {
    margin-top: 1rem;
    margin-bottom: 2rem;
    padding-left: 1.5rem;
    border-left: 2px solid #ccc; /* Visual nesting for replies */
  }

  .reply-content {
    margin-bottom: 1rem;
  }

  .nested-replies {
    margin-left: 1rem;
  }
  </style>
