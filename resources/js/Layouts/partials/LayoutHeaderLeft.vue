<script setup>
import VModal from "@/Components/VModal.vue";
import { Link } from "@inertiajs/vue3";
import route from "ziggy-js";

const links = [
    { label: "Все языки", href: route('languages') },
    { label: "Справка", href: route('info') },
    { label: "О нас", href: route('about') },
];
</script>

<template>
  <div class="grow flex flex-row gap-4 items-baseline">
    <!--  Сайдбар на маленьких экранах  -->
    <VModal
      without-x
      button-class="sm:hidden"
      class="absolute left-0 top-[4rem] rounded-t-none max-h-full h-[100dvh] w-2/3 flex flex-col gap-1"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        class="inline-block w-5 h-5 stroke-current"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M4 6h16M4 12h16M4 18h16"
        />
      </svg>

      <template #content>
        <Link
          v-for="{ href, label } in links"
          :key="label"
          class="text-xl"
          :href="href"
        >
          {{ label }}
        </Link>
      </template>
    </VModal>

    <Link
      class="text-2xl"
      :href="route('main')"
    >
      Ёрдан
    </Link>
    <div class="flex flex-row space-x-4 max-sm:hidden">
      <Link
        v-for="{ href, label } in links"
        :key="label"
        :href="href"
      >
        {{ label }}
      </Link>
    </div>
  </div>
</template>

<style scoped>

</style>
