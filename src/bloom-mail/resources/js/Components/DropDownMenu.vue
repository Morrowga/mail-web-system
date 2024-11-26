<script setup>
import { defineProps } from 'vue';
import Dropdown from './Dropdown.vue';
import DropdownLink from './DropdownLink.vue';
import { router } from '@inertiajs/vue3';

// Props
defineProps({
    title: {
        type: String,
        required: true, // The button title (e.g., "User Name")
    },
    content: {
        type: Array,
        default: () => [], // Main dropdown items (array of objects with `label` and `href`)
    },
});

const getRouteName = (item) => {
    if(item.post)
    {
        router.post(route(item.href))
        return;
    }

    router.get(route(item.href))
}
</script>

<template>
    <div class="relative ms-3">
        <Dropdown align="right" width="48">
            <template #trigger>
                <span class="inline-flex rounded-md">
                    <button
                        type="button"
                        class="inline-flex items-center rounded-md pt-2 px-3 text-sm font-medium leading-4 layout-nav-text transition duration-150 ease-in-out
                        text-capitalize"
                    >
                        {{ title }}

                        <svg
                            class="-me-0.5 ms-2 h-4 w-4"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </button>
                </span>
            </template>

            <template #content>
                <div
                v-for="(item, index) in content" :key="index"
                >
                    <div>

                    </div>
                    <DropdownLink
                        v-if="item.show"
                        @click="getRouteName(item)"
                        :as="'button'"
                        >
                            {{ item.label }}
                    </DropdownLink>
                </div>
            </template>
        </Dropdown>
    </div>
</template>

<style scoped>
.dropdown-item {
    padding: 0.5rem;
}
.dropdown-link {
    text-decoration: none;
    color: inherit;
    padding: 0.5rem 1rem;
    display: block;
    transition: background-color 0.2s ease-in-out;
}
.dropdown-link:hover {
    background-color: #f1f5f9;
}
.dropdown-divider {
    border-top: 1px solid #e5e7eb;
    margin: 0.5rem 0;
}
</style>
