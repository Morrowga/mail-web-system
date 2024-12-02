<script setup>
import { router } from "@inertiajs/vue3";
import { defineComponent } from "vue";

defineComponent({
    name: "CustomPagination",
});

const hasQueryParams = (base) => {
  return base.includes('?');
};

const props = defineProps({
    total: {
        require: true,
        type: Number,
        default: () => 0,
    },
    currentPage: {
        type: Number,
        require: true,
        default: () => 0,
    },
    nextPageUrl: {
        type: String,
        require: true,
        default: () => "",
    },
    previousPageUrl: {
        type: String,
        default: () => "",
        require: true,
    },
    base: {
        require: true,
        default: () => "/",
        type: String,
    },
});

// Function to dynamically return 'https' protocol
const getProtocol = () => {
    return window.location.protocol === "https:" ? "https://" : "https://"; // Force HTTPS
};

// Ensure the base URL uses the correct protocol (https)
const adjustBaseUrl = (base) => {
    const protocol = getProtocol(); // Use HTTPS for all environments
    if (!base.startsWith("http://") && !base.startsWith("https://")) {
        return protocol + base; // Add HTTPS if base doesn't have a protocol
    }
    return base; // If base already has a protocol, return as is
};

const onNavigatePage = (to) => {
    console.log(window.location.protocol);
    const separator = hasQueryParams(props?.base) ? '&' : '?'; // Determine the correct separator
    const adjustedBaseUrl = adjustBaseUrl(props.base); // Adjust the base URL with HTTPS
    const url = `${adjustedBaseUrl}${separator}page=${to}`; // Construct the full URL
    router.get(url); // Navigate to the constructed URL
};
</script>

<template>
    <div class="flex justify-center mt-4">
        <v-pagination :total-visible="3" :model-value="currentPage" :length="total">
            <template v-slot:next="pages">
                <v-btn
                    variant="tonal"
                    flat
                    density="compact"
                    @click="router.get(nextPageUrl)"
                    :disabled="pages.disabled"
                    icon="mdi-chevron-right"
                />
            </template>
            <template v-slot:prev="pages">
                <v-btn
                    density="compact"
                    :disabled="pages.disabled"
                    icon="mdi-chevron-left"
                    flat
                    @click="router.get(previousPageUrl)"
                />
            </template>
            <template v-slot:item="pages">
                <v-btn @click="onNavigatePage(pages.page)" flat>{{
                    pages.page
                }}</v-btn>
            </template>
        </v-pagination>
    </div>
</template>
