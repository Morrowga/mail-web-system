<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Welcome" />
    <div class="container">
        <img src="/images/bloomlogo.png" alt="Logo" />
    </div>
</template>

<style scoped>
.container {
    display: flex;
    justify-content: center; /* Horizontally center */
    align-items: center; /* Vertically center */
    height: 100vh; /* Take full viewport height */
    text-align: center; /* Center image text (if any) */
}
</style>
