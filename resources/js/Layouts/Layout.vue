<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import LayoutHeader from "@/Layouts/partials/LayoutHeader.vue";
import FlashMessage from "@/Layouts/partials/FlashMessage.vue";
import { computed } from "vue";

defineProps({
    title: {
        type: String,
        default: ''
    }
});

const message = computed(function () {
    return usePage().props.message;
});


</script>

<template>
    <Head>
        <title>{{ title }}</title>
    </Head>

    <LayoutHeader />
    <FlashMessage class="py-2" :message="message" />

    <div class="containe max-w-screen-lg mx-auto m-4 px-6">
        <slot />
    </div>
</template>

<style scoped>

</style>
