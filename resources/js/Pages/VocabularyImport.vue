<script setup>
import LanguageLayout from "@/Layouts/LanguageLayout.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import route from "ziggy-js";
import { ref } from "vue";

const { language } = defineProps({
    language: {
        required: true,
    }
})

const _token = usePage().props.csrf_token

const isLoaded = ref(false)
const tick = ref(false)

function onPaste(event) {
    const clipboardData = event.clipboardData
    let gsheets;
    try {
        gsheets = JSON.parse(clipboardData.getData('application/x-vnd.google-docs-embedded-grid_range_clip+wrapped'))
    } catch (e) {
        return
    }

    if (gsheets && !tick.value) {
        tick.value = true

        useForm({
            html: JSON.parse(gsheets.data).grh,
            _token,
        }).post(route('languages.vocabulary.import', [language.id]), {
            preserveScroll: true,
            onSuccess: () => {
                isLoaded.value = true
            }
        })
    }
}

</script>

<template>
  <LanguageLayout
    :language="language"
  >
    <div
      @paste="onPaste"
    >
      <div class="p-4">
        <button
          class="btn btn-primary"
          onclick="history.back()"
        >
          Назад
        </button>
      </div>
      <div
        v-if="!isLoaded"
        class="alert flex flex-col"
      >
        <p>
          На этой странице есть возможность импортировать примитивы лексики из своей гугл таблицы.
        </p>
        <p>
          Откройте гугл таблицу и выделите нужный фрагмент.
          Для этого если у вас не так, то исправьте.
          Первый столбец воспринимается как вокабула, второй как значение лексемы.
          После копирования нажмите ctrl/cmd + V на этой странице.
        </p>
        <p>
          Если вокабула повторяется, лексема будет добавлена под следующим групповым номером в словарной статье.
        </p>
      </div>
      <div
        v-else
        class="alert alert-success flex flex-col"
      >
        <p>
          Поздравляю, слова успешно загружены, не повторяйте ещё раз
        </p>
      </div>
    </div>
  </LanguageLayout>
</template>

<style scoped>

</style>