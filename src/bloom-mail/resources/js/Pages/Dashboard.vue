<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head,router } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="bg-[#f2f4f6]">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900" style="height: 100%;">
                        <VRow>
                            <VCol cols="2">
                                <div class="my-10">
                                    <p>Inbox ( 100 )</p>
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
                            <VCol cols="6">
                                <div class="mt-10 mb-3">
                                    <VBtn prepend-icon="mdi-mail" color="primary">Mail Box</VBtn>
                                </div>
                                <div>
                                    <VCard>
                                        <VCardText>
                                            <h3>Inbox Lists</h3>
                                            <h3>Inbox Lists</h3>
                                            <h3>Inbox Lists</h3>
                                            <h3>Inbox Lists</h3>
                                            <h3>Inbox Lists</h3>
                                        </VCardText>
                                    </VCard>
                                </div>
                            </VCol>
                            <VCol cols="4">
                                <VCard class="mt-10">
                                    <VCardText>
                                        <h3>Detail</h3>
                                    </VCardText>
                                </VCard>
                            </VCol>
                        </VRow>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
