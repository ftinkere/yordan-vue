<script setup>
import Layout from "@/Layouts/Layout.vue";
import { computed } from "vue";
import CardLinkList from "@/Components/CardLinkList.vue";
import VInput from "@/Components/VInput.vue";
import VModal from "@/Components/VModal.vue";
import { useForm } from "@inertiajs/vue3";
import route from "ziggy-js";

const { _token, ...props } = defineProps({
    lasts: {
        type: Array,
        default: () => [],
    },
    owned: {
        type: Array,
        default: () => [],
    },
    // eslint-disable-next-line vue/prop-name-casing
    _token: {
        type: String,
        default: null,
    },
})

const addLanguageForm = useForm({
    name: null,
    _token: _token,
});

const lastsLanguages = computed(function () {
    return props.lasts.map(function (el) {
        return { ...el, href: route('languages.view', { code: el.id }) };
    });
});

const ownedLanguages = computed(function () {
    return props.owned.map(function (el) {
        return { ...el, href: route('languages.view', { code: el.id }) };
    });
});

</script>

<template>
  <Layout title="Главная">
    <!--  Вводная статья  -->
    <article class="mx-auto alert">
      <div>
        <p>Добро пожаловать на Ёрдан!</p>
        <p>На данном сайте вы можете создавать свои конланги. И делиться ими с другими людьми.</p>
      </div>
    </article>

    <!--  Блок со списками языков  -->
    <div
      class="mt-4 mx-auto grid grid-cols-1"
      :class="{ 'md:grid-cols-2': $page.props.auth.user, 'max-w-md': !$page.props.auth.user }"
    >
      <!--  Блок языков пользователя, если тот залогинен  -->
      <div
        v-if="$page.props.auth.user"
        class="flex flex-col gap-2 max-w-screen-xs"
      >
        <h4 class="self-center">
          Ваши языки:
        </h4>
        <CardLinkList
          v-if="ownedLanguages.length > 0"
          :list="ownedLanguages"
          class="border-e"
        />

        <!--  Модалка добавления нового языка  -->
        <VModal
          button-class="mb-4 md:mb-0 btn btn-sm w-full"
          header="Добавить язык"
          class="max-w-screen-md"
        >
          <span>Добавить язык</span>

          <template #content>
            <div class="flex flex-row items-end">
              <VInput
                v-model="addLanguageForm.name"
                label="Название"
                :errors="addLanguageForm.errors.name"
                class="grow"
                input-class="rounded-e-none"
                @keyup.enter="addLanguageForm.post(route('languages.store'))"
              >
                <template #button>
                  <button
                    class="btn btn-success rounded-s-none"
                    @click="addLanguageForm.post(route('languages.store'))"
                  >
                    Добавить
                  </button>
                </template>
              </VInput>
            </div>
          </template>
        </VModal>
      </div>
      <!--  Конец блока языков пользователя -->

      <!--  Список последних изменённых языков -->
      <div class="flex flex-col gap-2 max-w-screen-xs">
        <h4 class="self-center">
          Последние изменения:
        </h4>
        <CardLinkList
          v-if="lastsLanguages.length > 0"
          class="max-w-screen-xs"
          :list="lastsLanguages"
        />
      </div>
    </div>
  </Layout>
</template>

<style scoped>

</style>
