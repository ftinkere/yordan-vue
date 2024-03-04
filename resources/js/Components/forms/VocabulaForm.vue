<script setup>
import TranscriptionConverter from "@/Components/TranscriptionConverter.vue";
import VInput from "@/Components/VInput.vue";
import { defineModel } from 'vue';

const vocabulaForm = defineModel()

</script>

<template>
  <div class="flex flex-col">
    <VInput
      v-model="vocabulaForm.vocabula"
      label="Вокабула"
      class="w-full"
      :errors="vocabulaForm.errors.vocabula"
      required
    />
    <div class="w-full">
      <VInput
        v-model="vocabulaForm.transcription"
        label="Транскрипция"
        :errors="vocabulaForm.errors.transcription"
      />
      <TranscriptionConverter v-model="vocabulaForm.transcription" />
    </div>
  </div>
</template>

<style scoped>

</style>