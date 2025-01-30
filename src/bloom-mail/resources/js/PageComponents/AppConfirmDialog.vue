<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
  routeUrl: { type: String, required: true },
  item: { type: String, required: true },
});

const { t, locale } = useI18n();

const form = useForm({})

const isActive = ref(false);

function closeDialog() {
  isActive.value = false;
}

const submitDelete = () => {
    form.delete(props?.routeUrl + '/' + props?.item?.id,{
        onSuccess: () => {
            isActive.value = false;
        },
        onError: (error) => {
        },
    })
}
</script>


<template>
    <v-dialog v-model="isActive" max-width="500">
      <template v-slot:activator="{ props: activatorProps }">
        <VBtn
            color="red"
            style="border-radius: 6px;"
            v-bind="activatorProps"
        >
            {{ $t('table.delete') }}
        </VBtn>
      </template>

      <template v-slot:default>
        <v-card>
          <v-card-title>{{ $t('system.title.confirmation') }}</v-card-title>
          <v-card-text>
            <div>
              <p>
                {{ $t('system.title.canttake') }}
              </p>
            </div>
          </v-card-text>


        <v-card-text class="d-flex justify-end">
            <v-btn :text="$t('buttons.cancel')" color="#727272" @click="closeDialog"></v-btn>
            <v-btn :text="$t('buttons.confirm')" class="mx-2" color="#ff0007" @click="submitDelete"></v-btn>
        </v-card-text>
        </v-card>
      </template>
    </v-dialog>
  </template>
