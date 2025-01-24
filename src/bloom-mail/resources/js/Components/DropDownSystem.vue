<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    icon: {
        type: String,
        required: true
    },
    content: {
        type: Array,
        required: true
    }
});

const isOpen = ref(false);
const filteredContent = computed(() => props.content.filter(item => item.show));

const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
};
</script>

<template>
    <div class="dropdown-system" @click="toggleDropdown">
        <div class="nav-item" :class="{ 'active': isOpen }">
            <div class="nav-content">
                <div class="left-content">
                    <v-icon :icon="icon" class="nav-icon" />
                    <span class="nav-title">{{ title }}</span>
                </div>
                <v-icon
                    :icon="isOpen ? 'mdi-chevron-up' : 'mdi-chevron-down'"
                    class="dropdown-icon"
                />
            </div>
        </div>

        <div v-if="isOpen" class="dropdown-content">
            <Link
                v-for="item in filteredContent"
                :key="item.label"
                :href="route(item.href)"
                :method="item.post ? 'post' : 'get'"
                as="div"
                class="dropdown-item"
                v-show="item.show"
            >
                <v-icon :icon="icon" class="nav-icon mr-2" />
                {{ item.label }}
            </Link>
        </div>
    </div>
</template>

<style scoped>
.dropdown-system {
    width: 100%;
    cursor: pointer;
}

.nav-item {
    padding: 12px 16px;
    transition: background-color 0.2s;
}

.nav-item:hover {
    background-color: rgba(0, 0, 0, 0.04);
}

.nav-item.active {
    background-color: rgba(0, 0, 0, 0.08);
}

.nav-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.left-content {
    display: flex;
    align-items: center;
    gap: 12px;
}

.nav-icon {
    font-size: 20px;
    opacity: 0.9;
}

.nav-title {
    font-size: 14px;
    font-weight: 500;
}

.dropdown-icon {
    font-size: 20px;
    opacity: 0.7;
    transition: transform 0.2s;
}

.dropdown-content {
    background-color: #f8f9fa;
    border-radius: 0 0 4px 4px;
}

.dropdown-item {
    padding: 8px 16px 8px 48px;
    font-size: 14px;
    transition: background-color 0.2s;
}

.dropdown-item:hover {
    background-color: rgba(0, 0, 0, 0.04);
}
</style>
