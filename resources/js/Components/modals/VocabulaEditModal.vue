<script setup>
import { inject, ref } from 'vue'
import VModalN from "@/Components/VModalN.vue";
import { useForm } from "@inertiajs/vue3";
import route from "ziggy-js";
import VLoader from "@/Components/VLoader.vue";
import VFlashSuccess from "@/Components/VFlashSuccess.vue";
import VocabulaForm from "@/Components/forms/VocabulaForm.vue";

const { vocabula, show } = defineProps({
    vocabula: {
        type: Object,
        required: true,
    },
})

const language = inject('language')

const vocabulaModal = ref(null)
const flashSuccess = ref(null)

const vocabulaForm = useForm({
    vocabula: vocabula.vocabula,
    transcription: vocabula.transcription,
})

function applyVocabula() {
    vocabulaForm.post(route('languages.vocabulary.update', { code: language.id, vocabula: vocabula.id }), {
        onSuccess: () => {
            vocabulaForm.defaults()

            flashSuccess.value?.flash()
            vocabulaModal.value?.close()
        }
    })
}

function close() {
    vocabulaModal.value?.close()
}
function open() {
    vocabulaModal.value?.open()
}

defineExpose({ close, open })

const emits = defineEmits(['close'])
</script>

<template>
  <VModalN
    ref="vocabulaModal"
    header="Изменить вокабулу"
    class="max-w-screen-sm"
    @close="vocabulaForm.reset(); emits('close')"
  >
    <template #postHeader>
      <button
        class="btn btn-sm btn-success"
        @click="applyVocabula"
      >
        Сохранить
      </button>

      <button
        class="btn btn-sm btn-warning"
        @click="vocabulaForm.reset()"
      >
        Сбросить
      </button>

      <VLoader :loading="vocabulaForm.processing" />

      <VFlashSuccess ref="flashSuccess" />
    </template>

    <template #content>
      <VocabulaForm v-model="vocabulaForm" />
    </template>
  </VModalN>
</template>

<style scoped>

</style>