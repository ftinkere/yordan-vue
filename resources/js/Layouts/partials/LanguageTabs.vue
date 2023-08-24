<script setup>
import { Link } from "@inertiajs/vue3";
import route from "ziggy-js";

const { language, can_edit } = defineProps({
    language: {
        type: Object,
        required: true,
    },
    can_edit: {
        type: Boolean,
        default: false,
    }
});

const tabs = [
    { name: 'О языке', url: route("languages.view", language.id), active: 'languages.view' },
    { name: 'Статьи', url: 'route("languages.articles", language.id)', active: 'languages.articles*' },
    { name: 'Словарь', url: 'route("languages.vocabulary", language.id)', active: 'languages.vocabulary*' },
    { name: 'Фонетика', url: 'route("languages.phonetic", language.id)', active: 'languages.phonetic' },
    { name: 'Орфография', url: 'route("languages.orthography", language.id)', active: 'languages.orthography' },
    { name: 'Грамматика', url: 'route("languages.grammatics", language.id)', active: 'languages.grammatics' },
];

if (can_edit) {
    tabs.push({ name: 'Настройки', url: 'route("languages.settings", language.id)', active: 'languages.settings' })
}
</script>

<template>
    <div class="flex flex-col lg:flex-row gap-2 lg:gap-6">
        <h1 class="text-2xl self-center">{{ language.autonym }} <span class="text-lg">/{{ language.autonym_transcription }}/</span></h1>
        <div class="tab tab-lg h-fit p-0">
            <Link v-for="tab in tabs"
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
