<script setup>
import VModalN from "@/Components/VModalN.vue";
import { computed, inject, ref, watch } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import route from "ziggy-js";
import VMarkdownEditor from "@/Components/VMarkdownEditor.vue";
import VInput from "@/Components/VInput.vue";
import VFlashSuccess from "@/Components/VFlashSuccess.vue";
import LexemeGrammatics from "@/Components/LexemeGrammatics.vue";

const { lexeme, vocabula } = defineProps({
    lexeme: {
        type: Object,
        default: null,
    },
    vocabula: {
        type: Object,
        default: null,
    },
})
const _token = usePage().props.csrf_token
const language = inject('language')

const lexemeModal = ref(null)
const flashSuccess = ref(null)

const emits = defineEmits([ 'close', 'success', 'request' ])

function close() {
    lexemeModal.value?.close()
    emits('close')
}

function open() {
    lexemeModal.value?.open()
}

defineExpose({ close, open })

function newForm() {
    return {
        group_number: lexeme?.group_number,
        lexeme_number: lexeme?.lexeme_number,
        // grammatics: lexeme?.grammatics?.map(g => `${g.grammatic_value_id}`),
        // grammatics_variables: lexeme?.grammatics?.filter(g => g.is_variable === 1).map(g => `${g.grammatic_value_id}`),
        grammatics: [] ,
        grammatics_variables: [],
        short: lexeme?.short,
        article: lexeme?.article,
        style: lexeme?.style,
        _token,
    }
}

const lexemeForm = useForm(newForm())
const isStyleAdd = ref(!!lexeme?.style)

function applyLexeme() {
    if (lexeme) {
        lexemeForm.transform(data => {
            if (data.grammatics) {
                data.grammatics = data.grammatics.map(g => parseInt(g))
            }
            if (data.grammatics_variables) {
                data.grammatics_variables = data.grammatics_variables.map(g => parseInt(g))
            }
            if (!isStyleAdd.value) {
                data.style = null
            }
            return data
        }).post(route('languages.vocabulary.lexemes.update', {
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

function createLexeme() {
    if (!lexeme) {
        lexemeForm.transform(data => {
            if (data.grammatics) {
                data.grammatics = data.grammatics.map(g => parseInt(g))
            }
            if (data.grammatics_variables) {
                data.grammatics_variables = data.grammatics_variables.map(g => parseInt(g))
            }
            if (!isStyleAdd.value) {
                data.style = null
            }
            return data
        }).post(route('languages.vocabulary.lexemes.store', { code: language, vocabula: vocabula.id }), {
            preserveScroll: true,
            onSuccess: () => {
                flashSuccess.value?.flash()
                emits('success')
                close()
            }
        })
    }
}

const deleteLexeme = function() {
    if (lexeme) {
        lexemeForm.delete(route('languages.vocabulary.lexemes.delete', {
            code: language,
            vocabula: lexeme.vocabula.id,
            lexeme: lexeme.id,
        }), {
            preserveScroll: true,
            onSuccess: () => {
                emits('success')
                close()
            }
        })
    }
}

const grammaticModal = ref(null)

</script>

<template>
  <VModalN
    ref="lexemeModal"
    header="Изменить лексему"
    class="max-w-screen-lg w-full"
    @close="lexeme ? applyLexeme() : ''; emits('close')"
  >
    <template #postHeader>
      <button
        class="btn btn-sm btn-success"
        @click="lexeme ? applyLexeme() : createLexeme()"
      >
        <span v-if="lexeme">
          Сохранить
        </span>
        <span v-else>
          Создать
        </span>
      </button>

      <VFlashSuccess ref="flashSuccess1" />
    </template>

    <template #content>
      <div class="flex flex-col gap-4">
        <article
          v-if="!lexeme"
          class="alert block"
        >
          <p>
            Оставьте номер группы пустым, чтобы автоматически поставился следующий после последнего существующего.
          </p>
          <p>
            Оставьте номер лексемы пустым, чтобы автоматически поставился следующий после последнего существующего в группе.
          </p>
        </article>
        <div class="flex flex-col">
          <div
            v-if="lexeme?.group_number !== 0 || lexeme?.lexeme_number !== 0"
            class="grid grid-cols-2"
          >
            <VInput
              v-model="lexemeForm.group_number"
              label="Номер группы"
              type="number"
            />
            <VInput
              v-model="lexemeForm.lexeme_number"
              label="Номер лексемы"
              type="number"
            />
          </div>

          <VInput
            v-model="lexemeForm.short"
            label="Короткое значение"
            :errors="lexemeForm.errors.short"
            :required="lexeme?.group_number !== 0 || lexeme?.lexeme_number !== 0"
          />

          <div>
            <div v-if="!isStyleAdd">
              <button
                class="link link-info"
                @click="isStyleAdd = true"
              >
                Добавить стилистическую пометку
              </button>
            </div>
            <div v-else>
              <VInput
                v-model="lexemeForm.style"
                label="Стилистическая пометка"
                :errors="lexemeForm.errors.style"
              />
              <div class="flex flex-row justify-end">
                <button
                  class="link link-error"
                  @click="isStyleAdd = false"
                >
                  Убрать стилистическую пометку
                </button>
              </div>
            </div>
          </div>

          <VMarkdownEditor
            v-model="lexemeForm.article"
            label="Словарная статья"
          />
        </div>

        <div class="flex flex-row flex-wrap justify-between">
          <button
            v-if="lexeme && (lexeme?.group_number !== 0 || lexeme?.lexeme_number !== 0)"
            class="btn btn-sm btn-error w-fit"
            @click="deleteLexeme"
          >
            Удалить
          </button>

          <div v-if="!lexeme">
            <button
              class="btn btn-sm btn-primary"
              @click="grammaticModal?.open()"
            >
              Грамматика
            </button>
            <VModalN
              ref="grammaticModal"
              header="Грамматика"
              class="max-w-screen-lg w-full"
            >
              <template #postHeader>
                <button
                  class="btn btn-sm btn-success"
                  @click="grammaticModal?.close()"
                >
                  Применить
                </button>
              </template>

              <template #content>
                <LexemeGrammatics
                  v-model="lexemeForm.grammatics"
                  v-model:variables="lexemeForm.grammatics_variables"
                />
              </template>
            </VModalN>
          </div>

          <button
            v-if="!lexeme"
            class="btn btn-sm btn-success"
            @click="createLexeme"
          >
            Создать
          </button>
        </div>
      </div>
    </template>
  </VModalN>
</template>

<style scoped>

</style>