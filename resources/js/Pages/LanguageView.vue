<script setup>
import LanguageLayout from "@/Layouts/LanguageLayout.vue";
import VMarkdownViewer from "@/Components/VMarkdownViewer.vue";
import VMarkdownEditor from "@/Components/VMarkdownEditor.vue";
import { computed, reactive, ref, watch } from "vue";
import LanguageTodoAction from "@/Components/LanguageTodoAction.vue";
import VModal from "@/Components/VModal.vue";
import VInput from "@/Components/VInput.vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import TranscriptionConverter from "@/Components/TranscriptionConverter.vue";
import _ from "lodash";
import UserShort from "@/Components/UserShort.vue";
import VInputFile from "@/Components/VInputFile.vue";
import LanguageFlag from "@/Components/LanguageFlag.vue";
import VSaveLoader from "@/Components/VSaveLoader.vue";
import EditButton from "@/Components/EditButton.vue";
import route from "ziggy-js";
import VFlashSuccess from "@/Components/VFlashSuccess.vue";

const { action, language } = defineProps({
    language: {
        type: Object,
        required: true,
    },
    action: {
        type: Object,
        default: null,
    }
});

const _token = usePage().props.csrf_token

const autonymModal = ref(null)
const autonymTranscriptionModal = ref(null)
const typeModal = ref(null)
const aboutModal = ref(null)
const phoneticModal = ref(null)
const articleModal = ref(null)

const actionModal = computed(() => {
    if (!action) {
        return  null;
    }

    if (action.modal === 'autonym') {
        return autonymModal
    } else if (action.modal === 'autonym_transcription') {
        return autonymTranscriptionModal
    } else if (action.modal === 'type') {
        return typeModal
    } else if (action.modal === 'about') {
        return aboutModal
    }  else if (action.modal === 'phonetic') {
        return phoneticModal
    } else if (action.modal === 'article') {
        return articleModal
    }
    return null
})

const actionAction = function () {
    if (action?.url) {
        router.visit(action.url)
    } else {
        actionModal.value?.value?.open()
    }
}

const actionUpdate = function () {
    useForm({}).get(route('languages.action', { code: language.id }))
}

const articleForm = useForm({
    title: '',
    _token,
});

const newArticle = function () {
    if (articleForm.title?.length === 0) {
        articleForm.setError('title', 'Заголовок не может быть пустым.')
        return;
    }
    articleForm.post(route('languages.articles.store', { code: language.id }))
};

const languageForm = useForm({
    name: language.name,
    autonym: language.autonym,
    autonym_transcription: language.autonym_transcription,
    type: language.type,
    status: language.status,
    _token,
});

const successFlash1 = ref(null)
const successFlash2 = ref(null)

const applyForm = function () {
    if (!languageForm.name) {
        languageForm.setError('name', "Имя не может быть пустым.")
        return;
    }
    languageForm.post(route('languages.update', { code: language.id }), {
        onSuccess: () => {
            successFlash1.value?.flash()
            successFlash2.value?.flash()
        }
    });
};

watch(computed(() => languageForm.data()), _.debounce(applyForm, 2000));

const aboutForm = useForm({
    about: language.base_articles?.about ?? '',
})
const applyAbout = function () {
    aboutForm.post(route('languages.about', { code: language.id }));
}

watch(computed(() => aboutForm.data()), _.debounce(applyAbout, 2000));


const phoneticForm = useForm({
    phonetic: language.base_articles?.phonetic ?? '',
    _token,
})
const applyPhonetic = function () {
    phoneticForm.post(route('languages.phonetic.article', { code: language.id }));
}

watch(computed(() => phoneticForm.data()), _.debounce(applyPhonetic, 2000));

let typeList = [
    'Априорный',
    'Апостериорный',
    'Художественный',
    'Артланг',
    'Инженерный',
    'Энджланг',
    'Вспомогательный',
    'Ауксланг',
    'Вымышленный',
    'Персональный',
    'Персланг',
    'Философский',
    'Логический',
];

const flagForm = useForm({
    flag: null,
});

const pushFlag = function () {
    flagForm.post(route('languages.flag', { code: language.id }));
}

const flagPreview = computed(function () {
    if (flagForm.flag) {
        return URL.createObjectURL(flagForm.flag);
    }
    return null;
});

const isEdit = ref(false);

</script>

