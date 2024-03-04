<script setup>
import PencilIcon from "@/Components/icons/PencilIcon.vue";

</script>

<template>
  <button class="btn btn-warning btn-sm">
    <slot>
      <PencilIcon />
    </slot>
  </button>
</template>

<style scoped>

</style>
