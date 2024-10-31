<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head,router } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Templates" />

    <AuthenticatedLayout>
        <div class="bg-[#f2f4f6] h-screen">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        <div>
                            <VBtn color="primary">Template Registration</VBtn>
                            <VBtn color="primary" class="mx-2" @click="router.get('/template-categories')">Category List</VBtn>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
