<script setup>
import TranscriptionConverter from "@/Components/TranscriptionConverter.vue";
import VSaveLoader from "@/Components/VSaveLoader.vue";
import VModal from "@/Components/VModal.vue";
import VInput from "@/Components/VInput.vue";
import { defineModel } from 'vue'

const { loader } = defineProps({
    loader: {
        type: Boolean,
        default: true,
    },
})

const vocabulaForm = defineModel('vocabulaForm')

const emits = defineEmits(['close', 'click-submit', 'click-reset', 'update:vocabula-form'])
</script>

<template>
  <VModal
    ref="vocabulaModal"
    header="Изменить вокабулу"
    button-class="btn btn-sm btn-warning"
    class="max-w-screen-sm"
    @close="emits('close')"
  >
    <slot />

    <template #postHeader>
      <button
        class="btn btn-sm btn-success"
        @click="emits('click-submit')"
      >
        Сохранить
      </button>
      <button
        class="btn btn-sm btn-warning"
        @click="emits('click-reset')"
      >
        Сбросить
      </button>
      <VSaveLoader :is-save="loader" />
    </template>

    <template #content>
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
            required
          />
          <TranscriptionConverter v-model="vocabulaForm.transcription" />
        </div>
      </div>
    </template>
  </VModal>

</template>

<style scoped>

</style>