<template>
  <LanguageLayout
    :language="language"
  >
    <div v-if="!isEdit || !language.can_edit">
      <div class="flex flex-row justify-between items-center">
        <h1 class="flex flex-row gap-4 items-center text-2xl font-bold">
          <span class="flex flex-col md:flex-row gap-2">
            <span>{{ language.name }}</span>
            <span
              v-if="language.autonym"
              class="hidden md:block"
            >
              -
            </span>
            <span v-if="language.autonym">{{ language.autonym }}</span>
            <span v-if="language.autonym_transcription"> /{{ language.autonym_transcription }}/</span>
          </span>
        </h1>

        <div class="flex flex-row gap-4 items-center">
          <VSaveLoader :is-save="!languageForm.isDirty && !flagForm.isDirty && !aboutForm.isDirty" />
          <VFlashSuccess ref="successFlash1" />
          <EditButton
            v-if="language.can_edit"
            @click="isEdit = true"
          />
        </div>
      </div>

      <div class="my-4 md:mx-6 flex flex-row gap-2 items-baseline justify-between">
        <div class="flex flex-col md:flex-row gap-2">
          <span v-if="language.type">{{ language.type }}</span>
          <span v-if="language.status">{{ language.status }}</span>
        </div>
        <div class="flex flex-row gap-2 items-center">
          <span
            class="tooltip"
            :data-tip="'Обновлён ' + (new Date(language.updated_at)).toLocaleDateString()"
          >
            С нами с {{ (new Date(language.created_at)).toLocaleDateString() }}
          </span>
          <UserShort :user="language.user" />
        </div>
      </div>

      <div
        v-if="language.can_edit"
        class="my-4"
      >
        <LanguageTodoAction
          v-if="action?.message"
          :action="action"
          @click="actionAction"
        />
      </div>

      <div>
        <VMarkdownViewer
          v-if="language.base_articles?.about"
          class="card border-y p-4 rounded-2xl text-white"
          :content="language.base_articles?.about"
        />
      </div>
    </div>

    <div
      v-else
      class="flex flex-col gap-4"
    >
      <div class="mb-4 flex flex-row justify-between items-center">
        <VModal
          modal-box-class="flex flex-col gap-2 items-center"
          button-class="h-fit w-fit"
          header="Сменить флаг"
        >
          <div class="pe-4 flex flex-row gap-2 items-center rounded-full hover:bg-neutral-700">
            <LanguageFlag
              class="hover:fill-neutral-400"
              size="3rem"
              :language="language"
            />
            <span>Изменить флаг</span>
          </div>

          <template #content>
            <div class="mt-6 flex flex-col gap-4 items-end">
              <LanguageFlag
                class="self-center"
                size="20rem"
                :language="language"
                :preview="flagPreview"
              />

              <VInputFile
                v-model="flagForm.flag"
                class="w-full"
                input-class="file-input file-input-bordered rounded-e-none"
              >
                <template #button>
                  <button
                    class="btn btn-success rounded-s-none"
                    type="submit"
                    @click="pushFlag"
                  >
                    Отправить
                  </button>
                </template>
              </VInputFile>
            </div>
          </template>
        </VModal>

        <div class="flex flex-row gap-2 items-center">
          <VSaveLoader :is-save="!languageForm.isDirty && !aboutForm.isDirty && !flagForm.isDirty" />
          <VFlashSuccess ref="successFlash2" />
          <button
            class="btn btn-success btn-sm"
            @click="actionUpdate(); applyForm(); applyAbout(); isEdit = false"
          >
            Сохранить
          </button>
        </div>
      </div>

      <div class="mx-auto flex flex-col gap-2 w-full">
        <VInput
          v-model="languageForm.name"
          label="Название"
          class="w-full"
          :errors="languageForm.errors.name"
          required
        />

        <div class="grid grid-cols-2">
          <VInput
            v-model="languageForm.autonym"
            label="Аутоним"
            class="w-full"
          />
          <div class="flex flex-col">
            <VInput
              v-model="languageForm.autonym_transcription"
              label="Произношение аутонима"
              class="w-full"
            />
            <TranscriptionConverter v-model="languageForm.autonym_transcription" />
          </div>
        </div>

        <div class="grid grid-cols-2">
          <VInput
            v-model="languageForm.type"
            label="Тип"
            class="w-full"
            :list="typeList"
          />
          <div>
            <label class="label"><span class="label-text">Статус</span></label>
            <select
              v-model="languageForm.status"
              class="w-full select select-bordered"
            >
              <option :selected="languageForm.status === 'Новый'">
                Новый
              </option>
              <option :selected="languageForm.status === 'В процессе'">
                В процессе
              </option>
              <option :selected="languageForm.status === 'Завершён'">
                Завершён
              </option>
            </select>
          </div>
        </div>

        <VMarkdownEditor
          v-model="aboutForm.about"
          :language="language"
          label="О языке"
          class="mt-2 w-full"
        />
      </div>
    </div>

    <VModal
      v-if="action?.modal === 'autonym'"
      ref="autonymModal"
      without-button
      header="Аутоним"
      @close="actionUpdate"
    >
      <template #content>
        <article>
          Аутоним — самоназвание вашего языка.
          Вы можете использовать юникод, но страйтесь использовать кириллицу или латиницу для записи,
          но это не обязательно.
        </article>
        <div>
          <VInput
            v-model="languageForm.autonym"
            class="grow"
            input-class="rounded-e-none"
            @keyup.enter="applyForm(); autonymModal.close()"
          >
            <template #button>
              <button
                class="btn btn-success rounded-s-none"
                @click="applyForm(); autonymModal.close()"
              >
                Изменить
              </button>
            </template>
          </VInput>
        </div>
      </template>
    </VModal>

    <VModal
      v-if="action?.modal === 'autonym_transcription'"
      ref="autonymTranscriptionModal"
      without-button
      header="Произношение аутонима"
      @close="actionUpdate"
    >
      <template #content>
        <article>
          Ваша орфография может быть неочевидна другим людям,
          поэтому укажите, пожалуйста, как ваш аутоним произносится.
          Для этого вы можете использовать X-SAMPA, который удобно набирать на клавиатуре.
          Просто введите его в поле и нажмите кнопку конвертации.
        </article>
        <div class="flex flex-col">
          <VInput
            v-model="languageForm.autonym_transcription"
            class="grow"
            input-class="rounded-e-none"
            @keyup.enter="applyForm(); autonymTranscriptionModal.close()"
          >
            <template #button>
              <button
                class="btn btn-success rounded-s-none"
                @click="applyForm(); autonymTranscriptionModal.close()"
              >
                Изменить
              </button>
            </template>
          </VInput>
          <TranscriptionConverter v-model="languageForm.autonym_transcription"/>
        </div>
      </template>
    </VModal>

    <VModal
      v-if="action?.modal === 'type'"
      ref="typeModal"
      without-button
      header="Тип конланга"
      @close="actionUpdate"
    >
      <template #content>
        <article>
          В данном поле вы можете указать что угодно, это ваше видение конланга.
          Но для вас есть небольшой список для помощи.
        </article>
        <div class="flex flex-col">
          <VInput
            v-model="languageForm.type"
            class="grow"
            input-class="rounded-e-none"
            :list="typeList"
            @keyup.enter="applyForm(); typeModal.close()"
          >
            <template #button>
              <button
                class="btn btn-success rounded-s-none"
                @click="applyForm(); typeModal.close()"
              >
                Изменить
              </button>
            </template>
          </VInput>
        </div>
      </template>
    </VModal>

    <VModal
      v-if="action?.modal === 'about'"
      ref="aboutModal"
      without-button
      header="О языке"
      @close="actionUpdate"
    >
      <template #postHeader>
        <button
          class="btn btn-sm btn-success self-end w-fit"
          @click="actionUpdate(); applyAbout()"
        >
          Сохранить
        </button>
        <VSaveLoader :is-save="!aboutForm.isDirty" />
      </template>

      <template #content>
        <article>
          Здесь вы можете сообщить о вашем языке что желаете.
          Можете сообщить о носителях. Об истории. О строе. О своих мыслях.
          Указать типологию вашего языка. С примерами на вашем языке.
          И любое что может заинтересовать других людей.
        </article>
        <div class="mt-4 flex flex-col">
          <VMarkdownEditor
            v-model="aboutForm.about"
            :language="language"
          />
        </div>
      </template>
    </VModal>

    <VModal
      v-if="action?.modal === 'phonetic'"
      ref="phoneticModal"
      without-button
      header="О фонетике языка"
      @close="actionUpdate"
    >
      <template #postHeader>
        <button
          class="btn btn-sm btn-success self-end w-fit"
          @click="actionUpdate(); applyPhonetic()"
        >
          Сохранить
        </button>
        <VSaveLoader :is-save="!phoneticForm.isDirty" />
      </template>

      <template #content>
        <div class="mt-4 flex flex-col">
          <VMarkdownEditor
            v-model="phoneticForm.phonetic"
            :language="language"
          />
        </div>
      </template>
    </VModal>

    <VModal
      v-if="action?.modal === 'article'"
      ref="articleModal"
      without-button
      class="w-full max-w-full md:max-w-screen-md"
      header="Добавить статью"
      @close="actionUpdate"
    >
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
  </LanguageLayout>
</template>

<style scoped>

</style>
