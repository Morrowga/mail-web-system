<script setup>
import { ref, computed } from 'vue';
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

const visiblePages = computed(() => {
    const total = props.total;
    console.log(total);
    const current = props.currentPage;
    const pages = [];

    if (total <= 7) {
        // If total pages are 7 or less, show all pages
        for (let i = 1; i <= total; i++) {
            pages.push(i);
        }
    } else {
        // Always show first page
        pages.push(1);

        if (current <= 3) {
            // If current page is near the start
            pages.push(2, 3, 4);
            pages.push('...');
            pages.push(total);
        } else if (current >= total - 2) {
            // If current page is near the end
            pages.push('...');
            pages.push(total - 3, total - 2, total - 1, total);
        } else {
            // If current page is in the middle
            pages.push('...');
            pages.push(current - 1, current, current + 1);
            pages.push('...');
            pages.push(total);
        }
    }

    console.log(total);

    return pages;
});

const onNavigatePage = (to) => {
    if (to === '...') {
        // If clicking on left ellipsis
        if (props.currentPage > 3) {
            const newPage = Math.max(1, props.currentPage - 3);
            router.get(`${props.base}?page=${newPage}`);
        }
        // If clicking on right ellipsis
        else {
            const newPage = Math.min(props.total, props.currentPage + 3);
            router.get(`${props.base}?page=${newPage}`);
        }
        return;
    }

    router.get(`${props.base}?page=${to}`);
};
</script>

<template>
    <div class="flex justify-center mt-4">
        <!-- Previous Button -->
        <v-btn
            density="compact"
            icon="mdi-chevron-left"
            flat
            @click="router.get(previousPageUrl)"
            :disabled="currentPage === 1"
            class="pagination-btn"
        />

        <!-- Page Numbers -->
        <template v-for="page in visiblePages" :key="page">
            <v-btn
                class="pagination-btn"
                @click="onNavigatePage(page)"
                :active="page === currentPage"
                flat
            >
                {{ page }}
            </v-btn>
        </template>

        <!-- Next Button -->
        <v-btn
            density="compact"
            icon="mdi-chevron-right"
            flat
            @click="router.get(nextPageUrl)"
            :disabled="currentPage === total"
            class="pagination-btn"
        />
    </div>
</template>

<style scoped>
.pagination-btn {
    min-width: 40px;
    height: 40px;
    margin: 0 2px;
    padding: 0 5px;
    border-radius: 0 !important;
    border: 0.5px solid #e0e0e0;
    text-align: center;
    line-height: 30px;
    transition: background-color 0.3s ease;
}

.pagination-btn:hover {
    background-color: #f4f4f4;
}

.v-btn--active {
    background-color: #41b5d3 !important;
    color: #fff !important;
}

.flex {
    display: flex;
    align-items: center;
}
</style>

