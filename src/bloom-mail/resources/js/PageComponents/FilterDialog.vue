<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';

const filterProps = defineProps({
    filterDialog: Boolean,
    form: Object
});

const page = usePage();

const { t, locale } = useI18n();

const person_in_charges = ref(page?.props?.person_in_charges)

const dialog = ref(filterProps.filterDialog);

const emit = defineEmits(['update:filterDialog']);

watch(() => filterProps.filterDialog, (newVal) => {
    dialog.value = newVal;
});

const statusList = ref([
    {
        "name": "Replying",
        "value": "replying"
    },
    {
        "name": "Read",
        "value": "read"
    },
    {
        "name": "New",
        "value": "new"
    },
    {
        "name": "Confirmed",
        "value": "confirmed"
    },
    {
        "name": "Resolved",
        "value": "resolved"
    },
])

const onClose = () => {
    dialog.value = false;
    emit('update:filterDialog', false);
}

const onSearch = () => {
    emit('handleSubmit', filterProps.form)
    onClose()
}

</script>
<template>
    <VDialog v-model="dialog" max-width="700" persistent>
        <VForm>
            <VCard>
                <VCardTitle class="d-flex justify-end align-center">
                    <div class="icon-border text-center">
                        <VIcon
                            icon="mdi-close"
                            class="close-icon"
                            style="color: #a5a5a5; font-weight: bold;"
                            @click="onClose"
                        ></VIcon>
                    </div>
                </VCardTitle>

                <VCardText>
                    <VForm>
                        <VRow>
                            <VCol cols="12">
                                <div class="d-flex justify-start">
                                    <div style="width: 30%; padding: 10px;">
                                        <InputLabel for="Status" :value="$t('input.status')"/>
                                    </div>
                                    <div style="width: 40%;">
                                        <VSelect
                                            v-model="form.status"
                                            variant="outlined" density="compact" required hide-details
                                            :items="statusList"
                                            item-value="value"
                                            item-title="name"
                                        ></VSelect>
                                        <InputError class="my-2" :message="''" />
                                    </div>
                                </div>
                            </VCol>
                            <VCol cols="12">
                                <div class="d-flex justify-start">
                                    <div style="width: 30%; padding: 10px;">
                                        <InputLabel for="Date" :value="$t('input.date')"/>
                                    </div>
                                    <div style="width: 70%;" class="d-flex justify-start">
                                        <VTextField
                                            density="compact"
                                            variant="outlined"
                                            id="from"
                                            type="date"
                                            class="block w-full"
                                            v-model="form.from"
                                            required
                                        ></VTextField>
                                        <div class="mx-3">
                                            <p style="font-size: 30px;">~</p>
                                        </div>
                                        <VTextField
                                            density="compact"
                                            variant="outlined"
                                            id="to"
                                            type="date"
                                            class="block w-full"
                                            v-model="form.to"
                                            required
                                        ></VTextField>
                                        <InputError class="my-2" :message="''" />
                                    </div>
                                </div>
                            </VCol>
                            <VCol cols="12">
                                <div class="d-flex justify-start">
                                    <div style="width: 30%; padding: 10px;">
                                        <InputLabel for="Person In Charge" :value="$t('input.person_in_charge')"/>
                                    </div>
                                    <div style="width: 40%;">
                                        <VSelect
                                            v-model="form.person_in_charge"
                                            :placeholder="'Select Person In Charge'"
                                            variant="outlined" density="compact" required hide-details
                                            :items="person_in_charges"
                                            item-value="name"
                                            clearable
                                            item-title="name"
                                        ></VSelect>
                                        <InputError class="my-2" :message="''" />
                                    </div>
                                </div>
                            </VCol>
                            <VCol cols="12">
                                <div class="d-flex justify-start">
                                    <div style="width: 30%; padding: 10px;">
                                        <InputLabel for="Keyword" :value="$t('input.keyword')"/>
                                    </div>
                                    <div style="width: 70%;">
                                        <VTextField
                                            density="compact"
                                            variant="outlined"
                                            id="keyword"
                                            type="text"
                                            class="block w-full"
                                            v-model="form.keyword"
                                            required
                                        ></VTextField>
                                        <InputError class="my-2" :message="''" />
                                    </div>
                                </div>
                            </VCol>
                            <VCol cols="12">
                                <div class="text-right">
                                    <VBtn color="primary" prepend-icon="mdi-magnify" @click="onSearch">{{ $t('buttons.search') }}</VBtn>
                                </div>
                            </VCol>
                        </VRow>
                    </VForm>
                </VCardText>

                </VCard>
        </VForm>
    </VDialog>
  </template>

<style scoped>
.close-icon,
.minimize-icon
{
  cursor: pointer;
  color: #757575;
  transition: color 0.3s ease;
}

.close-icon:hover,
.minimize-icon:hover
{
  color: #000;
}

.icon-border
{
    border: 2px solid #a5a5a5;
    margin: 5px;
    font-size: 13px;
}

.spamtrashbtn
{
    font-size: 10px;
    width: 35%;
    height: 3vh;
    box-shadow: none;
    border: 2px solid #a5a5a5;
}
</style>
