<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PermissionSelector from '@/PageComponents/PermissionSelector.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps(['role', 'permissions', 'role_permissions'])

const form = useForm({
    name: props?.role?.name ?? '',
    permissions: props?.role_permissions ?? [],
})

const formSubmit = () => {
    console.log(form);

    const isEdit = Boolean(props?.role);

    const routeLink = isEdit ? route('roles.update', props.role.id) : route('roles.store');
    const method = isEdit ? 'put' : 'post';

    form[method](routeLink, {
        onSuccess: () => {
            form.reset();
        },
        onError: (error) => {
            console.error("Form submission error:", error);
        },
    });
};
</script>

<template>
    <Head :title="$t('nav.roles')" />

    <AuthenticatedLayout>
        <div class="bg-[#f2f4f6] py-6">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden sm:rounded-lg"
                >
                    <div class="py-6 text-gray-900">
                        <div>
                            <h2 class="font-medium">{{ props?.role ? $t('other.edit_role') : $t('other.create_role') }}</h2>
                            <VForm @submit.prevent="formSubmit">
                                <VCard class="my-4">
                                    <VCardText>
                                        <VRow class="mt-3">
                                            <VCol cols="12" class="py-0">
                                                <div class="d-flex justify-start">
                                                    <div style="width: 17%; padding: 10px;">
                                                        <InputLabel for="address" :value="$t('table.role_name')"/>
                                                    </div>
                                                    <div style="width: 30%;">
                                                        <VTextField
                                                            :disabled="form.name == '管理者'"
                                                            density="compact"
                                                            variant="outlined"
                                                            id="name"
                                                            type="email"
                                                            class="mt-1 block w-full"
                                                            v-model="form.name"
                                                            required
                                                        ></VTextField>
                                                        <InputError class="mb-2" :message="form.errors.name" />
                                                    </div>
                                                </div>
                                            </VCol>
                                        </VRow>
                                    </VCardText>
                                    <VCardText>
                                        <h2 class="mx-2 mb-2">{{ $t('other.select_permissions') }}</h2>
                                        <PermissionSelector  :permissions="props?.permissions" v-model="form.permissions" />
                                        <InputError class="mb-2" :message="form.errors.permissions" />
                                    </VCardText>
                                    <VCardText class="text-right">
                                        <VBtn color="primary" prepend-icon="mdi-content-save-all-outline"  type="submit" class="text-white text-capitalize">{{ props?.role ? $t('buttons.update') : $t('buttons.registration') }}</VBtn>
                                    </VCardText>
                                </VCard>
                            </VForm>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
