<script setup>
import LanguageLayout from "@/Layouts/LanguageLayout.vue";
import route from "ziggy-js";
import { Link, useForm } from "@inertiajs/vue3";
import LexemeShort from "@/Components/LexemeShort.vue";
import LexemeArticle from "@/Components/LexemeArticle.vue";
import VPaginator from "@/Components/VPaginator.vue";
import VInput from "@/Components/VInput.vue";
import VModal from "@/Components/VModal.vue";
import VCheckbox from "@/Components/VCheckbox.vue";
import { ref } from "vue";
import TranscriptionConverter from "@/Components/TranscriptionConverter.vue";
import VMarkdownEditor from "@/Components/VMarkdownEditor.vue";
import LexemeGrammatics from "@/Components/LexemeGrammatics.vue";

const { language, vocables } = defineProps({
    language: {
        type: Object,
        required: true,
    },
    vocables: {
        type: Object,
        required: true,
    }
})

const searchForm = useForm({
    q: (new URLSearchParams(window.location.search)).get('q') ?? '',
    grammatic_filters: (new URLSearchParams(window.location.search)).getAll('grammatic_filters[]'),
})

const grammatic_filters_ = ref(searchForm.grammatic_filters)

const search = function() {
    searchForm
        .transform(data => {
            if (data.grammatic_filters !== null) {
                data.grammatic_filters = grammatic_filters_.value;
            }
            return data;
        })
        .get(route('languages.vocabulary', { code: language.id }))
}

const vocabulaForm = useForm({
    vocabula: '',
    transcription: '',
    grammatics: [],
    grammatics_variables: [],
    grammatics_general: true,
    lexeme: {
        short: '',
        article: '',
    }
})

const isGrammatic = ref(false);
const isLexeme = ref(false);


const addVocabulaModal = ref(null);
const lexemeGrammaticsModal = ref(null);

const pushVocabula = function() {
    vocabulaForm
        .transform(data => {
            if (!isGrammatic.value) {
                data.grammatics = [];
                data.grammatics_variables = [];
            }
            if (!isLexeme.value) {
                data.lexeme.short = '';
                data.lexeme.article = '';
            }
            return data
        }).post(route('languages.vocabulary.store', { code: language.id }), {
            onSuccess: () => { addVocabulaModal.value.close() },
    })
}

</script>

