<script setup>
defineProps({
    value: {
        type: String,
    },
});
</script>

<template>
    <label class="block text-sm text-gray-700 font-bold">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
