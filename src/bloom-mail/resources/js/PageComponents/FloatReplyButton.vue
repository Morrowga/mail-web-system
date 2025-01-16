<script setup>
import { computed, ref } from 'vue';
const props = defineProps({
    label: String,
    floatMails: Array
});

const emit = defineEmits(['update:onOpenDialog', 'update:hideFloat', 'changeMailDetail', 'removeMail', 'cancelMailStatus']);
const hover = ref(false);  // Track hover state

const handleClick = (item) => {
    emit('update:onOpenDialog', true);
    emit('changeMailDetail', item)
};

// const items = [
//     { title: 'Click Me' },
//     { title: 'Click Me' },
//     { title: 'Click Me' },
//     { title: 'Click Me 2' },
// ]

const handleRemove = () => {
    emit('update:hideFloat', false);

    props.floatMails.forEach(mail => {
        emit('cancelMailStatus', mail.id);
    });
};

const handleRemoveItem = (item) => {
    emit('removeMail', item);
    emit('cancelMailStatus', item.id);
};

const truncatedLabel = computed(() => {
  return props.label && props.label.length > 20 ? props.label.slice(0, 20) + '...' : props.label;
});

const truncatedSubject = (label) => {
    return label && label.length > 20 ? label.slice(0, 20) + '...' : label;
}
</script>


<template>
    <div>
        <!-- Floating Button with Dropdown -->
        <div class="d-flex justify-space-around">
            <v-menu>
            <template v-slot:activator="{ props }">
                <v-chip
                class="floating-button"
                v-bind="props"
                >
                    Recently Replies
                    <VIcon icon="mdi-close-box" @click="handleRemove" class="ml-5"></VIcon>
                </v-chip>
            </template>
            <v-list>
                <v-list-item
                v-for="(item, index) in props.floatMails"
                :key="index"
                :value="index"
                @click="handleClick(item)"
                >
                 <v-list-item-title>
                    <span class="p-5">
                        {{ item.sender }} {{ '(' + truncatedSubject(item.subject) + ')' }}
                    </span>
                    <VIcon icon="mdi-close-circle" style="color:red;" @click="handleRemoveItem(item)"></VIcon>
                </v-list-item-title>
                </v-list-item>
            </v-list>
            </v-menu>
        </div>
    </div>
</template>


<style scoped>
.floating-button {
    position: fixed;
    bottom: 0;
    right: 15%;
    width: auto;
    height: 50px;
    font-weight: bold !important;
    border-radius: 0;
    background-color: #e8e8e8;
    color: rgb(0,0,0,0.5);
    border-top: 2px solid rgba(0, 0, 0, 0.5);
    border-left: 2px solid rgba(0, 0, 0, 0.5);
    border-right: 2px solid rgba(0, 0, 0, 0.5);
    z-index: 1000;
}
</style>

