<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import WriteActionCard from '@/PageComponents/WriteActionCard.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps(['member', 'user'])

const form = useForm({
    name: props?.user?.name ?? '',
    email: props?.user?.email ?? '',
    dob: props?.member?.dob ?? '',
    phone: props?.member?.phone ?? ''
});

const formSubmit = (status) => {

    const isEdit = Boolean(props?.user);

    const routeLink = isEdit ? route('members.update', props.user.id) : route('members.store');
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
    if (date && ['dob'].includes(column)) {
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
                   {{  $t('system.nav.membership') }} > {{ Boolean(props?.user) ? $t('system.nav.new_member') :  $t('system.nav.new_member') }}
                </h1>
                <WriteActionCard :title="Boolean(props?.user) ? $t('system.nav.new_member') :  $t('system.nav.new_member')">
                    <VCardText class="mx-10">
                        <form class="my-3">
                            <div class="d-flex justify-between" v-if="props?.notification">
                                <div style="width: 20%">
                                    <InputLabel for="id" class="pt-3" :value="$t('system.table.member_id')" />
                                </div>

                                <div style="width: 80%">
                                    <TextInput
                                        id="member_id"
                                        type="text"
                                        density="compact"
                                        class="mt-1 block w-full"
                                        :value="props?.member?.id"
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
                                        {{$t('system.table.name')}}<strong style="color: red;" v-if="!props.user">* 必須</strong>
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
                                    <InputError class="mt-2" :message="form.errors.name" />
                                </div>
                            </div>
                            <div class="d-flex justify-between">
                                <div style="width: 20%">
                                    <InputLabel for="title" class="pt-3">
                                        {{$t('system.table.name')}}<strong style="color: red;" v-if="!props.user">* 必須</strong>
                                    </InputLabel>
                                </div>

                                <div style="width: 80%">
                                    <TextInput
                                        id="name_kana"
                                        type="text"
                                        density="compact"
                                        class="mt-1 block w-full"
                                        v-model="form.name_kana"
                                        required
                                        autofocus
                                    />
                                    <InputError class="mt-2" :message="form.errors.name_kana" />
                                </div>
                            </div>
                            <div class="d-flex justify-between">
                                <div style="width: 20%">
                                    <InputLabel for="email" class="pt-3">
                                        {{$t('system.table.email')}} <strong style="color: red;" v-if="!props.user">* 必須</strong>
                                    </InputLabel>
                                </div>

                                <div style="width: 80%">
                                    <TextInput
                                        id="name"
                                        type="email"
                                        density="compact"
                                        class="mt-1 block w-full"
                                        v-model="form.email"
                                        required
                                        autofocus
                                    />
                                    <InputError class="mt-2" :message="form.errors.email" />
                                </div>
                            </div>
                            <div class="d-flex justify-between mb-5">
                                <div style="width: 20%">
                                    <InputLabel for="dob" class="pt-3">
                                        {{$t('system.table.dob')}}<strong style="color: red;" v-if="!props.user">* 必須</strong>
                                    </InputLabel>
                                </div>
                                <div style="width: 80%">
                                    <VueDatePicker locale="ja" v-model="form.dob" @update:modelValue="(date) => formatDate(date, 'dob')" />

                                    <InputError class="mt-2" :message="form.errors.dob" />
                                </div>
                            </div>
                            <div class="d-flex justify-between">
                                <div style="width: 20%">
                                    <InputLabel for="phone" class="pt-3">
                                        {{$t('system.table.tel')}} <strong style="color: red;" v-if="!props.user">* 必須</strong>
                                    </InputLabel>
                                </div>

                                <div style="width: 80%">
                                    <TextInput
                                        id="phone"
                                        type="phone"
                                        density="compact"
                                        class="mt-1 block w-full"
                                        v-model="form.phone"
                                        required
                                        autofocus
                                    />
                                    <InputError class="mt-2" :message="form.errors.phone" />
                                </div>
                            </div>

                            <div class="mt-10 d-flex justify-center">
                                <div style="width: 100%;">
                                    <div class="d-flex justify-center">
                                        <VBtn
                                            @click="formSubmit()"
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
