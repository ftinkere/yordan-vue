<script setup>
import LanguageLayout from "@/Layouts/LanguageLayout.vue";

const { language, editMode } = defineProps({
    language: {
        type: Object,
        required: true,
    },
    editMode: {
        type: Boolean,
        default: false,
    }
})


</script>

<template>
  <LanguageLayout :language="language">

  </LanguageLayout>
</template>

<style scoped>

</style>