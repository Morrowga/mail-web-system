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
        <span
            class="text-red font-bold cursor-pointer"
            v-bind="activatorProps"
        >
            {{ $t('table.delete') }}
        </span>
      </template>

      <template v-slot:default>
        <v-card>
          <v-card-title>{{ $t('other.confirmation') }}</v-card-title>
          <v-card-text>
            <div>
              <p>
                {{ $t('other.delete_text') }}
              </p>
            </div>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>

            <v-btn :text="$t('buttons.cancel')" color="green" @click="closeDialog"></v-btn>
            <v-btn :text="$t('buttons.confirm')" color="red" @click="submitDelete"></v-btn>
          </v-card-actions>
        </v-card>
      </template>
    </v-dialog>
  </template>
