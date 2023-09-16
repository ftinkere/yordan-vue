<script setup>
import CardLinkList from "@/Components/CardLinkList.vue";
import route from "ziggy-js";

const { user } = defineProps({
    user: {
        type: Object,
        required: true,
    },
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
