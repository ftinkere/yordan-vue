<script setup>
import LanguageLayout from "@/Layouts/LanguageLayout.vue";
import LanguageArticle from "@/Components/LanguageArticle.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import VModal from "@/Components/VModal.vue";
import VInput from "@/Components/VInput.vue";
import { computed, ref, watch } from "vue";
import VSaveLoader from "@/Components/VSaveLoader.vue";
import VMarkdownEditor from "@/Components/VMarkdownEditor.vue";
import _ from "lodash";
import EditButton from "@/Components/buttons/EditButton.vue";
import route from "ziggy-js";
import VFlashSuccess from "@/Components/VFlashSuccess.vue";

const { language, article, editMode } = defineProps({
    language: {
        type: Object,
        required: true,
    },
    article: {
        type: Object,
        required: true,
    },
    editMode: {
        type: Boolean,
        default: false,
    }
})

const _token = usePage().props.csrf_token;

const isEdit = ref(editMode);

const articleForm = useForm({
    title: article.title,
    article: article.article,
    _token,
})

const successFlash = ref(null)

const applyForm = function () {
    articleForm.post(route('languages.articles.update', { code: language.id, article: article.id }), {
        onSuccess: () => successFlash.value?.flash(),
        preserveScroll: true,
    })
}

watch(computed(() => articleForm.data()), _.debounce(applyForm, 3000));

const tagForm = useForm({
    tag: '',
});

const pushTag = function () {
    tagForm.post(route('languages.articles.tags', { code: language.id, article: article.id }))
    tagForm.reset()
}

const deleteTag = function (tag) {
    useForm({ _token }).delete(route('languages.articles.tags.delete', {
        code: language.id,
        article: article.id,
        tag: tag.id
    }))
}

const publish = function () {
    useForm({ _token }).post(route('languages.articles.publish', { code: language.id, article: article.id }))
}

const unpublish = function () {
    useForm({ _token }).post(route('languages.articles.unpublish', { code: language.id, article: article.id }))
}

const deleteArticle = function () {
    useForm({ _token }).delete(route('languages.articles.delete', { code: language.id, article: article.id }))
}
</script>

<template>
  <LanguageLayout :language="language">
    <div class="px-4 flex flex-col gap-2">
      <div
        v-if="!isEdit"
        class="mb-2 flex flex-row justify-between"
      >
        <button
          class="btn btn-sm"
          onclick="history.back()"
        >
          Назад
        </button>

        <div>
          <VSaveLoader :is-save="!articleForm.isDirty" />
          <EditButton
            v-if="language.can_edit"
            @click="isEdit = true"
          />
        </div>
      </div>

      <LanguageArticle
        v-if="!isEdit"
        :article="article"
        :is-save="!articleForm.isDirty"
        view
      />

      <div
        v-else
        class="flex flex-col gap-2 w-full"
      >
        <div class="flex flex-row justify-between">
          <button
            v-if="article.published_at === null"
            class="me-6 btn btn-sm btn-warning"
            @click="publish"
          >
            Опубликовать
          </button>
          <button
            v-else
            class="me-6 btn btn-sm btn-warning"
            @click="unpublish"
          >
            Снять с публикации
          </button>

          <div class="flex flex-row gap-2 items-center">
            <VSaveLoader :is-save="!articleForm.isDirty" />
            <VFlashSuccess ref="successFlash" />

            <button
              class="btn btn-sm btn-success"
              @click="applyForm"
            >
              Сохранить
            </button>
            <button
              class="btn btn-sm btn-primary w-fit"
              @click="applyForm(); isEdit = false"
            >
              Посмотреть
            </button>
          </div>
        </div>

        <div class="mt-2 flex flex-col gap-4">
          <VInput
            v-model="articleForm.title"
            label="Заголовок"
            :errors="articleForm.errors.title"
          />

          <div class="flex flex-row flex-wrap gap-2 items-center">
            <span>Теги:</span>
            <div
              v-for="tag in article.tags"
              :key="tag.id"
              class="badge pe-0 h-fit items-center rounded-full"
            >
              <span class="px-2">{{ tag.tag }}</span>
              <button
                class="btn btn-sm btn-circle btn-ghost hover:btn-error fill-white"
                @click="deleteTag(tag)"
              >
                <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 384 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg>
              </button>
            </div>
            <VModal
              button-class="text-2xl btn btn-sm btn-primary btn-circle fill-white"
              header="Добавить тег"
            >
              <svg xmlns="http://www.w3.org/2000/svg" height="0.6em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H192V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H400c17.7 0 32-14.3 32-32s-14.3-32-32-32H256V80z"/></svg>

              <template #content>
                <div>
                  <VInput
                    v-model="tagForm.tag"
                    label="Тег"
                    class="rounded-e-none"
                    :errors="tagForm.errors.tag"
                    @keyup.enter="pushTag"
                  >
                    <template #button>
                      <button
                        class="btn btn-success rounded-s-none"
                        @click="pushTag"
                      >
                        Добавить
                      </button>
                    </template>
                  </VInput>
                </div>
              </template>
            </VModal>
          </div>

          <VMarkdownEditor
            v-model="articleForm.article"
            label="Статья"
            :language="language"
          />

          <button
            class="btn btn-error btn-sm w-fit"
            @click="deleteArticle"
          >
            Удалить статью
          </button>
        </div>
      </div>
    </div>
  </LanguageLayout>
</template>

<style scoped>

</style>
