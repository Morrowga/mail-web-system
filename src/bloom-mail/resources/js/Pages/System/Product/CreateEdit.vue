<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import WriteActionCard from '@/PageComponents/WriteActionCard.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps(['product', 'shops'])

console.log(props?.user);

const form = useForm({
    purchase_no: props?.product?.purchase_no ?? String(Math.floor(100000 + Math.random() * 900000)),
    shop_id: props?.product?.shop_id ?? '',
    content_time_frame: '000',
    treatment_begin_date: props?.product?.treatment_begin_date ?? '',
    product_detail: props?.product?.product_detail ?? '',
    price: props?.product?.price ?? 0,
    sale_start_date: props?.product?.sale_start_date ?? '',
    sale_end_date: props?.product?.sale_end_date ?? '',
    status: props?.product?.status ?? 'before_release'
});

const formSubmit = (status) => {
    form.status = status;

    const isEdit = Boolean(props?.product);

    const routeLink = isEdit ? route('products.update', props.product.id) : route('products.store');
    const method = isEdit ? 'patch' : 'post';

    form[method](routeLink, {
        onSuccess: () => {
            form.reset();  // Reset the form upon success
        },
        onError: (error) => {
            console.error("Form submission error:", error); // Handle the error if needed
        },
    });
};

const formatDate = (date, column) => {
    if (date && ['treatment_begin_date', 'sale_start_date', 'sale_end_date'].includes(column)) {
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
                   {{  $t('system.nav.product') }} > {{ Boolean(props?.product) ? $t('system.title.product_edit') :  $t('system.title.product_edit') }}
                </h1>
                <WriteActionCard :title="Boolean(props?.product) ? $t('system.title.product_edit') :  $t('system.title.product_edit')">
                    <VCardText class="mx-10">
                        <form class="my-3">
                            <div class="d-flex justify-between">
                                <div style="width: 20%">
                                    <InputLabel for="purchase_no" class="pt-3" :value="$t('system.table.purchase_no')" />
                                </div>

                                <div style="width: 80%">
                                    <TextInput
                                        id="purchase_no"
                                        type="text"
                                        density="compact"
                                        class="mt-1 block w-full"
                                        v-model="form.purchase_no"
                                        readonly
                                        disabled
                                        required
                                        autofocus
                                        autocomplete="purchase_no"
                                    />
                                    <InputError class="mt-2" :message="form.errors.purchase_no" />
                                </div>
                            </div>
                            <div class="d-flex justify-between">
                                <div style="width: 20%">
                                    <InputLabel for="shop_id" class="pt-3">
                                        {{$t('system.table.shop_name')}} <strong style="color: red;" v-if="!props.product">* 必須</strong>
                                    </InputLabel>
                                </div>

                                <div style="width: 80%">
                                    <v-select
                                        :items="props?.shops"
                                        dense
                                        v-model="form.shop_id"
                                        item-title="name"
                                        item-value="id"
                                        variant="outlined" density="compact" required hide-details
                                    ></v-select>
                                    <InputError class="mt-2" :message="form.errors.shop_id" />
                                </div>
                            </div>

                            <div class="d-flex justify-between my-5">
                                <div style="width: 20%">
                                    <InputLabel for="shop_id" class="pt-3">
                                        {{$t('system.table.treatment_begin_date')}}<strong style="color: red;" v-if="!props.product">* 必須</strong>
                                    </InputLabel>
                                </div>

                                <div style="width: 80%">
                                    <VueDatePicker v-model="form.treatment_begin_date" @update:modelValue="(date) => formatDate(date, 'treatment_begin_date')" />

                                    <InputError class="mt-2" :message="form.errors.treatment_begin_date" />
                                </div>
                            </div>
                            <div class="d-flex justify-start my-5">
                                <div style="width: 20%">
                                    <InputLabel for="product_detail" class="pt-3">
                                        {{$t('system.table.content_time_frame')}} <strong style="color: red;" v-if="!props.product">* 必須</strong>
                                    </InputLabel>
                                </div>

                                <div style="width: 12%">
                                    <div class="d-flex">
                                        <TextInput
                                            id="content_time_frame"
                                            type="text"
                                            variant="outlined"
                                            density="compact"
                                            required
                                            rows="2"
                                            hide-details
                                            class="mt-1 block w-full"
                                            v-model="form.content_time_frame"
                                            autofocus
                                            autocomplete="product_detail"
                                        />
                                        <p style="padding: 1rem;">
                                            分
                                        </p>
                                    </div>
                                    <InputError class="mt-2" :message="form.errors.content_time_frame" />
                                </div>
                            </div>
                            <div class="d-flex justify-between">
                                <div style="width: 20%">
                                    <InputLabel for="product_detail" class="pt-3">
                                        {{$t('system.table.product_detail')}}<strong style="color: red;" v-if="!props.product">* 必須</strong>
                                    </InputLabel>
                                </div>

                                <div style="width: 80%">
                                    <VTextarea
                                        id="product_detail"
                                        type="text"
                                        variant="outlined"
                                        density="compact"
                                        required
                                        rows="2"
                                        hide-details
                                        class="mt-1 block w-full"
                                        v-model="form.product_detail"
                                        autofocus
                                        autocomplete="product_detail"
                                    />
                                    <InputError class="mt-2" :message="form.errors.product_detail" />
                                </div>
                            </div>
                            <div class="d-flex justify-between my-5">
                                <div style="width: 20%">
                                    <InputLabel for="sale_end_date" class="pt-3">
                                        {{$t('system.table.price')}} <strong style="color: red;" v-if="!props.product">* 必須</strong>
                                    </InputLabel>
                                </div>

                                <div style="width: 80%">
                                    <TextInput
                                        id="price"
                                        type="number"
                                        density="compact"
                                        class="mt-1 block w-full"
                                        v-model="form.price"
                                        required
                                        autofocus
                                        autocomplete="price"
                                    />
                                    <InputError class="mt-2" :message="form.errors.price" />
                                </div>
                            </div>
                            <div class="d-flex justify-between my-5">
                                <div style="width: 20%">
                                    <InputLabel for="sale_start_date" class="pt-3">
                                        {{$t('system.table.sale_start_date')}} <strong style="color: red;" v-if="!props.product">* 必須</strong>
                                    </InputLabel>
                                </div>

                                <div style="width: 80%">
                                    <VueDatePicker v-model="form.sale_start_date" @update:modelValue="(date) => formatDate(date, 'sale_start_date')" />

                                    <InputError class="mt-2" :message="form.errors.sale_start_date" />
                                </div>
                            </div>
                            <div class="d-flex justify-between my-5">
                                <div style="width: 20%">
                                    <InputLabel for="sale_end_date" class="pt-3">
                                        {{$t('system.table.sale_end_date')}} <strong style="color: red;" v-if="!props.product">* 必須</strong>
                                    </InputLabel>
                                </div>

                                <div style="width: 80%">
                                    <VueDatePicker v-model="form.sale_end_date" @update:modelValue="(date) => formatDate(date, 'sale_end_date')" />

                                    <InputError class="mt-2" :message="form.errors.sale_end_date" />
                                </div>
                            </div>
                            <div class="d-flex justify-between my-5">
                                <div style="width: 80%">
                                    <v-checkbox
                                        color="primary"
                                        :value="'release'"
                                        v-model="form.status"
                                        :label="'Release'"
                                    />

                                    <InputError class="mt-2" :message="form.errors.status" />
                                </div>
                            </div>


                            <div class="mt-10 d-flex justify-between">
                                <div style="width: 20%;">
                                    <VBtn
                                        color="#727272"
                                        class="action-button-width"
                                        @click="router.get('/products')"
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
                                            @click="formSubmit('before_release')"
                                            color="primary"
                                            class="submit-button-width mx-3"
                                        >
                                            {{ $t('system.buttons.save_product')}}
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
