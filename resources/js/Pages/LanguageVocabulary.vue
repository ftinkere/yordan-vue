<script setup>
import LanguageLayout from "@/Layouts/LanguageLayout.vue";

const { language } = defineProps({
    language: Object,
});

console.log(language);
</script>

<template>
    <LanguageLayout :language="language">
        Vocabulary
    </LanguageLayout>
</template>

<style scoped>

</style>
