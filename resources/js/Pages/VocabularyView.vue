<script setup>
    import LanguageLayout from "@/Layouts/LanguageLayout.vue";
    import LexemeShort from "@/Components/LexemeShort.vue";
    import LexemeArticle from "@/Components/LexemeArticle.vue";
    import EditButton from "@/Components/EditButton.vue";
    import { computed, ref } from "vue";
    import VSaveLoader from "@/Components/VSaveLoader.vue";
    import { useForm, usePage } from "@inertiajs/vue3";
    import VModal from "@/Components/VModal.vue";
    import VInputFile from "@/Components/VInputFile.vue";
    import route from "ziggy-js";
    import TranscriptionConverter from "@/Components/TranscriptionConverter.vue";
    import VInput from "@/Components/VInput.vue";
    import LexemeGrammatics from "@/Components/LexemeGrammatics.vue";
    import _ from "lodash";

    const { language, vocabula, editMode } = defineProps({
        language: {
          type: Object,
          required: true,
        },
        vocabula: {
            type: Object,
            required: true,
        },
        editMode: {
            type: Boolean,
            default: false,
        },
    })

    const _token = usePage().props.csrf_token

    const isEdit = ref(editMode)

    const vocabulaModal = ref(null)

    const vocabulaForm = useForm({
        vocabula: vocabula.vocabula,
        transcription: vocabula.transcription,
    })

    const applyVocabula = function () {
        vocabulaForm.post(route('languages.vocabulary.update', { code: language.id, vocabula: vocabula.id }), {
            onSuccess: () => { vocabulaModal.value?.close() }
        })
    }

    const deleteVocabula = function () {
        vocabulaForm.delete(route('languages.vocabulary.delete', { code: language.id, vocabula: vocabula.id }))
    }

    const imageModal = ref(null)

    const imageForm = useForm({
        image: null,
        _token,
    })

    const pushImage = function() {
        imageForm.post(route('languages.vocabulary.image', { code: language.id, vocabula: vocabula.id}), {
            onSuccess: () => { imageModal.value?.close() }
        })
    }

    const deleteImage = function() {
        imageForm.delete(route('languages.vocabulary.image', { code: language.id, vocabula: vocabula.id}), {
            onSuccess: () => { imageModal.value?.close() }
        })
    }

    const imagePreview = computed(() => {
        if (imageForm.image) {
            return URL.createObjectURL(imageForm.image)
        } else if (vocabula.image) {
            return vocabula.image
        }
        return null
    })

    const lexemeForms = (function () {
        let forms = {}

        vocabula.lexemes.forEach((lexeme) => {
            forms[lexeme.id] = useForm({
                group_number: lexeme.group_number,
                lexeme_number: lexeme.lexeme_number,
                grammatics: lexeme?.grammatics?.map(g => g.id),
                grammatics_variables: lexeme?.grammatics?.filter(g => g.is_variable === 1).map(g => g.id),
                short: lexeme.short,
                article: lexeme.article,
                _token,
            })
        })

        return forms
    })()

    const applyLexeme = function (lexeme) {
        // lexemeForms[lexeme.id].post(route('languages.vocabulary.lexeme.update', { code: language, lexeme: lexeme.id }))
    }

    console.log(vocabula);
</script>

