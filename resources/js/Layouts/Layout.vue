<script setup>
import { Head } from '@inertiajs/vue3';
import LayoutHeader from "@/Layouts/partials/LayoutHeader.vue";
import FlashMessge from "@/Layouts/partials/FlashMessge.vue";

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
    <FlashMessge class="py-2" />

    <div class="container mx-auto m-4 px-6">
        <slot></slot>
    </div>
</template>

<style scoped>

</style>
