<script setup>
import LayoutHeaderLeft from "@/Layouts/partials/LayoutHeaderLeft.vue";
import LayoutHeaderRight from "@/Layouts/partials/LayoutHeaderRight.vue";

</script>

<template>
  <header class="container-fluid sm:px-6x text-lg bg-neutral-800">
    <nav class="mx-auto container max-w-screen-lg navbar flex flex-row justify-between">
      <LayoutHeaderLeft />
      <LayoutHeaderRight />
    </nav>
  </header>
</template>

<style scoped>

</style>
