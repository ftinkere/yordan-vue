<script setup>
import VModalN from "@/Components/VModalN.vue";
import { inject, ref } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import route from "ziggy-js";
import LexemeGrammatics from "@/Components/LexemeGrammatics.vue";

const { lexeme } = defineProps({
    lexeme: {
        type: Object,
        default: null,
    }
})
const _token = usePage().props.csrf_token
const language = inject('language')

const grammaticsModal = ref(null)
const flashSuccess = ref(null)

const emits = defineEmits([ 'close', 'success', 'request' ])

function newForm() {
    return {
        grammatics: lexeme?.grammatics?.map(g => `${g.grammatic_value_id}`),
        grammatics_variables: lexeme?.grammatics?.filter(g => g.is_variable === 1).map(g => `${g.grammatic_value_id}`),
        _token,
    }
}

const grammaticsForm = useForm(newForm())

function applyGrammatics() {
    if (lexeme) {
        grammaticsForm.transform(data => {
            if (data.grammatics) {
                data.grammatics = data.grammatics.map(g => parseInt(g))
            }
            if (data.grammatics_variables) {
                data.grammatics_variables = data.grammatics_variables.map(g => parseInt(g))
            }
            return data
        }).post(route('languages.vocabulary.lexemes.grammatics.update', {
            code: language,
            vocabula: lexeme.vocabula.id,
            lexeme: lexeme.id,
            _token,
        }), {
            preserveScroll: true,
            onSuccess: () => {
                flashSuccess.value?.flash()
                emits('success')
            }
        })
    }
}

function close() {
    grammaticsModal.value?.close()
    emits('close')
}

function open() {
    grammaticsModal.value?.open()
}

defineExpose({ close, open })

</script>

<template>
  <VModalN
    ref="grammaticsModal"
    header="Грамматика"
    class="max-w-screen-lg w-full"
    @close="applyGrammatics"
  >
    <template #postHeader>
      <button
        class="btn btn-sm btn-success"
        @click="applyGrammatics"
      >
        Применить
      </button>
    </template>

    <template #content>
      <LexemeGrammatics
        v-model="grammaticsForm.grammatics"
        v-model:variables="grammaticsForm.grammatics_variables"
      />
    </template>
  </VModalN>
</template>

<style scoped>

</style>