<script setup>
import { computed, ref } from 'vue';
const props = defineProps({
    label: String,
    newFloatMails: Array
});

const emit = defineEmits(['update:onOpenDialog', 'update:hideFloat', 'removeNewMail', 'changeNewMailValue']);

const handleRemove = () => {
    props.newFloatMails.forEach(mail => {
        emit('removeNewMail', mail.id);
    });

    emit('update:hideFloat', false);
};

const handleRemoveItem = (id) => {
    emit('removeNewMail', id);
};

const changeValue = (id) => {
    emit('changeNewMailValue', id)
    emit('update:onOpenDialog', true);
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
                    Mail Creation
                    <VIcon icon="mdi-close-box" @click="handleRemove" class="ml-5"></VIcon>
                </v-chip>
            </template>
            <v-list>
                <v-list-item
                v-for="(item, index) in props.newFloatMails"
                :key="index"
                :value="index"
                @click="changeValue(item.id)"
                >
                 <v-list-item-title>
                    <span class="p-5">
                        Mail Creation {{ item.subject == '' ?  index : item.subject }}
                    </span>
                    <VIcon icon="mdi-close-circle" style="color:red;" @click="handleRemoveItem(item.id)"></VIcon>
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

