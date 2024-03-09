<script setup>
import LanguageLayout from "@/Layouts/LanguageLayout.vue";
import LanguageArticle from "@/Components/LanguageArticle.vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import TagLink from "@/Components/TagLink.vue";
import VModal from "@/Components/VModal.vue";
import VInput from "@/Components/VInput.vue";
import route from "ziggy-js";
import VPaginator from "@/Components/VPaginator.vue";


const { language, articles } = defineProps({
    language: {
        type: Object,
        required: true,
    },
    articles: {
        type: Object,
        required: true,
    },
});

const _token = usePage().props.csrf_token

const articleForm = useForm({
    title: '',
    _token,
});

const newArticle = function () {
    if (articleForm.title.length === 0) {
        articleForm.setError('title', 'Заголовок не может быть пустым.');
        return;
    }
    articleForm.post(route('languages.articles.store', { code: language.id }))
};

function getArticles(articles) {
    if (language.can_edit) {
        return articles.data
    }
    return articles.data.filter(article => article.published_at)
}
</script>

<template>
  <LanguageLayout :language="language">
    <VModal
      v-if="language.can_edit"
      button-class="mb-4 btn btn-primary"
      header="Добавить статью"
    >
      <span>Добавить статью</span>

      <template #content>
        <div class="flex flex-row items-end">
          <VInput
            v-model="articleForm.title"
            label="Заголовок статьи"
            :errors="articleForm.errors.title"
            class="grow"
            input-class="rounded-e-none"
            @keyup.enter="newArticle"
          >
            <template #button>
              <button
                class="btn btn-success rounded-s-none"
                @click="newArticle"
              >
                Добавить
              </button>
            </template>
          </VInput>
        </div>
      </template>
    </VModal>

    <div class="mb-4 flex flex-row flex-wrap items-center gap-2">
      <Link
        v-if="language.tags.length > 0"
        :href="route('languages.articles', { code: language.id })"
        class="btn btn-xs btn-ghost btn-circle hover:btn-error fill-white"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          height="1em"
          viewBox="0 0 384 512"
        >
          <!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
          <path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/>
        </svg>
      </Link>
      <TagLink
        v-for="tag in language.tags"
        :key="tag.id"
        :tag="tag"
        :not-link="route().current('languages.articles', { code: language.id, tag: tag.tag })"
      />
    </div>

    <div class="flex flex-col gap-2">
      <div
        v-for="article in getArticles(articles)"
        :key="article.id"
        class="flex flex-row gap-2"
      >
        <LanguageArticle
          without-button
          :article="article"
        />
      </div>
      <article
        v-if="articles.data.length === 0"
        class="alert"
      >
        <div>
          <p>Статей не обнаружено(</p>
          <p v-if="language.can_edit">
            Добавьте статью про ваш язык.
            Например про части речи, склонение и спряжение слов, как слова компонуются в синтаксис,
            укажите интересные примеры предложений или текстов или что вы ещё можете сообщить.
          </p>
        </div>
      </article>
    </div>

    <VPaginator :paginator="articles" />
  </LanguageLayout>
</template>

<style scoped>

</style>
