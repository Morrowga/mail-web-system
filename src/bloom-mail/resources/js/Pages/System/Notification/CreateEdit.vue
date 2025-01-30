<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import WriteActionCard from '@/PageComponents/WriteActionCard.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps(['notification'])

const form = useForm({
    title: props?.notification?.title ?? '',
    content: props?.notification?.content ?? '',
    start_time: props?.notification?.start_time ?? '',
    end_time: props?.notification?.end_time ?? '',
    type: props?.notification?.type ?? '',
    status: props?.notification?.status ?? 'before_release'
});

const formSubmit = (status) => {
    if(status != '')
    {
        form.status = status;
    }

    const isEdit = Boolean(props?.notification);

    const routeLink = isEdit ? route('notifications.update', props.notification.id) : route('notifications.store');
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

const formatDate = (date, column) => {
    if (date && ['start_time', 'end_time'].includes(column)) {
        form[column] = new Date(date).toISOString().slice(0, 19).replace("T", " ");
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto sm:px-6 space-y-6 lg:px-8">
                <h1
                    class="font-semibold leading-tight text-gray-800"
                >
                   {{  $t('system.nav.notification') }} > {{ Boolean(props?.notification) ? $t('system.title.notification_edit') :  $t('system.title.notification_edit') }}
                </h1>
                <WriteActionCard :title="Boolean(props?.notification) ? $t('system.title.notification_edit') :  $t('system.title.notification_edit')">
                    <VCardText class="mx-10">
                        <form class="my-3">
                            <div class="d-flex justify-between" v-if="props?.notification">
                                <div style="width: 20%">
                                    <InputLabel for="notification_id" class="pt-3" :value="$t('system.table.notification_id')" />
                                </div>

                                <div style="width: 80%">
                                    <TextInput
                                        id="notification_id"
                                        type="text"
                                        density="compact"
                                        class="mt-1 block w-full"
                                        :value="props?.notification?.id"
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
                                        {{$t('system.table.title')}}<strong style="color: red;" v-if="!props.notification">* 必須</strong>
                                    </InputLabel>
                                </div>

                                <div style="width: 80%">
                                    <TextInput
                                        id="title"
                                        type="text"
                                        density="compact"
                                        class="mt-1 block w-full"
                                        v-model="form.title"
                                        required
                                        autofocus
                                    />
                                </div>
                            </div>
                            <div class="d-flex justify-between">
                                <div style="width: 20%">
                                    <InputLabel for="shop_id" class="pt-3">
                                        {{$t('system.table.type')}} <strong style="color: red;" v-if="!props.notification">* 必須</strong>
                                    </InputLabel>
                                </div>

                                <div style="width: 80%">
                                    <v-select
                                        :items="['reservation', 'salon']"
                                        dense
                                        v-model="form.type"
                                        variant="outlined" density="compact" required hide-details
                                    ></v-select>
                                    <InputError class="mt-2" :message="form.errors.type" />
                                </div>
                            </div>
                            <div class="d-flex justify-between my-5">
                                <div style="width: 20%">
                                    <InputLabel for="content" class="pt-3">
                                        {{$t('system.table.content')}}<strong style="color: red;" v-if="!props.notification">* 必須</strong>
                                    </InputLabel>
                                </div>

                                <div style="width: 80%">
                                    <VTextarea
                                        id="content"
                                        type="text"
                                        variant="outlined"
                                        density="compact"
                                        required
                                        rows="2"
                                        hide-details
                                        class="mt-1 block w-full"
                                        v-model="form.content"
                                        autofocus
                                        autocomplete="content"
                                    />
                                    <InputError class="mt-2" :message="form.errors.content" />
                                </div>
                            </div>

                            <div class="d-flex justify-between my-5">
                                <div style="width: 20%">
                                    <InputLabel for="start_time" class="pt-3">
                                        {{$t('system.table.start_time')}}<strong style="color: red;" v-if="!props.notification">* 必須</strong>
                                    </InputLabel>
                                </div>

                                <div style="width: 80%">
                                    <VueDatePicker v-model="form.start_time" @update:modelValue="(date) => formatDate(date, 'start_time')" />

                                    <InputError class="mt-2" :message="form.errors.start_time" />
                                </div>
                            </div>
                            <div class="d-flex justify-between my-5">
                                <div style="width: 20%">
                                    <InputLabel for="end_time" class="pt-3">
                                        {{$t('system.table.end_time')}}<strong style="color: red;" v-if="!props.notification">* 必須</strong>
                                    </InputLabel>
                                </div>

                                <div style="width: 80%">
                                    <VueDatePicker v-model="form.end_time" @update:modelValue="(date) => formatDate(date, 'end_time')" />

                                    <InputError class="mt-2" :message="form.errors.end_time" />
                                </div>
                            </div>

                            <div class="d-flex justify-between my-5" v-if="props?.notification">
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
                                        @click="router.get('/notifications')"
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
