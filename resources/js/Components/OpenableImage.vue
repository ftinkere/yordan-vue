<script setup>
import VModalN from "@/Components/VModalN.vue";

const { image } = defineProps({
    image: {
        type: String,
    },
    alt: {
        type: String,
        default: 'Изображение',
    },
    height: {
        type: String,
        default: '10em',
    }
})
</script>

<template>
  <VModalN
    v-if="image"
    class="max-w-screen-sm"
    header="Изображение вокабулы"
  >
    <template #opener="{ onClick }">
      <img
        class="mb-4 cursor-pointer"
        :src="image"
        :alt="alt"
        :style="{
          'max-height': height,
        }"
        @click="onClick"
      >
    </template>

    <template #content>
      <img
        class="mt-4"
        :src="image"
        :alt="alt"
        :style="{
          'max-height': '90%',
        }"
      >
    </template>
  </VModalN>
</template>

<style scoped>

</style>