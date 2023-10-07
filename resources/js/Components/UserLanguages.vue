<script setup>
import CardLinkList from "@/Components/CardLinkList.vue";
import route from "ziggy-js";

const props = defineProps({
    languages: {
        type: Object,
        required: true,
    },
});

const languages = props.languages.map((language) => {
    return { ...language, href: route('languages.view', { code: language.id }) };
});
</script>

<template>
  <CardLinkList :list="languages" />
</template>

<style scoped>

</style>
