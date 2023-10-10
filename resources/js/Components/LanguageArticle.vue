<script setup>
import { Link } from "@inertiajs/vue3";
import VMarkdownViewer from "@/Components/VMarkdownViewer.vue";
import TagLink from "@/Components/TagLink.vue";
import { inject } from "vue";
import route from "ziggy-js";

const { article, view } = defineProps({
    article: {
        type: Object,
        required: true,
    },
    view: Boolean,
});

const language = inject('language');
</script>

<template>
  <div class="p-4 card border-t flex flex-col gap-2 w-full">
    <div class="flex flex-col">
      <Link
        v-if="!view"
        :href="route('languages.articles.view', { code: language.id, article: article.id })"
        class="font-bold text-xl me-4 hover:text-info"
      >
        <span>{{ article.title }}</span>
      </Link>
      <h4
        v-else
        class="font-bold text-xl me-4"
      >
        {{ article.title }}
      </h4>

      <div class="flex flex-row flex-wrap gap-2 items-baseline">
        <span
          v-if="article.published_at === null"
          class="text-warning"
        >
          Не опубликовано
        </span>
        <span
          v-else
          class="text-neutral-400 font-light"
        >
          {{ (new Date(article.published_at)).toLocaleString() }}
        </span>

        <TagLink
          v-for="tag in article.tags"
          :key="tag.id"
          :tag="tag"
        />
      </div>
    </div>
    <VMarkdownViewer
      :content="article.article"
      class="break-words"
      :class="{ 'line-clamp-6' : !view }"
    />
  </div>
</template>

<style scoped>

</style>
