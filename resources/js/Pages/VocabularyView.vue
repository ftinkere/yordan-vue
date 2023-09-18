<script setup>
    const { vocabula } = defineProps({
        language: {
          type: Object,
          required: true,
        },
        vocabula: {
            type: Object,
            required: true,
        }
    })

    console.log(vocabula);
</script>

<template>
  {{ vocabula }}
</template>

<style scoped>

</style>