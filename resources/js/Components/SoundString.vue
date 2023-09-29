<script setup>
    defineProps({
        sound: {
            type: Object,
            required: true,
        }
    })
</script>

<template>
  <span>{{ sound.table }} {{ sound.sub_column }} {{ sound.column }} {{ sound.row }}: {{ sound.sound }}</span>
</template>

<style scoped>

</style>