<script setup>
import LanguageLayout from "@/Layouts/LanguageLayout.vue";
import LexemeShort from "@/Components/LexemeShort.vue";
import LexemeArticle from "@/Components/LexemeArticle.vue";
import EditButton from "@/Components/buttons/EditButton.vue";
import { nextTick, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import route from "ziggy-js";
import VFlashSuccess from "@/Components/VFlashSuccess.vue";
import BackButton from "@/Components/buttons/BackButton.vue";
import VocabulaEditModal from "@/Components/modals/VocabulaEditModal.vue";
import OpenableImage from "@/Components/OpenableImage.vue";
import UploadableImage from "@/Components/UploadableImage.vue";
import LexemeEditModal from "@/Components/modals/LexemeEditModal.vue";
import LinksEditModal from "@/Components/modals/LinksEditModal.vue";
import GrammaticsEditModal from "@/Components/modals/GrammaticsEditModal.vue";

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

    const isEdit = ref(editMode)

    const update = ref(true)

    const flashSuccess = ref(null)
    const deleteVocabula = function () {
        useForm({}).delete(route('languages.vocabulary.delete', { code: language.id, vocabula: vocabula.id }))
    }

    // const updateLexemeForms = function (lexemes) {
    //     let forms = {}
    //
    //     forms[0] = useForm({
    //         group_number: null,
    //         lexeme_number: null,
    //         grammatics: [],
    //         grammatics_variables: [],
    //         short: '',
    //         article: '',
    //         style: '',
    //         _token,
    //     })
    //
    //
    //     lexemes.forEach((lexeme) => {
    //         forms[lexeme.id] = useForm({
    //             group_number: lexeme.group_number,
    //             lexeme_number: lexeme.lexeme_number,
    //             grammatics: lexeme?.grammatics?.map(g => `${g.grammatic_value_id}`),
    //             grammatics_variables: lexeme?.grammatics?.filter(g => g.is_variable === 1).map(g => `${g.grammatic_value_id}`),
    //             short: lexeme.short,
    //             article: lexeme.article,
    //             style: lexeme.style,
    //             _token,
    //         })
    //     })
    //
    //     return forms
    // }

    // const createModal = ref(null)
    //
    // const createLexeme = function () {
    //     lexemeForms[0]
    //         .transform(data => {
    //             if (data.grammatics) {
    //                 data.grammatics = data.grammatics.map(g => parseInt(g))
    //             }
    //             if (data.grammatics_variables) {
    //                 data.grammatics_variables = data.grammatics_variables.map(g => parseInt(g))
    //             }
    //             if (!isStyleAdd.value[0]) {
    //                 data.style = null
    //             }
    //             return data
    //         })
    //         .post(route('languages.vocabulary.lexemes.store', { code: language, vocabula: vocabula.id }), {
    //             onSuccess: (e) => {
    //                 createModal.value?.close()
    //                 lexemeForms = updateLexemeForms(e.props.vocabula.lexemes)
    //             },
    //         })
    // }

    const vocabulaEditModal = ref(null)

    const lexemeEditModal = ref(null)
    const lexemeId = ref(null)
    const lexemeUpdate = ref(true)

    const linksEditModal = ref(null)

    const grammaticsEditModal = ref(null)
</script>

<template>
  <LanguageLayout :language="language">
    <div class="card flex flex-col">
      <BackButton />

      <div class="flex flex-row justify-between items-end md:items-center">
        <div class="flex flex-row flex-wrap gap-2 items-center">
          <h4 class="card-title m-2 font-bold text-3xl">
            <span>{{ vocabula.vocabula }}</span>
            <span
              v-if="vocabula.transcription"
              class="font-normal text-md"
            >
              /{{ vocabula.transcription }}/
            </span>
          </h4>

          <EditButton
            v-if="isEdit"
            @click="vocabulaEditModal?.open()"
          >
            Изменить вокабулу
          </EditButton>
        </div>

        <div
          v-if="language.can_edit"
          class="flex flex-row flex-wrap-reverse gap-2 items-center justify-end"
        >
          <VFlashSuccess ref="flashSuccess" />

          <EditButton
            v-if="language.can_edit && !isEdit"
            @click="isEdit = true"
          />
          <button
            v-if="isEdit"
            class="btn btn-sm btn-success"
          >
            Сохранить
          </button>
          <button
            v-if="isEdit"
            class="btn btn-sm btn-primary"
            @click="isEdit = false"
          >
            Посмотреть
          </button>
        </div>
      </div>

      <div class="flex flex-row flex-wrap gap-2">
        <OpenableImage
          v-if="!isEdit && vocabula.image"
          :image="vocabula.image"
          :alt="vocabula.vocabula"
        />

        <UploadableImage
          v-else-if="isEdit"
          :image="vocabula.image"
          :alt="vocabula.vocabula"
          label="Изображение вокабулы"
          :push-url="route('languages.vocabulary.image', { code: language.id, vocabula: vocabula.id})"
          :delete-url="route('languages.vocabulary.image', { code: language.id, vocabula: vocabula.id})"
        />
      </div>

      <div class="ms-6">
        <ul class="divide-y divide-neutral-700">
          <li class="mb-6 flex flex-col">
            <template v-if="update">
              <LexemeShort
                v-for="lexeme in vocabula.lexemes"
                :key="lexeme.id"
                :lexeme="lexeme"
              />
            </template>
          </li>

          <li
            v-for="lexeme in vocabula.lexemes"
            :key="lexeme.id"
          >
            <article class="card">
              <div class="p-1">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                  <LexemeShort
                    v-if="update"
                    :lexeme="lexeme"
                  />

                  <!--<EditButton-->
                  <!--  v-if="language.can_edit && isEdit"-->
                  <!--  @click="lexemeId = lexeme.id; lexemeUpdate = false; nextTick(() => { lexemeUpdate = true; nextTick(() => { lexemeEditModal?.open() }) }) "-->
                  <!--&gt;-->
                  <!--  <span v-if="lexeme.group_number === 0">Изменить общее</span>-->
                  <!--  <span v-else>Изменить лексему</span>-->
                  <!--</EditButton>-->
                  <div
                    v-if="language.can_edit && isEdit"
                    class="w-fit self-end bg-neutral-600/20 rounded-full"
                  >
                    <button
                      class="group btn btn-sm rounded-full btn-ghost hover:bg-primary"
                      @click="lexemeId = lexeme.id; lexemeUpdate = false; nextTick(() => { lexemeUpdate = true; nextTick(() => { grammaticsEditModal?.open() }) }) "
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M224 96C135.6 96 64 167.6 64 256s71.6 160 160 160c77.4 0 142-55 156.8-128H256c-17.7 0-32-14.3-32-32s14.3-32 32-32H400c25.8 0 49.6 21.4 47.2 50.6C437.8 389.6 341.4 480 224 480C100.3 480 0 379.7 0 256S100.3 32 224 32c57.4 0 109.7 21.6 149.3 57c13.2 11.8 14.3 32 2.5 45.2s-32 14.3-45.2 2.5C302.3 111.4 265 96 224 96z"/></svg>
                      <span class="hidden group-hover:inline">
                        Грамматика
                      </span>
                    </button>

                    <button
                      class="group btn btn-sm rounded-full btn-ghost hover:bg-primary"
                      @click="lexemeId = lexeme.id; lexemeUpdate = false; nextTick(() => { lexemeUpdate = true; nextTick(() => { linksEditModal?.open() }) }) "
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 640 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M579.8 267.7c56.5-56.5 56.5-148 0-204.5c-50-50-128.8-56.5-186.3-15.4l-1.6 1.1c-14.4 10.3-17.7 30.3-7.4 44.6s30.3 17.7 44.6 7.4l1.6-1.1c32.1-22.9 76-19.3 103.8 8.6c31.5 31.5 31.5 82.5 0 114L422.3 334.8c-31.5 31.5-82.5 31.5-114 0c-27.9-27.9-31.5-71.8-8.6-103.8l1.1-1.6c10.3-14.4 6.9-34.4-7.4-44.6s-34.4-6.9-44.6 7.4l-1.1 1.6C206.5 251.2 213 330 263 380c56.5 56.5 148 56.5 204.5 0L579.8 267.7zM60.2 244.3c-56.5 56.5-56.5 148 0 204.5c50 50 128.8 56.5 186.3 15.4l1.6-1.1c14.4-10.3 17.7-30.3 7.4-44.6s-30.3-17.7-44.6-7.4l-1.6 1.1c-32.1 22.9-76 19.3-103.8-8.6C74 372 74 321 105.5 289.5L217.7 177.2c31.5-31.5 82.5-31.5 114 0c27.9 27.9 31.5 71.8 8.6 103.9l-1.1 1.6c-10.3 14.4-6.9 34.4 7.4 44.6s34.4 6.9 44.6-7.4l1.1-1.6C433.5 260.8 427 182 377 132c-56.5-56.5-148-56.5-204.5 0L60.2 244.3z"/></svg>
                      <span class="hidden group-hover:inline">
                        Связи
                      </span>
                    </button>

                    <button
                      class="group btn btn-sm rounded-full btn-ghost hover:bg-primary"
                      @click="lexemeId = lexeme.id; lexemeUpdate = false; nextTick(() => { lexemeUpdate = true; nextTick(() => { lexemeEditModal?.open() }) })"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z"/></svg>
                      <span class="hidden group-hover:inline">
                        <span v-if="lexeme.group_number === 0">Общее</span>
                        <span v-else>Лексема</span>
                      </span>
                    </button>
                  </div>
                </div>

                <!--<button-->
                <!--  v-if="language.can_edit && isEdit"-->
                <!--  class="mb-2 btn btn-primary btn-sm"-->
                <!--  @click="lexemeId = lexeme.id; lexemeUpdate = false; nextTick(() => { lexemeUpdate = true; nextTick(() => { grammaticsEditModal?.open() }) }) "-->
                <!--&gt;-->
                <!--  <span>Изменить грамматику</span>-->
                <!--</button>-->

                <LexemeArticle
                  v-if="update"
                  :lexeme="lexeme"
                  without-short
                />

                <!--<button-->
                <!--  v-if="language.can_edit && isEdit"-->
                <!--  class="mt-2 btn btn-primary btn-sm"-->
                <!--  @click="lexemeId = lexeme.id; lexemeUpdate = false; nextTick(() => { lexemeUpdate = true; nextTick(() => { linksEditModal?.open() }) }) "-->
                <!--&gt;-->
                <!--  <span>Изменить связи</span>-->
                <!--</button>-->
              </div>
            </article>
          </li>
        </ul>
      </div>

      <div
        v-if="isEdit"
        class="mt-4 flex flex-row flex-wrap-reverse justify-between"
      >
        <button
          class="btn btn-sm btn-error self-start w-fit"
          @click="deleteVocabula"
        >
          Удалить
        </button>

        <button
          class="btn btn-sm btn-primary self-end w-fit"
          @click="lexemeId = null; lexemeUpdate = false; nextTick(() => { lexemeUpdate = true; nextTick(() => { lexemeEditModal?.open() }) })"
        >
          Добавить лексему
        </button>
        <!--<VModal-->
        <!--  v-if="isEdit"-->
        <!--  ref="createModal"-->
        <!--  button-class="mt-4 btn btn-sm btn-primary self-start"-->
        <!--  header="Добавить лексему"-->
        <!--&gt;-->
        <!--  <span>Добавить лексему</span>-->

        <!--  <template #postHeader>-->
        <!--    <button-->
        <!--      class="btn btn-sm btn-success"-->
        <!--      @click="createLexeme"-->
        <!--    >-->
        <!--      Добавить-->
        <!--    </button>-->
        <!--  </template>-->

        <!--  <template #content>-->
        <!--    <div class="flex flex-col gap-4">-->
        <!--      <div class="flex flex-col">-->
        <!--        <article class="alert block">-->
        <!--          <p>-->
        <!--            Оставьте номер группы пустым, чтобы автоматически поставился следующий после последнего существующего.-->
        <!--          </p>-->
        <!--          <p>-->
        <!--            Оставьте номер лексемы пустым, чтобы автоматически поставился следующий после последнего существующего в группе.-->
        <!--          </p>-->
        <!--        </article>-->
        <!--        <div class="grid grid-cols-2">-->
        <!--          <VInput-->
        <!--            v-model="lexemeForms[0].group_number"-->
        <!--            label="Номер группы"-->
        <!--            type="number"-->
        <!--            input-class="min-w-0"-->
        <!--          />-->
        <!--          <VInput-->
        <!--            v-model="lexemeForms[0].lexeme_number"-->
        <!--            label="Номер лексемы"-->
        <!--            type="number"-->
        <!--            input-class="min-w-0"-->
        <!--          />-->
        <!--        </div>-->

        <!--        <VInput-->
        <!--          v-model="lexemeForms[0].short"-->
        <!--          label="Короткое значение"-->
        <!--          :errors="lexemeForms[0].errors.short"-->
        <!--          required-->
        <!--        />-->

        <!--        <div>-->
        <!--          <div v-if="!isStyleAdd[0]">-->
        <!--            <button-->
        <!--              class="link link-info"-->
        <!--              @click="isStyleAdd[0] = true"-->
        <!--            >-->
        <!--              Добавить стилистическую пометку-->
        <!--            </button>-->
        <!--          </div>-->
        <!--          <div v-else>-->
        <!--            <VInput-->
        <!--              v-model="lexemeForms[0].style"-->
        <!--              label="Стилистическая пометка"-->
        <!--              :errors="lexemeForms[0].errors.style"-->
        <!--            />-->
        <!--            <div class="flex flex-row justify-end">-->
        <!--              <button-->
        <!--                class="link link-error"-->
        <!--                @click="isStyleAdd[0] = false"-->
        <!--              >-->
        <!--                Убрать стилистическую пометку-->
        <!--              </button>-->
        <!--            </div>-->
        <!--          </div>-->
        <!--        </div>-->

        <!--        <VMarkdownEditor-->
        <!--          v-model="lexemeForms[0].article"-->
        <!--          label="Словарная статья"-->
        <!--          :language="language"-->
        <!--        />-->
        <!--      </div>-->

        <!--      <div class="flex flex-row flex-wrap justify-between">-->
        <!--        <button-->
        <!--          class="btn btn-sm btn-success w-fit"-->
        <!--          @click="createLexeme"-->
        <!--        >-->
        <!--          Добавить-->
        <!--        </button>-->

        <!--        <VModal-->
        <!--          header="Грамматика"-->
        <!--          button-class="btn btn-sm btn-primary w-fit"-->
        <!--          class="w-[85dvw]"-->
        <!--        >-->
        <!--          Грамматика-->

        <!--          <template #content>-->
        <!--            <LexemeGrammatics-->
        <!--              v-model="lexemeForms[0].grammatics"-->
        <!--              v-model:variables="lexemeForms[0].grammatics_variables"-->
        <!--              :language="language"-->
        <!--            />-->
        <!--          </template>-->
        <!--        </VModal>-->
        <!--      </div>-->
        <!--    </div>-->
        <!--  </template>-->
        <!--</VModal>-->
      </div>
    </div>

    <VocabulaEditModal
      v-if="language.can_edit && isEdit"
      ref="vocabulaEditModal"
      :vocabula="vocabula"
      @success="flashSuccess?.flash()"
    />

    <LexemeEditModal
      v-if="lexemeUpdate && language.can_edit && isEdit"
      ref="lexemeEditModal"
      :lexeme="vocabula.lexemes.find(l => l.id == lexemeId)"
      :vocabula="vocabula"
      @success="flashSuccess?.flash(); update = false; nextTick(() => { update = true })"
    />

    <LinksEditModal
      v-if="lexemeUpdate && language.can_edit && isEdit"
      ref="linksEditModal"
      :lexeme="vocabula.lexemes.find(l => l.id == lexemeId)"
      @success="flashSuccess?.flash(); update = false; nextTick(() => { update = true })"
    />

    <GrammaticsEditModal
      v-if="lexemeUpdate && language.can_edit && isEdit"
      ref="grammaticsEditModal"
      :lexeme="vocabula.lexemes.find(l => l.id == lexemeId)"
      @success="flashSuccess?.flash(); update = false; nextTick(() => { update = true })"
    />
  </LanguageLayout>
</template>

<style scoped>

</style>