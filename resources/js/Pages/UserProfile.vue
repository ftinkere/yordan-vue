<script setup>
import UserInfo from "@/Components/UserInfo.vue";
import Layout from "@/Layouts/Layout.vue";
import UserLanguages from "@/Components/UserLanguages.vue";
import UserAvatar from "@/Components/UserAvatar.vue";
import { Link } from "@inertiajs/vue3";
import CardList from "@/Components/CardList.vue";
import route from "ziggy-js";

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
      <UserLanguages :languages="user.languages" />

      <div v-if="user.deletedLanguages.length > 0">
        Удалённые языки:

        <CardList>
          <span
            v-for="language in user.deletedLanguages"
            :key="language.id"
            class="w-full text-center text-lg"
          >
            {{ language.name }}
            <Link
              class="link link-info"
              :href="route('languages.restore', language.id)"
            >
              Восстановить
            </Link>
          </span>
        </CardList>
      </div>
    </div>
  </Layout>
</template>

<style scoped>

</style>
