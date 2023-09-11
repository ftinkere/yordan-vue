<script setup>
/* global route */

import CardLinkList from "@/Components/CardLinkList.vue";

const { user } = defineProps({
    user: Object,
});

const languages = user.languages.map((language) => {
    return { ...language, href: route('languages.view', { code: language.id }) };
});
</script>

<template>
    <CardLinkList :list="languages" />
</template>

<style scoped>

</style>
