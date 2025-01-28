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

const onNavigatePage = (to) => {
    // router.get(`${props.base}?page=${to}`);
    const separator = hasQueryParams(props?.base) ? '&' : '?'; // Determine the correct separator
    const url = `${props.base}${separator}page=${to}`; // Construct the full URL
    router.get(url); // Navigate to the constructed URL

};
</script>

<template>
   <div class="flex justify-center mt-4">
        <v-pagination
            :total-visible="3"
            :model-value="currentPage"
            :length="total"
            class="pagination-compact"
        >
            <!-- Previous Button -->
            <template v-slot:prev="pages">
                <v-btn
                    density="compact"
                    icon="mdi-chevron-left"
                    flat
                    @click="router.get(previousPageUrl)"
                    :disabled="pages.disabled"
                    class="pagination-btn"
                />
            </template>

            <!-- Page Numbers -->
            <template v-slot:item="pages">
                <v-btn
                    class="pagination-btn"
                    @click="onNavigatePage(pages.page)"
                    :active="pages.isActive"
                    flat
                >
                    {{ pages.page }}
                </v-btn>
            </template>

            <!-- Next Button -->
            <template v-slot:next="pages">
                <v-btn
                    density="compact"
                    icon="mdi-chevron-right"
                    flat
                    @click="router.get(nextPageUrl)"
                    :disabled="pages.disabled"
                    class="pagination-btn"
                />
            </template>
        </v-pagination>
    </div>
</template>

<style scoped>
.pagination-compact {
    display: flex;
    align-items: center;
    gap: 2px; /* Minimal gap between items */
    margin: 0;
    padding: 0;
}

.pagination-btn {
    min-width: 40px;
    height: 40px;
    margin: 0;
    padding: 0 5px;
    text-align: center;
    line-height: 30px;
    border-radius: 4px;
    transition: background-color 0.3s ease, color 0.3s ease;
}

.pagination-btn:hover {
    background-color: #f4f4f4; /* Optional hover effect */
}

/* Active Page Styling */
.pagination-btn[aria-current="page"] {
    background-color: #E66B1C !important; /* Active background color */
    color: #fff !important; /* Active text color */
    font-weight: bold; /* Optional: Bold text for active page */
    pointer-events: none; /* Disable clicks on the active button */
}
</style>

