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

const getProtocol = () => {
    if (window.location.protocol === "https:") {
        return "https://";
    } else {
        return "http://";
    }
};

const adjustBaseUrl = (base) => {
    const protocol = getProtocol();

    if (!base.startsWith("http://") && !base.startsWith("https://")) {
        return protocol + base;
    }

    return base;
};

const onNavigatePage = (to) => {
    const separator = hasQueryParams(props?.base) ? '&' : '?';
    const adjustedBaseUrl = adjustBaseUrl(props.base);
    const url = `${adjustedBaseUrl}${separator}page=${to}`;
    router.get(url);
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
