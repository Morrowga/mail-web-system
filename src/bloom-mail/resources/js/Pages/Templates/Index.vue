<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head,router } from '@inertiajs/vue3';
import { ref } from 'vue';

const templateArray = ref([
    "[Automatic reservation] Confirmation of whether it is face or body",
    "Automatic reservation] In the case of a vacation for those who wish to be nominated.",
    "When asking if it's two body frames or a face body.",
    "When declining a nomination due to a prior reservation",
])

const templateCategoriesArray = ref([
    "Reservations",
    "New",
    "Cancel",
])

const isCollapsed = ref(templateCategoriesArray.value.map((_, index) => index === 0 ? false : true));

const toggleCollapse = (index) => {
  isCollapsed.value[index] = !isCollapsed.value[index];
};

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
                        <div style="padding: 20px;">
                            <VBtn class="text-capitalize" color="primary" @click="router.get(route('templates.create'))">Template Registration</VBtn>
                            <VBtn color="primary" class="mx-2 text-capitalize" @click="router.get('/template-categories')">Category List</VBtn>
                            <div class="my-5" v-for="(category, index) in templateCategoriesArray" :key="index">
                                <VCard>
                                    <!-- Card Title -->
                                    <VCardTitle
                                    class="header-title"
                                    style="background-color: #7f7f7f; cursor: pointer;"
                                    @click="toggleCollapse(index)"
                                    >
                                    <div class="font-weight-black d-flex align-center" style="color: #fff;">
                                        <!-- Icon that changes based on collapse state -->
                                        <VIcon>{{ isCollapsed[index] ? 'mdi-plus' : 'mdi-minus' }}</VIcon>
                                        <span class="ml-2">{{ category }}</span>
                                    </div>
                                    </VCardTitle>

                                    <!-- Collapsible Card Content -->
                                    <VExpandTransition>
                                        <VCardText v-if="!isCollapsed[index]" style="padding: 0;">
                                            <div
                                            v-for="(template, index) in templateArray"
                                            :key="index"
                                            :class="['d-flex', 'justify-between', index % 2 === 0 ? 'self-card-text' : 'self-card-text-white']"
                                            >
                                            <div>
                                                <a href="#" class="a-underline-none font-bold">
                                                {{ template }}
                                                </a>
                                            </div>
                                            <div>
                                                <span class="text-[#1b5d9b] font-[500]">Copy</span>
                                                <span class="text-[#1b5d9b] font-[500]"> | </span>
                                                <span class="text-red font-bold">Delete</span>
                                            </div>
                                            </div>
                                        </VCardText>
                                    </VExpandTransition>
                                </VCard>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
.header-title {
    padding: 5 !important;
    margin: 0 !important;
}

.self-card-text{
    padding: 20px !important;
    /* padding-bottom: 20px !important; */
    background-color: #e6e6e6;
}

.self-card-text-white{
    padding: 20px !important;
    /* padding-bottom: 20px !important; */
    background-color: #fff;
}

.a-underline-none{
    text-decoration: none;
    color: #1b5d9b;
}
</style>