<template>
  <LanguageLayout :language="language">
    <div class="mt-4 flex flex-col gap-4">
      <div class="flex flex-col sm:flex-row gap-2 justify-between">
        <VInput
          v-model="searchForm.q"
          type="search"
          placeholder="Введите для поиска..."
          class="w-full"
          input-class="rounded-e-none"
          @keyup.enter="search"
        >
          <template #button>
            <button
              class="btn btn-primary rounded-s-none"
              @click="search"
            >
              Поиск
            </button>
          </template>
        </VInput>
      </div>

      <div class="flex flex-row flex-wrap justify-between items-center">
        <VModal
          header="Фильтр грамматики"
          class="w-full md:min-w-[95dvw]"
        >
          <svg
            class="fill-white"
            xmlns="http://www.w3.org/2000/svg"
            height="1em"
            viewBox="0 0 512 512"
          >
            <!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
            <path d="M3.9 54.9C10.5 40.9 24.5 32 40 32H472c15.5 0 29.5 8.9 36.1 22.9s4.6 30.5-5.2 42.5L320 320.9V448c0 12.1-6.8 23.2-17.7 28.6s-23.8 4.3-33.5-3l-64-48c-8.1-6-12.8-15.5-12.8-25.6V320.9L9 97.3C-.7 85.4-2.8 68.8 3.9 54.9z" />
          </svg>
          Фильтр грамматики

          <template #postHeader>
            <button
              class="btn btn-sm btn-primary"
              type="submit"
              @click="search"
            >
              Применить
            </button>
            <button
              class="btn btn-sm btn-warning"
              type="reset"
              @click="searchForm.grammatic_filters = null; search()"
            >
              Сбросить
            </button>
          </template>

          <template #content>
            <LexemeGrammatics
              v-model="grammatic_filters_"
              :language="language"
            />
          </template>
        </VModal>

        <VModal
          v-if="language.can_edit"
          ref="addVocabulaModal"
          button-class="btn btn-sm btn-success"
          header="Добавить вокабулу"
          :class="{ 'min-w-[85dvw]': isLexeme }"
        >
          <span>Добавить вокабулу</span>

          <template #content>
            <div class="flex flex-col">
              <VInput
                v-model="vocabulaForm.vocabula"
                label="Вокабула"
                :errors="vocabulaForm.errors.vocabula"
                required
              />
              <div>
                <VInput
                  v-model="vocabulaForm.transcription"
                  label="Транскрипция"
                  :errors="vocabulaForm.errors.transcription"
                  required
                />
                <TranscriptionConverter v-model="vocabulaForm.transcription" />
              </div>
              <div class="flex flex-row justify-between">
                <VCheckbox
                  v-model="isGrammatic"
                  label-class="w-fit"
                >
                  Добавить грамматику?
                </VCheckbox>
                <VModal
                  v-if="isGrammatic"
                  ref="lexemeGrammaticsModal"
                  header="Добавить грамматику"
                  button-class="btn btn-sm btn-primary"
                  class="w-[85dvw]"
                >
                  Грамматика

                  <template #postHeader>
                    <button
                      class="btn btn-sm btn-success"
                      @click="lexemeGrammaticsModal.close()"
                    >
                      Применить
                    </button>
                  </template>

                  <template #content>
                    <VCheckbox
                      v-model="vocabulaForm.grammatics_general"
                      label-class="w-fit"
                    >
                      Применить ко всем будущим лексемам?
                    </VCheckbox>
                    <LexemeGrammatics
                      v-model="vocabulaForm.grammatics"
                      v-model:variables="vocabulaForm.grammatics_variables"
                      :language="language"
                    />
                  </template>
                </VModal>
              </div>
              <div class="flex flex-col">
                <VCheckbox
                  v-model="isLexeme"
                  label-class="w-fit"
                >
                  Добавить первую лексему?
                </VCheckbox>
                <VInput
                  v-if="isLexeme"
                  v-model="vocabulaForm.lexeme.short"
                  label="Короткое значение"
                  :errors="vocabulaForm.errors.lexeme"
                />
                <VMarkdownEditor
                  v-if="isLexeme"
                  v-model="vocabulaForm.lexeme.article"
                  :language="language"
                  label="Словарная статья"
                />
              </div>
              <button
                class="mt-2 btn btn-success w-fit self-end"
                @click="pushVocabula"
              >
                Добавить
              </button>
            </div>
          </template>
        </VModal>
      </div>

      <div
        v-for="vocabula in vocables.data"
        :key="vocabula.id"
        class="card border-t dark:border-t-neutral-700"
      >
        <Link href="">
          <h4 class="card-title m-2">
            <span>{{ vocabula.vocabula }}</span>
            <span class="font-normal text-md">/{{ vocabula.transcription }}/</span>
          </h4>
        </Link>
        <div class="ms-6">
          <ul class="divide-y dark:divide-neutral-700">
            <template
              v-for="lexeme in vocabula.lexemes"
              :key="lexeme.id"
            >
              <li v-if="lexeme.short.length > 0 || lexeme.grammatics.length > 0">
                <details
                  v-if="lexeme.article.length > 0 || lexeme.links.length > 0"
                  class="card collapse collapse-arrow"
                >
                  <summary class="collapse-title min-h-fit p-1">
                    <LexemeShort
                      :lexeme="lexeme"
                      :one="vocabula.lexemes.filter(lx => lx.group_number === lexeme.group_number).length === 1"
                    />
                  </summary>
                  <div class="collapse-content">
                    <!--<x-language.vocabula.lexeme_article :lexeme="$lexeme" vocabulary />-->
                    <LexemeArticle
                      :lexeme="lexeme"
                      without-short
                    />
                  </div>
                </details>

                <article
                  v-else
                  class="card"
                >
                  <div class="min-h-fit p-1">
                    <LexemeShort
                      :lexeme="lexeme"
                      :one="vocabula.lexemes.filter(lx => lx.group_number === lexeme.group_number).length === 1"
                    />
                  </div>
                </article>
              </li>
            </template>
          </ul>
        </div>
      </div>

      <VPaginator :paginator="vocables" />
    </div>
  </LanguageLayout>
</template>

<style scoped>

</style>
