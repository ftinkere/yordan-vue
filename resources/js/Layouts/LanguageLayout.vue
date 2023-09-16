<script setup>
import Layout from "@/Layouts/Layout.vue";
import LanguageTabs from "@/Layouts/partials/LanguageTabs.vue";
import { provide } from "vue";

const { language } = defineProps({
    language: {
        type: Object,
        required: true,
    },
});

provide('language', language);
</script>

<template>
  <Layout>
    <LanguageTabs :language="language" />
    <br>
    <slot />
  </Layout>
</template>

<style scoped>

</style>
