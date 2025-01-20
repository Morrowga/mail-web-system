<template>
    <VCard :class="borderClass() + ' mt-2'">
        <VCardText>
            <div class="reply-item">
                <div class="reply-content">
                    <div class="d-flex justify-between">
                    <div>
                        <p> <strong class="text-capitalize">{{ reply.name }} </strong> <{{ reply.sender }}></p>
                        <br>
                        <p>
                            <div v-html="formattedReplyBody"></div>

                            <div class="mt-5" v-if="reply?.attachments && reply?.attachments.length > 0">
                                <h4 class="my-2">{{ $t('input.attachment') }}: </h4>
                                <VRow>
                                    <VCol v-for="(attachment, index) in reply?.attachments" :key="index" cols="12" lg="6">
                                        <div class="cursor-pointer" @click="onDownload(attachment.path)">
                                            <template v-if="attachment.mime_type.includes('image')">
                                            <img width="150" height="150":src="attachment.path" alt="Attachment" class="attachment-image" style="border-radius: 20px; object-fit: cover;"/>
                                            </template>
                                            <template v-else>
                                                <!-- Show div for other types of attachments -->
                                                <div class="attachment-placeholder">
                                                    <div class="box-file">
                                                        <p>{{ attachment.file_name }}</p>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </VCol>
                                </VRow>
                            </div>
                        </p>
                    </div>
                    <div>
                        <p>{{ reply.datetime }}</p>
                    </div>
                    </div>
                </div>
            </div>
        </VCardText>
    </VCard>
  </template>

  <script setup>
  import { computed, defineProps } from 'vue';

  const props = defineProps({
    reply: Object,
    mail: Object
  });

  const formattedReplyBody = computed(() => {
  return props?.reply.body.replace(/\n/g, '<br>');
});

const borderClass = () =>  {
    if (props?.reply?.uid == null) {
        return 'border-green';
    } else if (props?.reply?.uid !== null && props?.reply.uid === props?.mail?.uid) {
        return 'border-red-thicker';
    } else {
        return 'border-red';
    }
}

const onDownload = (path) => {
  // Fetch the file from the given path
  fetch(path)
    .then(response => {
      // Check if the request was successful
      if (!response.ok) {
        throw new Error('File not found');
      }
      return response.blob();
    })
    .then(blob => {
      const link = document.createElement('a');

      const url = URL.createObjectURL(blob);

      const fileName = path.split('/').pop();
      link.href = url;
      link.download = fileName;

      document.body.appendChild(link);

      link.click();

      document.body.removeChild(link);
      URL.revokeObjectURL(url);
    })
    .catch(error => {
      console.error('Error downloading file:', error);
      alert('Failed to download the file');
    });
}
  </script>

  <style scoped>
  .reply-item {
    margin-top: 1rem;
    margin-bottom: 2rem;
    padding-left: 1.5rem;
  }

  .border-red-thicker {
    border: 3px solid red;
    box-shadow: 2px 2px 5px #000;
    border-color: red;
  }

  .box-file
  {
    width: 150px;
    background-color: gray;
    height: 150px;
    display: flex;
    text-align: center !important;
    border-radius: 20px;
    color: #fff;
    justify-content: center;
    align-items: center;
  }

  .border-red
  {
    border: 1px solid red;
    border-color: red;
  }

  .border-green
  {
    border: 1px solid green;
    border-color: green;
  }

  .reply-content {
    margin-bottom: 1rem;
  }

  .nested-replies {
    margin-left: 1rem;
  }
  </style>
