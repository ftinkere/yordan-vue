<script setup>
    import LanguageLayout from "@/Layouts/LanguageLayout.vue";
    import LexemeShort from "@/Components/LexemeShort.vue";
    import LexemeArticle from "@/Components/LexemeArticle.vue";
    import EditButton from "@/Components/EditButton.vue";
    import { computed, nextTick, reactive, ref, watch } from "vue";
    import VSaveLoader from "@/Components/VSaveLoader.vue";
    import { useForm, usePage } from "@inertiajs/vue3";
    import VModal from "@/Components/VModal.vue";
    import VInputFile from "@/Components/VInputFile.vue";
    import route from "ziggy-js";
    import TranscriptionConverter from "@/Components/TranscriptionConverter.vue";
    import VInput from "@/Components/VInput.vue";
    import LexemeGrammatics from "@/Components/LexemeGrammatics.vue";
    import _ from "lodash";
    import VMarkdownEditor from "@/Components/VMarkdownEditor.vue";
    import LexemeLink from "@/Components/LexemeLink.vue";
    import VInputLexeme from "@/Components/VInputLexeme.vue";
    import VCheckbox from "@/Components/VCheckbox.vue";
    import FlashMessage from "@/Layouts/partials/FlashMessage.vue";
    import VFlashSuccess from "@/Components/VFlashSuccess.vue";

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

    const update = ref(true)

    const vocabulaModal = ref(null)

    const vocabulaForm = useForm({
        vocabula: vocabula.vocabula,
        transcription: vocabula.transcription,
    })

    const applyVocabula = function () {
        vocabulaForm.post(route('languages.vocabulary.update', { code: language.id, vocabula: vocabula.id }), {
            onSuccess: () => {
                vocabulaModal.value?.close()
            }
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

    const updateLexemeForms = function (lexemes) {
        let forms = {}

        forms[0] = useForm({
            group_number: null,
            lexeme_number: null,
            grammatics: [],
            grammatics_variables: [],
            short: '',
            article: '',
            style: '',
            _token,
        })


        lexemes.forEach((lexeme) => {
            forms[lexeme.id] = useForm({
                group_number: lexeme.group_number,
                lexeme_number: lexeme.lexeme_number,
                grammatics: lexeme?.grammatics?.map(g => `${g.grammatic_value_id}`),
                grammatics_variables: lexeme?.grammatics?.filter(g => g.is_variable === 1).map(g => `${g.grammatic_value_id}`),
                short: lexeme.short,
                article: lexeme.article,
                style: lexeme.style,
                _token,
            })
        })

        return forms
    }

    let lexemeForms = updateLexemeForms(vocabula.lexemes)

    const isSave = computed(() => {
        return !vocabulaForm.isDirty && !_.some(lexemeForms, l => l.isDirty)
    })

    const createModal = ref(null)

    function styles() {
        let res = {}

        res[0] = false
        vocabula.lexemes.forEach(e => res[e.id] = !!e.style)
        return res
    }

    const isStyleAdd = ref(styles())

    const createLexeme = function () {
        lexemeForms[0]
            .transform(data => {
                if (data.grammatics) {
                    data.grammatics = data.grammatics.map(g => parseInt(g))
                }
                if (data.grammatics_variables) {
                    data.grammatics_variables = data.grammatics_variables.map(g => parseInt(g))
                }
                if (!isStyleAdd.value[0]) {
                    data.style = null
                }
                return data
            })
            .post(route('languages.vocabulary.lexemes.store', { code: language, vocabula: vocabula.id }), {
                onSuccess: (e) => {
                    createModal.value?.close()
                    lexemeForms = updateLexemeForms(e.props.vocabula.lexemes)
                },
            })
    }
    const applyLexeme = function (id) {
        lexemeForms[id]
            .transform(data => {
                if (data.grammatics) {
                    data.grammatics = data.grammatics.map(g => parseInt(g))
                }
                if (data.grammatics_variables) {
                    data.grammatics_variables = data.grammatics_variables.map(g => parseInt(g))
                }
                if (!isStyleAdd.value[id]) {
                    data.style = null
                }
                return data
            }).post(route('languages.vocabulary.lexemes.update', { code: language, vocabula: vocabula.id, lexeme: id }), {
                onSuccess: () => {
                    update.value = false
                    nextTick(() => {
                        update.value = true
        })
                }
        })
    }
    const deleteLexeme = function (id) {
        lexemeForms[id].delete(route('languages.vocabulary.lexemes.update', { code: language, vocabula: vocabula.id, lexeme: id }))
    }


    for (let id in lexemeForms) {
        const comp = computed(() => lexemeForms[id].data())
        if (id != 0) {
            watch(comp, _.debounce(() => applyLexeme(id), 3000))
        }
    }

    const linksModal = reactive([])

    const isLinkReverse = ref(false)
    const linkForm = useForm({
        type: '',
        to_lexeme_id: null,
        comment: '',
    })
    const reverseLinkForm = useForm({
        type: null,
        comment: null,
    })
    const successFlash = ref(null)

    const addLink = function (lexeme) {
        linkForm.transform(data => {
            if (isLinkReverse.value) {
                data.reverse = reverseLinkForm.data()
            }
            return data
        }).post(route('languages.vocabulary.lexemes.links.store', {
            code: language.id,
            vocabula: vocabula.id,
            lexeme: lexeme.id
        }), {
            onSuccess: () => {
                successFlash.value.forEach(e => e?.flash())
                linksModal.forEach(modal => {
                    try {
                        modal.close()
                    } catch (e) { /* empty */ }
                })
            },
        })
    }

    const deleteLink = function (lexeme, link) {
        linkForm.delete(route('languages.vocabulary.lexemes.links.delete', { code: language.id, vocabula: vocabula.id, lexeme: lexeme.id, link: link.id }))
    }

</script>

<template>
  <LanguageLayout :language="language">
    <div class="card flex flex-col">
      <div>
        <button
          class="btn btn-sm"
          onclick="history.back()"
        >
          Назад
        </button>
      </div>

      <div class="flex flex-row justify-between items-end md:items-center">
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
              <VSaveLoader :is-save="isSave" />
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

        <div class="flex flex-row flex-wrap-reverse gap-2 items-center justify-end">
          <VSaveLoader :is-save="isSave" />
          <EditButton
            v-if="language.can_edit && !isEdit"
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
              v-if="update"
              v-for="lexeme in vocabula.lexemes"
              :key="lexeme.id"
              :lexeme="lexeme"
              :one="vocabula.lexemes.filter(lx => lx.group_number === lexeme.group_number).length === 1"
            />
          </li>

          <li
            v-for="lexeme in vocabula.lexemes"
            :key="lexeme.id"
          >
            <article class="card">
              <div class="p-1">
                <div class="inline md:flex md:flex-row md:justify-between">
                  <LexemeShort
                    v-if="update"
                    :lexeme="lexeme"
                    :one="vocabula.lexemes.filter(lx => lx.group_number === lexeme.group_number).length === 1"
                  />

                  <VModal
                    v-if="isEdit && lexemeForms[lexeme.id]"
                    header="Изменить лексему"
                    button-class="btn btn-sm btn-warning float-right"
                    class="max-w-screen-md w-full"
                    @close="applyLexeme(lexeme.id)"
                  >
                    <span v-if="lexeme.group_number === 0">Изменить общее</span>
                    <span v-else>Изменить лексему</span>

                    <template #postHeader>
                      <button
                        class="btn btn-sm btn-success"
                        @click="applyLexeme(lexeme.id)"
                      >
                        Сохранить
                      </button>
                      <VSaveLoader :is-save="isSave" />
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
                            />
                            <VInput
                              v-model="lexemeForms[lexeme.id].lexeme_number"
                              label="Номер лексемы"
                              type="number"
                            />
                          </div>

                          <VInput
                            v-model="lexemeForms[lexeme.id].short"
                            label="Короткое значение"
                            :errors="lexemeForms[lexeme.id].errors.short"
                            :required="lexeme.group_number !== 0 || lexeme.lexeme_number !== 0"
                          />

                          <div>
                            <div v-if="!isStyleAdd[lexeme.id]">
                              <button
                                class="link link-info"
                                @click="isStyleAdd[lexeme.id] = true"
                              >
                                Добавить стилистическую пометку
                              </button>
                            </div>
                            <div v-else>
                              <VInput
                                v-model="lexemeForms[lexeme.id].style"
                                label="Стилистическая пометка"
                                :errors="lexemeForms[lexeme.id].errors.style"
                              />
                              <div class="flex flex-row justify-end">
                                <button
                                  class="link link-error"
                                  @click="isStyleAdd[lexeme.id] = false"
                                >
                                  Убрать стилистическую пометку
                                </button>
                              </div>
                            </div>
                          </div>

                          <VMarkdownEditor
                            v-model="lexemeForms[lexeme.id].article"
                            label="Словарная статья"
                            :language="language"
                          />
                        </div>

                        <div class="flex flex-row flex-wrap justify-between">
                          <button
                            v-if="lexeme.group_number !== 0 || lexeme.lexeme_number !== 0"
                            class="btn btn-sm btn-error w-fit"
                            @click="deleteLexeme(lexeme.id)"
                          >
                            Удалить
                          </button>
                          <span v-else />

                          <div class="flex flex-row flex-wrap gap-2">
                            <VModal
                              header="Связи"
                              button-class="btn btn-sm btn-primary w-fit"
                              class="max-w-screen-md w-full"
                            >
                              Связи

                              <template #content>
                                <div class="flex flex-col">
                                  <div
                                    v-for="(arr, type) in _.groupBy(lexeme.links, 'type')"
                                    :key="type"
                                    class="inline"
                                  >
                                    <span class="w-fit h-fit float-left">{{ type }}:&thinsp;&thinsp;&thinsp;&thinsp;</span>
                                    <div class="flex flex-col">
                                      <div
                                        v-for="link in arr"
                                        :key="link.id"
                                        class="flex flex-row flex-wrap gap-4"
                                      >
                                        <LexemeLink :link="link" />

                                        <button
                                          class="btn btn-xs btn-error border-neutral-800"
                                          @click="deleteLink(lexeme, link)"
                                        >
                                          Удалить связь
                                        </button>
                                      </div>
                                    </div>
                                  </div>


                                  <VModal
                                    ref="linksModal"
                                    header="Добавить связь"
                                    button-class="mt-4 btn btn-sm btn-primary w-fit"
                                  >
                                    Добавить связь

                                    <template #postHeader>
                                      <button
                                        class="btn btn-sm btn-success"
                                        @click="addLink(lexeme)"
                                      >
                                        Добавить
                                      </button>

                                      <VFlashSuccess ref="successFlash" />
                                    </template>

                                    <template #content>
                                      <div class="flex flex-col">
                                        <VInputLexeme
                                          v-model="linkForm.to_lexeme_id"
                                          :language="language"
                                        />
                                        <VInput
                                          v-model="linkForm.type"
                                          label="Тип связи"
                                          :errors="linkForm.errors.type"
                                          required
                                        />
                                        <VInput
                                          v-model="linkForm.comment"
                                          label="Комментарий"
                                          :errors="linkForm.errors.comment"
                                        />
                                        <VCheckbox
                                          v-model="isLinkReverse"
                                          class="w-fit"
                                        >
                                          Добавить обратную связь?
                                        </VCheckbox>

                                        <div v-if="isLinkReverse">
                                          <VInput
                                            v-model="reverseLinkForm.type"
                                            label="Тип обратной связи"
                                            required
                                          />
                                          <VInput
                                            v-model="reverseLinkForm.comment"
                                            label="Комментарий обратной связи"
                                          />
                                        </div>

                                        <button
                                          class="mt-4 btn btn-sm btn-success w-fit"
                                          @click="addLink(lexeme)"
                                        >
                                          Добавить
                                        </button>
                                      </div>
                                    </template>
                                  </VModal>
                                </div>
                              </template>
                            </VModal>

                            <VModal
                              header="Грамматика"
                              button-class="btn btn-sm btn-primary w-fit"
                              class="max-w-screen-lg w-full"
                              @close="applyLexeme(lexeme.id)"
                            >
                              Грамматика

                              <template #postHeader>
                                <button
                                  class="btn btn-sm btn-success"
                                  @click="applyLexeme(lexeme.id)"
                                >
                                  Применить
                                </button>
                                <VSaveLoader :is-save="isSave" />
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
                        </div>
                      </div>
                    </template>
                  </VModal>
                </div>

                <LexemeArticle
                  v-if="update"
                  :lexeme="lexeme"
                  without-short
                />
              </div>
            </article>
          </li>
        </ul>
      </div>

      <div class="flex flex-row flex-wrap-reverse justify-between">
        <button
          v-if="isEdit"
          class="mt-4 btn btn-sm btn-error self-start w-fit"
          @click="deleteVocabula"
        >
          Удалить
        </button>

        <VModal
          v-if="isEdit"
          ref="createModal"
          button-class="mt-4 btn btn-sm btn-primary self-start"
          header="Добавить лексему"
        >
          <span>Добавить лексему</span>

          <template #postHeader>
            <button
              class="btn btn-sm btn-success"
              @click="createLexeme"
            >
              Добавить
            </button>
          </template>

          <template #content>
            <div class="flex flex-col gap-4">
              <div class="flex flex-col">
                <article class="alert block">
                  <p>
                    Оставьте номер группы пустым, чтобы автоматически поставился следующий после последнего существующего.
                  </p>
                  <p>
                    Оставьте номер лексемы пустым, чтобы автоматически поставился следующий после последнего существующего в группе.
                  </p>
                </article>
                <div class="grid grid-cols-2">
                  <VInput
                    v-model="lexemeForms[0].group_number"
                    label="Номер группы"
                    type="number"
                    input-class="min-w-0"
                  />
                  <VInput
                    v-model="lexemeForms[0].lexeme_number"
                    label="Номер лексемы"
                    type="number"
                    input-class="min-w-0"
                  />
                </div>

                <VInput
                  v-model="lexemeForms[0].short"
                  label="Короткое значение"
                  :errors="lexemeForms[0].errors.short"
                  required
                />

                <div>
                  <div v-if="!isStyleAdd[0]">
                    <button
                      class="link link-info"
                      @click="isStyleAdd[0] = true"
                    >
                      Добавить стилистическую пометку
                    </button>
                  </div>
                  <div v-else>
                    <VInput
                      v-model="lexemeForms[0].style"
                      label="Стилистическая пометка"
                      :errors="lexemeForms[0].errors.style"
                    />
                    <div class="flex flex-row justify-end">
                      <button
                        class="link link-error"
                        @click="isStyleAdd[0] = false"
                      >
                        Убрать стилистическую пометку
                      </button>
                    </div>
                  </div>
                </div>

                <VMarkdownEditor
                  v-model="lexemeForms[0].article"
                  label="Словарная статья"
                  :language="language"
                />
              </div>

              <div class="flex flex-row flex-wrap justify-between">
                <button
                  class="btn btn-sm btn-success w-fit"
                  @click="createLexeme"
                >
                  Добавить
                </button>

                <VModal
                  header="Грамматика"
                  button-class="btn btn-sm btn-primary w-fit"
                  class="w-[85dvw]"
                >
                  Грамматика

                  <template #content>
                    <LexemeGrammatics
                      v-model="lexemeForms[0].grammatics"
                      v-model:variables="lexemeForms[0].grammatics_variables"
                      :language="language"
                    />
                  </template>
                </VModal>
              </div>
            </div>
          </template>
        </VModal>
      </div>
    </div>
  </LanguageLayout>
</template>

<style scoped>

</style>