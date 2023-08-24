<script setup>
import Layout from "@/Layouts/Layout.vue";
import LanguageTabs from "@/Layouts/partials/LanguageTabs.vue";

const { language } = defineProps({
    language: {
        type: Object,
        required: true,
    },
    can_edit: {
        type: Boolean,
        default: false,
    }
})
</script>

<template>
    <Layout>
        <LanguageTabs :language="language" :can_edit="can_edit" />
        <br>
        <slot />
    </Layout>
</template>

<style scoped>

</style>