<template>
  <LanguageLayout :language="language">
    <div class="card flex flex-col">
      <div class="flex flex-row justify-between items-center">
        <div class="flex flex-row flex-wrap gap-2 items-center">
          <h4 class="card-title m-2 font-bold text-3xl">
            <span>{{ vocabula.vocabula }}</span>
            <span class="font-normal text-md">/{{ vocabula.transcription }}/</span>
          </h4>

          <VModal
            v-if="isEdit"
            ref="vocabulaModal"
            header="Изменить вокабулу"
            button-class="btn btn-sm btn-warning"
            class="max-w-screen-sm"
            @close="vocabulaForm.reset()"
          >
            <span>Изменить вокабулу</span>

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
        </div>

        <div class="flex flex-col md:flex-row flex-wrap gap-2 items-center">
          <VSaveLoader :is-save="!vocabulaForm.isDirty" />
          <EditButton
            v-if="!isEdit"
            @click="isEdit = true"
          />
          <button
            v-if="isEdit"
            class="btn btn-sm btn-success"
            @click="applyVocabula"
          >
            Сохранить
          </button>
          <button
            v-if="isEdit"
            class="btn btn-sm btn-primary"
            @click="applyVocabula(); isEdit = false"
          >
            Посмотреть
          </button>
        </div>
      </div>

      <div class="flex flex-row flex-wrap gap-2">
        <VModal
          v-if="vocabula.image && !isEdit"
          :button-class="{'my-4 btn btn-sm btn-primary': !vocabula.image, '': vocabula.image}"
          class="max-w-screen-sm"
          header="Изображение вокабулы"
        >
          <img
            class="mb-4 max-h-[10em]"
            :src="vocabula.image"
            :alt="vocabula.vocabula"
          >

          <template #content>
            <img
              class="mt-4 max-h-[100em]"
              :src="vocabula.image"
              :alt="vocabula.vocabula"
            >
          </template>
        </VModal>
        <VModal
          v-else-if="isEdit"
          ref="imageModal"
          :button-class="{'my-4 btn btn-sm btn-primary': !vocabula.image, '': vocabula.image}"
          class="max-w-screen-sm"
        >
          <img
            v-if="vocabula.image"
            class="mb-4 max-h-[10em]"
            :src="vocabula.image"
            :alt="vocabula.vocabula"
          >
          <span
            v-else-if="isEdit && !vocabula.image"
          >
            Загрузить изображение
          </span>

          <template #content>
            <div class="flex flex-col items-center">
              <img
                v-if="imagePreview"
                class="h-[10em]"
                alt="Аватар"
                :src="imagePreview"
              >

              <VInputFile
                v-model="imageForm.image"
                label="Аватар"
                class="w-full"
                input-class="file-input file-input-bordered rounded-e-none"
                :errors="imageForm.errors.image"
              >
                <template #button>
                  <button
                    class="btn btn-success rounded-s-none"
                    type="submit"
                    @click="pushImage"
                  >
                    Отправить
                  </button>
                </template>
              </VInputFile>

              <button
                v-if="vocabula.image"
                class="mt-4 btn btn-sm btn-error self-start"
                @click="deleteImage"
              >
                Удалить
              </button>
            </div>
          </template>
        </VModal>
      </div>

      <div class="ms-6">
        <ul class="divide-y divide-neutral-700">
          <li class="mb-6 flex flex-col">
            <LexemeShort
              v-for="lexeme in vocabula.lexemes"
              :key="lexeme.id"
              :lexeme="lexeme"
              :one="vocabula.lexemes.filter(lx => lx.group_number === lexeme.group_number).length === 1"
            />
          </li>

          <template
            v-for="lexeme in vocabula.lexemes"
            :key="lexeme.id"
          >
            <li v-if="lexeme.short.length > 0 || lexeme.grammatics.length > 0">
              <article class="card">
                <div class="p-1">
                  <div class="innline md:flex md:flex-row md:justify-between">
                    <LexemeShort
                      :lexeme="lexeme"
                      :one="vocabula.lexemes.filter(lx => lx.group_number === lexeme.group_number).length === 1"
                    />

                    <VModal
                      v-if="isEdit"
                      header="Изменить лексему"
                      button-class="btn btn-sm btn-warning float-right"
                      class="max-w-[75dvw]"
                    >
                      <span v-if="lexeme.group_number === 0">Изменить общее</span>
                      <span v-else>Изменить лексему</span>

                      <template #postHeader>
                        <button
                          class="btn btn-sm btn-success"
                          @click="applyLexeme(lexeme)"
                        >
                          Сохранить
                        </button>
                        <button
                          class="btn btn-sm btn-warning"
                          @click="lexemeForms[lexeme.id].reset()"
                        >
                          Сбросить
                        </button>
                      </template>

                      <template #content>
                        <div class="flex flex-col gap-4">
                          <div class="flex flex-col">
                            <div
                              v-if="lexeme.group_number !== 0 || lexeme.lexeme_number !== 0"
                              class="grid grid-cols-2"
                            >
                              <VInput
                                v-model="lexemeForms[lexeme.id].group_number"
                                label="Номер группы"
                                type="number"
                                @change="_.debounce(() => applyLexeme(lexeme))"
                              />
                              <VInput
                                v-model="lexemeForms[lexeme.id].lexeme_number"
                                label="Номер лексемы"
                                type="number"
                                @change="_.debounce(() => applyLexeme(lexeme))"
                              />
                            </div>
                          </div>

                          <VModal
                            header="Грамматика"
                            button-class="btn btn-sm btn-primary w-fit"
                            class="w-[85dvw]"
                          >
                            Грамматика

                            <template #postHeader>
                              <button
                                class="btn btn-sm btn-success"
                              >
                                Применить
                              </button>
                            </template>

                            <template #content>
                              <LexemeGrammatics
                                v-model="lexemeForms[lexeme.id].grammatics"
                                v-model:variables="lexemeForms[lexeme.id].grammatics_variables"
                                :language="language"
                              />
                            </template>
                          </VModal>
                        </div>
                      </template>
                    </VModal>
                  </div>

                  <LexemeArticle
                    :lexeme="lexeme"
                    without-short
                  />
                </div>
              </article>
            </li>
          </template>
        </ul>
      </div>

      <button
        v-if="isEdit"
        class="mt-4 btn btn-sm btn-error self-start"
        @click="deleteVocabula"
      >
        Удалить
      </button>
    </div>
  </LanguageLayout>
</template>

<style scoped>

</style>