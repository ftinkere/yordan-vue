<script setup>
/* global route */

import UserAvatar from "@/Components/UserAvatar.vue";

const { user } = defineProps({
    user: Object,
})
</script>

<template>
    <a :href="route('profile', { user: user.id })" class="ps-4 flex flex-row items-center gap-2 rounded-full hover:bg-neutral-800">
        <span>{{ user.name }}</span>
        <UserAvatar  class="w-[2rem]" :avatar="user.avatar" :alt="user.name" />
    </a>
</template>

<style scoped>

</style>
