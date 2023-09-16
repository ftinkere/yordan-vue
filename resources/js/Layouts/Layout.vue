<script setup>
import { Head } from '@inertiajs/vue3';
import LayoutHeader from "@/Layouts/partials/LayoutHeader.vue";
import FlashMessage from "@/Layouts/partials/FlashMessage.vue";

defineProps({
    title: {
        type: String,
        default: '',
    },
    message: {
        type: String,
        default: null,
    },
})
</script>

<template>
  <Head>
    <title>{{ title }}</title>
  </Head>

  <LayoutHeader />
  <FlashMessage
    class="py-2"
    :message="message"
  />

  <div class="container max-w-screen-lg mx-auto m-4 px-6 mb-[50dvh]">
    <slot />
  </div>
</template>

<style scoped>

</style>
