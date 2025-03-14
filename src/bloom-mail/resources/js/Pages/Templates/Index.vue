<script setup>
import { permissionGrant } from '@/Helper/permissionUtils';
import MailLayout from '@/Layouts/MailLayout.vue';
import ConfirmDialog from '@/PageComponents/ConfirmDialog.vue';
import { Head,router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps(['template_categories', 'auth']);

const permissions = props?.auth?.user?.permissions

const { t, locale } = useI18n();

const snackbarVisible = ref(false);
const snackbarMessage = ref("");

// const templateArray = ref([
//     "[Automatic reservation] Confirmation of whether it is face or body",
//     "Automatic reservation] In the case of a vacation for those who wish to be nominated.",
//     "When asking if it's two body frames or a face body.",
//     "When declining a nomination due to a prior reservation",
// ])

// const templateCategoriesArray = ref([
//     "Reservations",
//     "New",
//     "Cancel",
// ])

const isCollapsed = ref(props?.template_categories.map((_, index) => index === 0 ? false : true));

const toggleCollapse = (index) => {
  isCollapsed.value[index] = !isCollapsed.value[index];
};

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

const goToEdit = (id) => {
   if(permissionGrant(permissions, 'template_createdit'))
    {
        router.get(route('templates.edit', id))
    }
}

</script>

<template>
    <Head :title="$t('nav.template')" />

    <MailLayout>
        <div class="bg-[#f2f4f6] h-screen">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        <div style="padding: 20px;">
                            <VBtn class="text-capitalize" color="primary" v-if="permissionGrant(permissions, 'template_createdit')" @click="router.get(route('templates.create'))">{{$t('buttons.template_registration')}}</VBtn>
                            <VBtn color="primary" class="mx-2 text-capitalize" v-if="permissionGrant(permissions, 'templatecategory_createdit')" @click="router.get('/template-categories')">{{$t('buttons.category_list')}}</VBtn>
                            <div class="my-5" v-for="(category, index) in props?.template_categories" :key="category?.id">
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
                                        <span class="ml-2">{{ category?.name }}</span>
                                    </div>
                                    </VCardTitle>

                                    <!-- Collapsible Card Content -->
                                    <VExpandTransition>
                                        <VCardText v-if="!isCollapsed[index]" style="padding: 0;">
                                            <div
                                            v-for="(template, index) in category?.templates"
                                            :key="index"
                                            :class="['d-flex', 'justify-between', 'cursor-pointer', index % 2 === 0 ? 'self-card-text' : 'self-card-text-white']"
                                            >
                                            <div @click="goToEdit(template?.id)">
                                                <a class="a-underline-none font-bold">
                                                {{ template?.title }}
                                                </a>
                                            </div>
                                            <div>
                                                <span class="text-[#1b5d9b] font-[500] cursor-pointer" @click="copyToClipboard(template.title)">{{ $t('table.copy')}}</span>
                                                <span class="text-[#1b5d9b] font-[500] cursor-pointer "> | </span>
                                                <ConfirmDialog v-if="permissionGrant(permissions, 'template_delete')" :item="template" :routeUrl="'/templates'" />
                                            </div>
                                            </div>
                                        </VCardText>
                                    </VExpandTransition>
                                </VCard>
                            </div>
                        </div>
                    </div>
                    <VSnackbar v-model="snackbarVisible" :timeout="3000" color="info">
                        {{ snackbarMessage }}
                        <template v-slot:actions>
                            <VBtn text @click="snackbarVisible = false">Close</VBtn>
                        </template>
                    </VSnackbar>
                </div>
            </div>
        </div>
    </MailLayout>
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
