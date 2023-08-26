<script setup>
import LanguageLayout from "@/Layouts/LanguageLayout.vue";
import VMarkdownViewer from "@/Components/VMarkdownViewer.vue";
import VMarkdownEditor from "@/Components/VMarkdownEditor.vue";
import {ref} from "vue";
import TodoList from "@/Components/LanguageTodoList.vue";

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

console.log(language);

</script>

<template>
    <LanguageLayout :language="language" :can_edit="can_edit">
        <h1 class="text-2xl font-bold">
            <span>{{ language.name }}</span>
            <span v-if="language.autonym"> - {{ language.autonym }}</span>
            <span v-if="language.autonym_transcription"> /{{ language.autonym_transcription }}/</span>
        </h1>

        <div class="mt-4 ms-6 mb-4 flex flex-row gap-2 items-baseline">
            <span v-if="language.type">{{ language.type }}</span>
            <span v-if="language.statuses.filter((el) => el.status === 'new').length > 0">Новый</span>
            <span v-if="language.statuses.filter((el) => el.status === 'progress').length > 0">В процессе</span>
            <span v-if="language.statuses.filter((el) => el.status === 'complete').length > 0">Завершён</span>
        </div>

        <div>
            <VMarkdownViewer class="card border-y p-4 rounded-2xl text-white"
                             v-if="language.base_articles?.about"
                             :content="language.base_articles?.about" />
            <TodoList class="mt-4"  :language="language"/>
            <VMarkdownViewer class="mt-4 card border-y border-warning p-4 rounded-2xl text-white"
                             v-if="language.dev_note?.note"
                             :content="language.dev_note?.note" />
        </div>

    </LanguageLayout>
</template>

<style scoped>

</style>
