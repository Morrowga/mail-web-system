<template>
    <v-row dense v-for="(row, rowIndex) in groupedPermissions" :key="rowIndex">
        <v-col
            v-for="permission in row"
            :key="permission"
            cols="3"
            class="d-flex align-center"
        >
            <v-checkbox
            color="primary"
            :value="permission?.id"
            v-model="localSelectedPermissions"
            :label="permission?.display"
            @change="updateParent"
            />
        </v-col>
    </v-row>
  </template>

  <script setup>
  import { computed, ref, watch } from "vue";

  const props = defineProps({
    modelValue: {
      type: Array,
      required: true,
    },
    permissions: {
      type: Array,
      required: true,
    },
  });

  const emit = defineEmits(["update:modelValue"]);

  console.log(props?.permissions)

  const localSelectedPermissions = ref([...props.modelValue]);

  const sortedPermissions = computed(() =>
  [...props.permissions]
    .filter((permission) => typeof permission.name === "string")
    .sort((a, b) => {
      const prefixA = a.name.split("_")[0]; // Extract prefix from 'name'
      const prefixB = b.name.split("_")[0]; // Extract prefix from 'name'
      return prefixA.localeCompare(prefixB); // Compare prefixes
    })
);

const groupedPermissions = computed(() => {
  const groups = [];
  for (let i = 0; i < sortedPermissions.value.length; i += 4) {
    groups.push(sortedPermissions.value.slice(i, i + 4));
  }
  return groups;
});

  const updateParent = () => {
    emit("update:modelValue", localSelectedPermissions.value);
  };

  watch(
    () => props.modelValue,
    (newValue) => {
      localSelectedPermissions.value = [...newValue];
    }
  );
  </script>
