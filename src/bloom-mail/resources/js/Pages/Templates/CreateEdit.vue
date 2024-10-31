<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Folders - Modification" />

    <AuthenticatedLayout>
        <div class="bg-[#f2f4f6] h-screen">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        <VCard>
                            <VCardText>
                                Template Creation/Edit
                            </VCardText>
                        </VCard>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
