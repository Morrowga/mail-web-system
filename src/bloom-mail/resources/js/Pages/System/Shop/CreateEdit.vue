<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import WriteActionCard from '@/PageComponents/WriteActionCard.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, onMounted } from 'vue';
import moment from 'moment';

const props = defineProps(['shop'])

console.log(props?.shop?.opening_time)

const form = useForm({
    name: props?.shop?.name ?? '',
    shop_type: props?.shop?.shop_type ?? '',
    address: props?.shop?.address ?? '',
    opening_time: formatIncomingTime(props?.shop?.opening_time),
    closing_time: formatIncomingTime(props?.shop?.closing_time),
    phone_no: props?.shop?.phone_no ?? '',
    reception_start_time: formatIncomingTime(props?.shop?.reception_start_time),
    reception_end_time: formatIncomingTime(props?.shop?.reception_end_time),
    close_day: props?.shop?.close_day ?? '',
    close_day_text: props?.shop?.close_day_text ?? '',
    room_numbers: props?.shop?.room_numbers ?? 0,
    access: props?.shop?.access ?? '',
    parking_nearby: props?.shop?.parking_nearby ?? '',
    store_direction: props?.shop?.store_direction ?? '',
    gmap_location: props?.shop?.gmap_location ?? '',
    gmap_photos: props?.shop?.gmap_photos ?? '',
    youtube: props?.shop?.youtube ?? '',
    shop_images: null,
    top_statement: props?.shop?.top_statement ?? '',
    store_sub_title: props?.shop?.store_sub_title ?? '',
    store_btm_text: props?.shop?.store_btm_text ?? '',
    store_sub_title_two: props?.shop?.store_btm_text ?? '',
    store_btm_text_two: props?.shop?.store_btm_text ?? '',
    status: props?.shop?.status ?? 'release'
});

// Format incoming time from backend to time picker format
function formatIncomingTime(time) {
    if (!time) return '';

    // If time is in HH:mm format
    if (typeof time === 'string') {
        const [hours, minutes] = time.split(':');
        return {
            hours: parseInt(hours),
            minutes: parseInt(minutes)
        };
    }
    return '';
}

// Format time for backend before submitting
function formatTimeForBackend(timeObj) {
    if (!timeObj) return '';

    // If timeObj is already a string in correct format, return it
    if (typeof timeObj === 'string' && timeObj.includes(':')) {
        return timeObj;
    }

    // If timeObj is a date picker object
    if (timeObj.hours !== undefined && timeObj.minutes !== undefined) {
        const hours = timeObj.hours.toString().padStart(2, '0');
        const minutes = timeObj.minutes.toString().padStart(2, '0');
        return `${hours}:${minutes}`;
    }

    return '';
}

// Handle time picker changes
const formatTime = (date, column) => {
    if (!date) return;

    const formattedTime = formatTimeForBackend(date);
    form[column] = formattedTime;
};

