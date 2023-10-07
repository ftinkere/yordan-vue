<script setup>
const { action } = defineProps({
    action: {
        type: Object,
        required: true,
    }
})

const emits = defineEmits(['click'])
</script>

<template>
  <div class="alert flex flex-row flex-wrap items-center justify-between">
    <span class="inline-flex gap-1">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-info shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      {{ action.message }}
    </span>

    <div>
      <button
        class="btn btn-sm btn-primary"
        @click="emits('click')"
      >
        {{ action.button ?? 'Добавить' }}
      </button>
    </div>
  </div>
</template>

<style scoped>

</style>
