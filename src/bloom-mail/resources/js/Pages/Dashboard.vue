<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FloatMailButton from '@/PageComponents/FloatMailButton.vue';
import MailCreationDialog from '@/PageComponents/MailCreationDialog.vue';
import MailDetail from '@/PageComponents/MailDetail.vue';
import MailTable from '@/PageComponents/MailTable.vue';
import { Head,router } from '@inertiajs/vue3';
import { ref } from 'vue';

const createDialogVisible = ref(false);

const isVisibleFloatButton = ref(false);


const mails = ref([
    {
        status : "New",
        color : "new",
        sender: "Toshiyuki Asai <asai@xxx.com>",
        subject: "Ginza] There was an appointment.",
        datetime: "August 10, 10:10"
    },
    {
        status : "New",
        color : "new",
        sender: "Toshiyuki Asai <asai@xxx.com>",
        subject: "Shinjuku] There was a reservation.",
        datetime: "August 10, 10:09"
    },
    {
        status : "Currently Supporting",
        color : "currently_supporting",
        sender: "Toshiyuki Asai  <asai@xxx.com>",
        subject: "Men's] There was a reservation.",
        datetime: "August 10, 10:08"
    },
    {
        status : "Completion",
        color : "completion",
        sender: "Toshiyuki Asai <asai@xxx.com>",
        subject: "RE: [Men's] I have an appointment.",
        datetime: "August 10, 10:07"
    },
])

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="bg-[#f2f4f6]">
            <div class="mx-auto sm:px-6 lg:px-5">
                <div
                    class="overflow-hidden sm:rounded-lg"
                >
                    <div class="py-6 text-gray-900" style="height: 100%;">
                        <VRow>
                            <VCol cols="12" lg="2">
                                <div class="my-5">
                                    <p>Inbox ( 100 )</p>
                                    <div class="folder-path">
                                        <p class="folder-item">├ Folder 1 (50)</p>
                                        <div class="sub-folder">
                                        <p class="folder-item">　├ Folder 2 (30)</p>
                                        <p class="folder-item">　└ Folder 3 (20)</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="my-10">
                                    <p>Sent Mail</p>
                                </div>
                                <div class="my-10">
                                    <p>Draf Email</p>
                                </div>
                                <div class="my-10">
                                    <p @click="router.get('/spams')" style="cursor: pointer;">Spam</p>
                                </div>
                                <div class="my-10">
                                    <p>Trash Can</p>
                                </div>
                            </VCol>
                            <VCol cols="12" lg="6">
                                <div class="mt-5 mb-3">
                                    <MailCreationDialog
                                    :createDialog="createDialogVisible"
                                    :floatButton="isVisibleFloatButton"
                                    @update:dialog="createDialogVisible = $event"
                                    @update:visibleFloat="isVisibleFloatButton = $event"
                                    />
                                </div>
                                <div>
                                    <VCard style="border-radius: 20px;">
                                        <MailTable :data="mails" />
                                    </VCard>
                                </div>
                            </VCol>
                            <VCol cols="12" lg="4">
                                <VCard class="mt-5" style="border-radius: 20px;">
                                    <MailDetail />
                                </VCard>
                            </VCol>
                        </VRow>
                    </div>
                </div>
            </div>
            <FloatMailButton
                v-if="isVisibleFloatButton"
                @update:onOpenDialog="createDialogVisible = $event"
                @update:hideFloat="isVisibleFloatButton = $event"
            />
        </div>
    </AuthenticatedLayout>
</template>


<style scoped>
.folder-path {
  padding-left: 20px; /* Indent for folder structure */
}

.folder-item {
  margin: 5px 0; /* Space between folder items */
}

.sub-folder {
  padding-left: 20px; /* Further indent subfolders */
}
</style>
