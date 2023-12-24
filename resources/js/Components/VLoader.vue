<script setup>
  const { loading, size } = defineProps({
      loading: Boolean,
      size: {
          type: String,
          default: '1em',
      },
  })
</script>

<template>
  <span
    v-if="loading"
    class="loading loading-spinner"
    :style="{ height: size }"
  >
  </span>
</template>

<style scoped>

</style>