<script setup>
import LanguageLayout from "@/Layouts/LanguageLayout.vue";
import route from "ziggy-js";
import { useForm } from "@inertiajs/vue3";
import VSaveLoader from "@/Components/VSaveLoader.vue";

const { language } = defineProps({
    language: {
        type: Object,
        required: true,
    }
})

const deleteForm = useForm({})

const deleteLanguage = function () {
    deleteForm.delete(route('languages.delete', language.id))
}

const settingsForm = useForm({
    hidden: language.hidden,
})

function applyHidden(event) {
    console.log(event.target.checked)
    settingsForm.transform(data => {
        return { value: data.hidden }
    }).post(route('languages.hide', language.id), {
        preserveScroll: true,
    })
}

</script>

<template>
  <LanguageLayout :language="language">
    <div class="mx-auto form-control w-fit">
      <label>
        <span>
          Скрыть с главной страницы
        </span>

        <input
          v-model="settingsForm.hidden"
          type="checkbox"
          class="toggle toggle-primary rounded-full"
          style="background-image: none;"
          @change="applyHidden"
        >
      </label>
    </div>

    <button
      class="mt-10 btn btn-error"
      @click="deleteLanguage"
    >
      <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M135.2 17.7C140.6 6.8 151.7 0 163.8 0H284.2c12.1 0 23.2 6.8 28.6 17.7L320 32h96c17.7 0 32 14.3 32 32s-14.3 32-32 32H32C14.3 96 0 81.7 0 64S14.3 32 32 32h96l7.2-14.3zM32 128H416V448c0 35.3-28.7 64-64 64H96c-35.3 0-64-28.7-64-64V128zm96 64c-8.8 0-16 7.2-16 16V432c0 8.8 7.2 16 16 16s16-7.2 16-16V208c0-8.8-7.2-16-16-16zm96 0c-8.8 0-16 7.2-16 16V432c0 8.8 7.2 16 16 16s16-7.2 16-16V208c0-8.8-7.2-16-16-16zm96 0c-8.8 0-16 7.2-16 16V432c0 8.8 7.2 16 16 16s16-7.2 16-16V208c0-8.8-7.2-16-16-16z"/></svg>
      Удалить
      <VSaveLoader :is-save="!deleteForm.processing" />
    </button>
  </LanguageLayout>
</template>

<style scoped>

</style>