const formSubmit = (status) => {
    // Format all time fields before submission
    form.opening_time = formatTimeForBackend(form.opening_time);
    form.closing_time = formatTimeForBackend(form.closing_time);
    form.reception_start_time = formatTimeForBackend(form.reception_start_time);
    form.reception_end_time = formatTimeForBackend(form.reception_end_time);

    if(status != '')
    {
        form.status = status;
    } else {
        form.status = form.status == 'draft' ? 'release': form.status;
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

const daysOfWeek = ['不定休', '月', '火', '水', '木', '金', '土', '日'];

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
                            <!-- <div class="d-flex justify-between" v-if="props?.shop">
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
                            </div> -->
                            <div class="d-flex justify-between">
                                <div style="width: 20%">
                                    <InputLabel for="shop_type" class="pt-3">
                                        {{$t('system.table.shop_type')}} <strong style="color: red;" v-if="!props.shop">* 必須</strong>
                                    </InputLabel>
                                </div>

                                <div style="width: 80%">
                                    <v-select
                                        :items="['type 1', 'type 2', 'type 3']"
                                        dense
                                        v-model="form.shop_type"
                                        variant="outlined" density="compact" required hide-details
                                    ></v-select>
                                    <InputError class="mt-2" :message="form.errors.shop_type" />
                                </div>
                            </div>
                            <div class="d-flex justify-between mt-5">
                                <div style="width: 20%">
                                    <InputLabel for="name" class="pt-3">
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

                                    <InputError class="mt-2" :message="form.errors.name" />
                                </div>
                            </div>
                            <div class="d-flex justify-between">
                                <div style="width: 20%">
                                    <InputLabel for="address" class="pt-3">
                                        {{$t('system.table.address')}}
                                        <strong style="color: red;" v-if="!props.shop">* 必須</strong>
                                    </InputLabel>
                                </div>

                                <div style="width: 80%">
                                    <TextInput
                                        id="address"
                                        type="text"
                                        density="compact"
                                        class="mt-1 block w-full"
                                        v-model="form.address"
                                        required
                                        autofocus
                                    />

                                    <InputError class="mt-2" :message="form.errors.address" />
                                </div>
                            </div>
                            <div class="d-flex justify-between mb-5">
                                <div style="width: 20%">
                                    <InputLabel for="opening_time" class="pt-3">
                                        {{$t('system.table.opening_time')}}
                                        <strong style="color: red;" v-if="!props.shop">* 必須</strong>
                                    </InputLabel>
                                </div>

                                <div style="width: 80%">
                                    <VueDatePicker
                                        locale="ja"
                                        time-picker
                                        :enable-time-picker="true"
                                        :enable-seconds="false"
                                        :clearable="true"
                                        v-model="form.opening_time"
                                        @update:modelValue="(date) => formatTime(date, 'opening_time')"
                                        format="HH:mm"
                                    />

                                    <InputError class="mt-2" :message="form.errors.opening_time" />
                                </div>
                            </div>

                            <div class="d-flex justify-between mt-5">
                                <div style="width: 20%">
                                    <InputLabel for="closing_time" class="pt-3">
                                        {{$t('system.table.closing_time')}}
                                        <strong style="color: red;" v-if="!props.shop">* 必須</strong>
                                    </InputLabel>
                                </div>

                                <div style="width: 80%">
                                    <VueDatePicker
                                        locale="ja"
                                        time-picker
                                        :enable-time-picker="true"
                                        :enable-seconds="false"
                                        :clearable="true"
                                        v-model="form.closing_time"
                                        @update:modelValue="(date) => formatTime(date, 'closing_time')"
                                        format="HH:mm"
                                    />

                                    <InputError class="mt-2" :message="form.errors.closing_time" />
                                </div>
                            </div>
                            <div class="d-flex justify-between my-5">
                                <div style="width: 20%">
                                    <InputLabel for="phone_no" class="pt-3">
                                        {{$t('system.table.phone_no')}}
                                        <strong style="color: red;" v-if="!props.shop">* 必須</strong>
                                    </InputLabel>
                                </div>

                                <div style="width: 80%">
                                    <TextInput
                                        id="phone_no"
                                        type="text"
                                        density="compact"
                                        class="mt-1 block w-full"
                                        v-model="form.phone_no"
                                        required
                                        autofocus
                                    />

                                    <InputError class="mt-2" :message="form.errors.phone_no" />
                                </div>
                            </div>

                            <div class="d-flex justify-between mt-5">
                                <div style="width: 20%">
                                    <InputLabel for="closing_time" class="pt-3">
                                        {{$t('system.table.reception_start_time')}}
                                        <strong style="color: red;" v-if="!props.shop">* 必須</strong>
                                    </InputLabel>
                                </div>

                                <div style="width: 80%">
                                    <VueDatePicker
                                        locale="ja"
                                        time-picker
                                        :enable-time-picker="true"
                                        :enable-seconds="false"
                                        :clearable="true"
                                        v-model="form.reception_start_time"
                                        @update:modelValue="(date) => formatTime(date, 'reception_start_time')"
                                        format="HH:mm"
                                    />

                                    <InputError class="mt-2" :message="form.errors.reception_start_time" />
                                </div>
                            </div>

                            <div class="d-flex justify-between mt-5">
                                <div style="width: 20%">
                                    <InputLabel for="reception_end_time" class="pt-3">
                                        {{$t('system.table.reception_end_time')}}
                                        <strong style="color: red;" v-if="!props.shop">* 必須</strong>
                                    </InputLabel>
                                </div>

                                <div style="width: 80%">
                                    <VueDatePicker
                                        locale="ja"
                                        time-picker
                                        :enable-time-picker="true"
                                        :enable-seconds="false"
                                        :clearable="true"
                                        v-model="form.reception_end_time"
                                        @update:modelValue="(date) => formatTime(date, 'reception_end_time')"
                                        format="HH:mm"
                                    />

                                    <InputError class="mt-2" :message="form.errors.reception_end_time" />
                                </div>
                            </div>

                            <div class="d-flex justify-between mt-5">
                                <div style="width: 20%">
                                    <InputLabel for="close_day" class="pt-3">
                                        {{$t('system.table.close_day')}}
                                        <strong style="color: red;" v-if="!props.shop">* 必須</strong>
                                    </InputLabel>
                                </div>

                                <div style="width: 80%">
                                    <div>
                                        <v-radio-group v-model="form.close_day" inline>
                                            <v-radio
                                                v-for="(day, index) in daysOfWeek"
                                                :key="index"
                                                :label="day"
                                                :value="day"
                                                class="mr-5"
                                            >
                                            </v-radio>
                                            <v-radio
                                                :label="'その他自由記載'"
                                                :value="'その他自由記載'"
                                                class="mr-5"
                                            >
                                            </v-radio>

                                            <TextInput
                                                v-if="form.close_day === 'その他自由記載'"
                                                v-model="form.close_day_text"
                                                density="compact"
                                                class="mt-3 ml-3"
                                                required
                                                placeholder="入力してください"
                                                autofocus
                                            ></TextInput>
                                        </v-radio-group>
                                    </div>
                                    <InputError class="mt-2" :message="form.errors.close_day" />
                                </div>
                            </div>
                            <div class="d-flex justify-between my-5">
                                <div style="width: 20%">
                                    <InputLabel for="room_numbers" class="pt-3">
                                        {{$t('system.table.room_numbers')}}
                                        <strong style="color: red;" v-if="!props.shop">* 必須</strong>
                                    </InputLabel>
                                </div>

                                <div style="width: 80%">
                                    <TextInput
                                        id="room_numbers"
                                        type="number"
                                        density="compact"
                                        class="mt-1 block w-full"
                                        v-model="form.room_numbers"
                                        required
                                        autofocus
                                    />

                                    <InputError class="mt-2" :message="form.errors.room_numbers" />
                                </div>
                            </div>

                            <div class="d-flex justify-between my-5">
                                <div style="width: 20%">
                                    <InputLabel for="access" class="pt-3">
                                        {{$t('system.table.access')}}
                                        <strong style="color: red;" v-if="!props.shop">* 必須</strong>
                                    </InputLabel>
                                </div>

                                <div style="width: 80%">
                                    <TextInput
                                        id="access"
                                        type="text"
                                        placeholder="入力してください"
                                        density="compact"
                                        class="mt-1 block w-full"
                                        v-model="form.access"
                                        required
                                        autofocus
                                    />

                                    <InputError class="mt-2" :message="form.errors.access" />
                                </div>
                            </div>

                            <div class="d-flex justify-between my-5">
                                <div style="width: 20%">
                                    <InputLabel for="parking_nearby" class="pt-3">
                                        {{$t('system.table.parking_nearby')}}
                                        <strong style="color: red;" v-if="!props.shop">* 必須</strong>
                                    </InputLabel>
                                </div>

                                <div style="width: 80%">
                                    <TextInput
                                        id="parking_nearby"
                                        type="text"
                                        density="compact"
                                        placeholder="入力してください"
                                        class="mt-1 block w-full"
                                        v-model="form.parking_nearby"
                                        required
                                        autofocus
                                    />

                                    <InputError class="mt-2" :message="form.errors.parking_nearby" />
                                </div>
                            </div>

                            <div class="d-flex justify-between my-5">
                                <div style="width: 20%">
                                    <InputLabel for="store_direction" class="pt-3">
                                        {{$t('system.table.store_direction')}}
                                        <strong style="color: red;" v-if="!props.shop">* 必須</strong>
                                    </InputLabel>
                                </div>

                                <div style="width: 80%">
                                    <TextInput
                                        id="store_direction"
                                        type="text"
                                        placeholder="入力してください"
                                        density="compact"
                                        class="mt-1 block w-full"
                                        v-model="form.store_direction"
                                        required
                                        autofocus
                                    />

                                    <InputError class="mt-2" :message="form.errors.store_direction" />
                                </div>
                            </div>

                            <div class="d-flex justify-between my-5">
                                <div style="width: 20%">
                                    <InputLabel for="access" class="pt-3">
                                        Googlemap店舗位置 <br> HTMLコード
                                        <strong style="color: red;" v-if="!props.shop">* 必須</strong>
                                    </InputLabel>
                                </div>

                                <div style="width: 80%">
                                    <VTextarea
                                        id="gmap_location"
                                        type="text"
                                        density="compact"
                                        placeholder="入力してください"
                                        variant="outlined"
                                        hide-details
                                        class="mt-1 block w-full"
                                        v-model="form.gmap_location"
                                        required
                                        rows="3"
                                        autofocus
                                    />

                                    <InputError class="mt-2" :message="form.errors.gmap_location" />
                                </div>
                            </div>

                            <div class="d-flex justify-between my-5">
                                <div style="width: 20%">
                                    <InputLabel for="access" class="pt-3">
                                        Googlemap店内写真 <br> HTMLコード
                                        <strong style="color: red;" v-if="!props.shop">* 必須</strong>
                                    </InputLabel>
                                </div>

                                <div style="width: 80%">
                                    <VTextarea
                                        id="gmap_photos"
                                        type="text"
                                        density="compact"
                                        variant="outlined"
                                        placeholder="入力してください"
                                        hide-details
                                        class="mt-1 block w-full"
                                        v-model="form.gmap_photos"
                                        required
                                        rows="3"
                                        autofocus
                                    />

                                    <InputError class="mt-2" :message="form.errors.gmap_photos" />
                                </div>
                            </div>

                            <div class="d-flex justify-between my-5">
                                <div style="width: 20%">
                                    <InputLabel for="access" class="pt-3">
                                        Youtube動画 <br> HTMLコード
                                        <strong style="color: red;" v-if="!props.shop">* 必須</strong>
                                    </InputLabel>
                                </div>

                                <div style="width: 80%">
                                    <VTextarea
                                        id="youtube"
                                        type="text"
                                        density="compact"
                                        placeholder="入力してください"
                                        variant="outlined"
                                        hide-details
                                        class="mt-1 block w-full"
                                        v-model="form.youtube"
                                        required
                                        rows="3"
                                        autofocus
                                    />

                                    <InputError class="mt-2" :message="form.errors.youtube" />
                                </div>
                            </div>
                            <div class="d-flex justify-between my-5">
                                <div style="width: 20%">
                                    <InputLabel for="access" class="pt-3">
                                        {{$t('system.table.top_statement')}}
                                        <strong style="color: red;" v-if="!props.shop">* 必須</strong>
                                    </InputLabel>
                                </div>

                                <div style="width: 80%">
                                    <TextInput
                                        id="top_statement"
                                        type="text"
                                        density="compact"
                                        class="mt-1 block w-full"
                                        v-model="form.top_statement"
                                        required
                                        placeholder="入力してください"
                                        autofocus
                                    />

                                    <InputError class="mt-2" :message="form.errors.top_statement" />
                                </div>
                            </div>

                            <div class="d-flex justify-between my-5">
                                <div style="width: 20%">
                                    <InputLabel for="access" class="pt-3">
                                        {{$t('system.table.store_sub_title')}}
                                        <strong style="color: red;" v-if="!props.shop">* 必須</strong>
                                    </InputLabel>
                                </div>

                                <div style="width: 80%">
                                    <TextInput
                                        id="store_sub_title"
                                        type="text"
                                        density="compact"
                                        class="mt-1 block w-full"
                                        v-model="form.store_sub_title"
                                        required
                                        placeholder="入力してください"
                                        autofocus
                                    />

                                    <InputError class="mt-2" :message="form.errors.store_sub_title" />
                                </div>
                            </div>
                            <div class="d-flex justify-between my-5">
                                <div style="width: 20%">
                                    <InputLabel for="access" class="pt-3">
                                        {{$t('system.table.store_btm_text')}}
                                        <strong style="color: red;" v-if="!props.shop">* 必須</strong>
                                    </InputLabel>
                                </div>

                                <div style="width: 80%">
                                    <TextInput
                                        id="store_btm_text"
                                        type="text"
                                        density="compact"
                                        class="mt-1 block w-full"
                                        v-model="form.store_btm_text"
                                        required
                                        placeholder="入力してください"
                                        autofocus
                                    />

                                    <InputError class="mt-2" :message="form.errors.store_btm_text" />
                                </div>
                            </div>

                            <div class="d-flex justify-between my-5">
                                <div style="width: 20%">
                                    <InputLabel for="access" class="pt-3">
                                        {{$t('system.table.store_sub_title_two')}}
                                        <strong style="color: red;" v-if="!props.shop">* 必須</strong>
                                    </InputLabel>
                                </div>

                                <div style="width: 80%">
                                    <TextInput
                                        id="store_sub_title_two"
                                        type="text"
                                        density="compact"
                                        class="mt-1 block w-full"
                                        v-model="form.store_sub_title_two"
                                        required
                                        placeholder="入力してください"
                                        autofocus
                                    />

                                    <InputError class="mt-2" :message="form.errors.store_sub_title_two" />
                                </div>
                            </div>
                            <div class="d-flex justify-between my-5">
                                <div style="width: 20%">
                                    <InputLabel for="access" class="pt-3">
                                        {{$t('system.table.store_btm_text_two')}}
                                        <strong style="color: red;" v-if="!props.shop">* 必須</strong>
                                    </InputLabel>
                                </div>

                                <div style="width: 80%">
                                    <TextInput
                                        id="store_btm_text_two"
                                        type="text"
                                        density="compact"
                                        class="mt-1 block w-full"
                                        v-model="form.store_btm_text_two"
                                        required
                                        placeholder="入力してください"
                                        autofocus
                                    />

                                    <InputError class="mt-2" :message="form.errors.store_btm_text_two" />
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
                                            @click="formSubmit('release')"
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
