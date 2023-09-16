<script setup>
import UserInfo from "@/Components/UserInfo.vue";
import Layout from "@/Layouts/Layout.vue";
import UserLanguages from "@/Components/UserLanguages.vue";
import UserAvatar from "@/Components/UserAvatar.vue";

const { user } = defineProps({
    user: {
        type: Object,
        required: true,
    },
})
</script>

<template>
  <Layout>
    <UserAvatar
      class="mx-auto"
      :avatar="user.avatar"
      size="10em"
    />
    <div class="mx-auto card max-w-md flex flex-col gap-2">
      <UserInfo :user="user" />
      <UserLanguages :user="user" />
    </div>
  </Layout>
</template>

<style scoped>

</style>
