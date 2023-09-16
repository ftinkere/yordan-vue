<script setup>
import UserAvatar from "@/Components/UserAvatar.vue";
import route from "ziggy-js";

const { user } = defineProps({
    user: Object,
})
</script>

<template>
  <a
    :href="route('profile', { user: user.id })"
    class="ps-4 flex flex-row items-center gap-2 rounded-full hover:bg-neutral-800"
  >
    <span>{{ user.name }}</span>
    <UserAvatar
      size="2em"
      :avatar="user.avatar"
      :alt="user.name"
    />
  </a>
</template>

<style scoped>

</style>
