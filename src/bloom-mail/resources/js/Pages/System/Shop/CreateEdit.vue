<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import WriteActionCard from '@/PageComponents/WriteActionCard.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps(['shop'])

const form = useForm({
    name: props?.shop?.name ?? '',
    status: props?.shop?.status ?? 'before_release'
});

const formSubmit = (status) => {
    if(status != '')
    {
        form.status = status;
    }

    const isEdit = Boolean(props?.shop);

    const routeLink = isEdit ? route('shops.update', props.shop.id) : route('shops.store');
    const method = isEdit ? 'patch' : 'post';

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
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto sm:px-6 space-y-6 lg:px-8">
                <h1
                    class="font-semibold leading-tight text-gray-800"
                >
                   {{  $t('system.nav.shop') }} > {{ Boolean(props?.shop) ? $t('system.nav.new_shop') :  $t('system.nav.new_shop') }}
                </h1>
                <WriteActionCard :title="Boolean(props?.shop) ? $t('system.nav.new_shop') :  $t('system.nav.new_shop')">
                    <VCardText class="mx-10">
                        <form class="my-3">
                            <div class="d-flex justify-between" v-if="props?.shop">
                                <div style="width: 20%">
                                    <InputLabel for="shop_id" class="pt-3" :value="$t('system.table.shop_id')" />
                                </div>

                                <div style="width: 80%">
                                    <TextInput
                                        id="shop_id"
                                        type="text"
                                        density="compact"
                                        class="mt-1 block w-full"
                                        :value="props?.shop?.id"
                                        readonly
                                        disabled
                                        required
                                        autofocus
                                    />
                                </div>
                            </div>
                            <div class="d-flex justify-between">
                                <div style="width: 20%">
                                    <InputLabel for="title" class="pt-3">
                                        {{$t('system.table.shop_name')}}<strong style="color: red;" v-if="!props.shop">* 必須</strong>
                                    </InputLabel>
                                </div>

                                <div style="width: 80%">
                                    <TextInput
                                        id="name"
                                        type="text"
                                        density="compact"
                                        class="mt-1 block w-full"
                                        v-model="form.name"
                                        required
                                        autofocus
                                    />
                                </div>
                            </div>

                            <div class="d-flex justify-between my-5" v-if="props?.shop">
                                <div style="width: 80%">
                                    <v-checkbox
                                        color="primary"
                                        :value="'release'"
                                        v-model="form.status"
                                        :label="'Release'"
                                        :true-value="'release'"
                                        :false-value="'before_release'"
                                    />

                                    <InputError class="mt-2" :message="form.errors.status" />
                                </div>
                            </div>

                            <div class="mt-10 d-flex justify-between">
                                <div style="width: 20%;">
                                    <VBtn
                                        color="#727272"
                                        class="action-button-width"
                                        @click="router.get('/shops')"
                                    >
                                        {{ $t('system.buttons.back')}}
                                    </VBtn>
                                </div>
                                <div style="width: 80%;">
                                    <div class="d-flex justify-center">
                                        <VBtn
                                            @click="formSubmit('draft')"
                                            color="#727272"
                                            class="submit-button-width"
                                        >
                                            {{ $t('system.buttons.draft') }}
                                        </VBtn>
                                        <VBtn
                                            @click="formSubmit('')"
                                            color="primary"
                                            class="submit-button-width mx-3"
                                        >
                                            {{ $t('system.buttons.publish')}}
                                        </VBtn>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </VCardText>
                </WriteActionCard>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
