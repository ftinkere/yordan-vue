<script setup>


import CardLinkList from "@/Components/CardLinkList.vue";

const { user } = defineProps({
    user: Object,
})
</script>

<template>
    <CardLinkList :list="user.languages" />
</template>

<style scoped>

</style>
