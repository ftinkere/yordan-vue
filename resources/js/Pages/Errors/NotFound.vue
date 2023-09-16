<script setup>
import Layout from "@/Layouts/Layout.vue";

</script>

<template>
  <Layout>
    <div class="flex flex-col gap-2">
      <span class="font-bold text-xl">Not found</span>
      <button
        class="w-fit btn btn-sm btn-secondary"
        onclick="history.back()"
      >
        Назад
      </button>
    </div>
  </Layout>
</template>

<style scoped>

</style>
