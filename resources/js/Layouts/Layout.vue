<script setup>
import { Head } from '@inertiajs/vue3';
import LayoutHeader from "@/Layouts/partials/LayoutHeader.vue";

defineProps({
    title: {
        type: String,
        default: ''
    }
})
</script>

<template>
    <Head>
        <title>{{ title }}</title>
    </Head>

    <LayoutHeader />

    <div class="container mx-auto px-6">
        <slot></slot>
    </div>
</template>

<style scoped>

</style>
