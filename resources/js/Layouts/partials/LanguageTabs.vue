<script setup>
import { Link } from "@inertiajs/vue3";
import VModal from "@/Components/VModal.vue";
import LanguageFlag from "@/Components/LanguageFlag.vue";
import route from "ziggy-js";

/* global route */

const { language } = defineProps({
    language: {
        type: Object,
        required: true,
    },
});

const tabs = [
    { name: 'О языке', url: route("languages.view", language.id), active: 'languages.view' },
    { name: 'Статьи', url: route("languages.articles", language.id), active: 'languages.articles*' },
    { name: 'Словарь', url: route("languages.vocabulary", language.id), active: 'languages.vocabulary*' },
    { name: 'Фонетика', url: 'route("languages.phonetic", language.id)', active: 'languages.phonetic' },
    { name: 'Орфография', url: 'route("languages.orthography", language.id)', active: 'languages.orthography' },
    { name: 'Грамматика', url: 'route("languages.grammatics", language.id)', active: 'languages.grammatics' },
];

if (language.can_edit) {
    tabs.push({ name: 'Настройки', url: 'route("languages.settings", language.id)', active: 'languages.settings' })
}
</script>

<template>
  <div class="flex flex-col gap-2">
    <h1 class="flex flex-row gap-2 items-center text-2xl self-center w-fit">
      <VModal
        modal-box-class="flex flex-col gap-2 items-center"
        button-class="h-fit w-fit"
        header="Флаг языка"
      >
        <LanguageFlag
          v-if="language.flag"
          class="hover:fill-neutral-400"
          size="3rem"
          :language="language"
        />

        <template #content>
          <LanguageFlag
            class="mx-auto"
            size="20rem"
            :language="language"
          />
        </template>
      </VModal>

      <span
        v-if="language.autonym"
        class="w-fit"
      >
        {{ language.autonym }}
      </span>
      <span
        v-if="language.autonym_transcription"
        class="w-fit text-lg"
      >
        /{{ language.autonym_transcription }}/
      </span>
    </h1>
    <div class="tab tab-lg h-fit p-0">
      <Link
        v-for="tab in tabs"
        :key="tab.name"
        class="tab tab-bordered"
        :class="{ 'tab-active': route().current(tab.active) }"
        :href="tab.url"
      >
        {{ tab.name }}
      </Link>
    </div>
  </div>
</template>

<style scoped>

</style>
