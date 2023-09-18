<script setup>

defineProps({
    avatar: {
        type: String,
        default: null,
    },
    alt: {
        type: String,
        default: 'Аватар',
    },
    size: {
        type: String,
        default: '5em',
    }
})
</script>

<template>
  <img
    v-if="avatar"
    class="rounded-full"
    :alt="alt"
    :src="avatar"
    :style="{ width: size, height: size }"
  >
  <svg
    v-else
    class="fill-neutral-200 rounded-xl"
    viewBox="0 0 1024 1024"
    xmlns="http://www.w3.org/2000/svg"
    :style="{ width: size, height: size }"
  >
    <path d="M628.736 528.896A416 416 0 0 1 928 928H96a415.872 415.872 0 0 1 299.264-399.104L512 704l116.736-175.104zM720 304a208 208 0 1 1-416 0 208 208 0 0 1 416 0z" />
  </svg>
</template>

<style scoped>

</style>
