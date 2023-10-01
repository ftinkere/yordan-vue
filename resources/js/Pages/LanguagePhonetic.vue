<script setup>
import LanguageLayout from "@/Layouts/LanguageLayout.vue";
import { computed, ref, toRef, watch } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import VModal from "@/Components/VModal.vue";
import SoundSelector from "@/Components/SoundSelector.vue";
import EditButton from "@/Components/EditButton.vue";
import route from "ziggy-js";
import { router } from '@inertiajs/vue3'
import VMarkdownViewer from "@/Components/VMarkdownViewer.vue";
import VMarkdownEditor from "@/Components/VMarkdownEditor.vue";
import VSaveLoader from "@/Components/VSaveLoader.vue";
import LanguageTodoAction from "@/Components/LanguageTodoAction.vue";
import _ from "lodash";
import VInput from "@/Components/VInput.vue";
import VCheckbox from "@/Components/VCheckbox.vue";
import VFlashSuccess from "@/Components/VFlashSuccess.vue";
import SoundString from "@/Components/SoundString.vue";

const { language, sounds, tables, ...props } = defineProps({
    language: {
        type: Object,
        required: true
    },
    tables: {
        type: Array,
        required: true,
    },
    sounds: {
        type: [Array, Object],
        required: true,
    },
    mode: {
        type: String,
        default: 'view',
    }
})

// let tables = toRef(props.tables)

const _token = usePage().props.csrf_token

// const mode = ref((new URLSearchParams(window.location.search)).get('mode') ?? 'view') // view, edit, add
const mode = ref(props.mode) // view, edit, add

const articleEditModal = ref(null)
const editModal = ref(null)

const lsounds = computed(() => {
    return sounds.filter(s => s?.language_sounds?.filter(ls => ls?.language_id  === language.id)?.length > 0)
})

const lsound = ref(null)
const tdEditForm = useForm({
    allophone_of: 0,
    _token,
})

const applyEditForm = function () {
    if (lsound.value) {
        tdEditForm.transform(data => {
            if (data.allophone_of == -1) {
                data.allophone_of = null
            }
            return data
        }).post(route('languages.phonetic.edit', [ language.id, lsound.value.id ]))
    }
}

const toggleSound = function (td) {
    // useForm({ sound_id: td.sound?.sound_id ?? td.sound?.id }).post(route('languages.phonetic.toggle-sound', [language.id]))
    fetch(route('languages.phonetic.toggle-sound', [language.id]), {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ sound_id: td.sound?.sound_id ?? td.sound?.id, _token }),
    }).then(resp => resp.json())
        .then(data => td.language_has = data.language_has)
}

const openEditModal = function (td) {
    if (!td.data) {
        return
    }

    if (mode.value === 'edit') {
        lsound.value = td.sound

        tdEditForm.allophone_of = td.sound?.allophone_of

        editModal.value.open()
    } else if (mode.value === 'add') {
        toggleSound(td)
    }
}


const phoneticForm = useForm({
    phonetic: language.base_articles?.phonetic ?? '',
    _token,
})

const applyPhonetic = function () {
    phoneticForm.post(route('languages.phonetic.article', { code: language.id }));
}

watch(computed(() => phoneticForm.data()), _.debounce(applyPhonetic, 2000));


const addSoundForm = useForm({
    sound: '',
    table: '',
    row: '',
    column: '',
    sub_column: '',
})
const isSubColumn = ref(false)

const flashSuccess = ref(null)

const applyAddSoundForm = function () {
    addSoundForm.transform(data => {
        if (!isSubColumn.value) {
            data.sub_column = null
        }
        return data
    }).post(route('languages.phonetic.sounds.store', { code: language.id }), {
        onSuccess: () => flashSuccess.value.flash()
    })
}

const deleteSoundForm = useForm({})

const deleteSound = function (sound) {
    deleteSoundForm.delete(route('languages.phonetic.sounds.delete', { code: language.id, sound: sound.id }))
}

</script>

<template>
  <LanguageLayout :language="language">
    <!-- Buttons -->
    <div
      v-if="language.can_edit"
      class="mb-2 flex flex-row justify-between items-center"
    >
      <span />

      <!-- Right side -->
      <div>
        <div
          v-if="mode === 'view'"
          class="flex flex-row flex-wrap gap-4"
        >
          <button
            class="btn btn-sm btn-info"
            @click="router.get(route('languages.phonetic', { code: language.id, mode: 'add' }))"
          >
            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 384 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M224 32c0-17.7-14.3-32-32-32s-32 14.3-32 32V144H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H160V320c0 17.7 14.3 32 32 32s32-14.3 32-32V208H336c17.7 0 32-14.3 32-32s-14.3-32-32-32H224V32zM0 480c0 17.7 14.3 32 32 32H352c17.7 0 32-14.3 32-32s-14.3-32-32-32H32c-17.7 0-32 14.3-32 32z"/></svg>
          </button>
          <EditButton
            @click="router.get(route('languages.phonetic', { code: language.id, mode: 'edit' }))"
          />
        </div>

        <button
          v-else
          class="btn btn-sm btn-success"
          @click="router.get(route('languages.phonetic', { code: language.id, mode: 'view' }))"
        >
          Сохранить
        </button>
      </div>
    <!--  End right side -->
    </div>
    <!-- End of buttons -->
    <!-- Tables  -->
    <div class="flex flex-row flex-wrap gap-8">
      <!--  Table  -->
      <div
        v-for="table in tables"
        :key="table.name"
        class="overflow-x-auto grow"
      >
        <table class="table table-xs table-hover w-fit">
          <thead>
            <tr>
              <td
                v-for="el in table.rows[0]"
                :key="el.key"
                :is="el.header ? 'th' : 'td'"
                :colspan="el.colspan ?? 1"
                class="text-center text-white whitespace-nowrap w-fit"
              >
                {{ el.data }}
              </td>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(row, i) in table.rows.slice(1)"
              :key="i"
            >
              <td
                v-for="el in row"
                :key="el.key"
                :is="el.header ? 'th' : 'td'"
                :colspan="el.colspan ?? 1"
                :class="{
                  'text-center border border-neutral-700 whitespace-nowrap w-fit': !el.header,
                  'text-end whitespace-nowrap w-fit': el.header,
                  'cursor-pointer hover:bg-neutral-800': el.data && mode === 'edit',
                  'cursor-pointer': el.data && mode === 'add' && !el.header,
                  'bg-green-700 hover:bg-orange-800': el.language_has && mode === 'add' && !el.header,
                  'hover:bg-green-800': el.data && !el.language_has && mode === 'add' && !el.header,
                  'tooltip': el.sound?.allophone
                }"
                :data-tip="'Это аллофон звука /' + el.sound?.allophone?.sound?.sound + '/'"
                @click="openEditModal(el)"
              >
                <span>
                  {{ el.data }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <!-- End table -->
    </div>
    <!-- End tables -->

    <!-- Adding custom sounds -->
    <div
      v-if="mode === 'add'"
      class="mt-6 flex flex-col gap-4"
    >
      <article class="alert">
        Вы можете добавить свои звуки в язык. Для этого требуется указать сам звук, название таблицы (которое пишется в первой ячейке), название строки, столбца и под-столбца.<br/>
        Если вы укажете "несуществующие" данные, то это просто создаст новую сущность.<br/>
        Доступные подстолбцы для таблицы 'consonants': 'Voiced' и 'Voiceless'. Для 'vowels': 'Unrounded' и 'Rounded'.<br/>
        Создание подстолбца в таблице создаёт его для всех столбцов, поэтому если вам нужно добавить один-два звука порой лучше использовать столбец.<br/>
        Если подстолбец оставить пустым, то он объединит все ячейки и будет один на столбец. Не советую делать при этом другие звуки в подстолбцах с пустым в столбце<br/>
        Пока доступен только такой способ добавления, если вам нужно много таких звуков, просто простите.<br/>
        Вы можете добавлять в существующую таблицу, но не советую указывать данные уже существующих звуков.<br/>
        Порядок строк и столбцов автоматический, к сожалению. Что встретится первее, то первее и будет показано<br/>
      </article>

      <div class="flex flex-row flex-wrap gap-4">
        <!-- Modal adding sound -->
        <VModal
          header="Добавить свой звук"
          button-class="btn btn-sm btn-success"
          class="max-w-screen-sm"
        >
          Добавить свой звук

          <template #postHeader>
            <button
              class="btn btn-sm btn-success"
              @click="applyAddSoundForm"
            >
              Добавить
            </button>
            <VSaveLoader :is-save="!addSoundForm.processing" />
            <VFlashSuccess ref="flashSuccess" />
          </template>

          <template #content>
            <div class="flex flex-col gap-2">
              <article class="alert">
                Окно безопасно закрывать, введённые данные не сбросятся
              </article>

              <VInput
                v-model="addSoundForm.sound"
                label="Звук"
                :errors="addSoundForm.errors.sound"
                required
              />
              <VInput
                v-model="addSoundForm.table"
                label="Таблица"
                :errors="addSoundForm.errors.table"
                required
              />
              <VInput
                v-model="addSoundForm.row"
                label="Строка"
                :errors="addSoundForm.errors.row"
                required
              />
              <VInput
                v-model="addSoundForm.column"
                label="Колонка"
                :errors="addSoundForm.errors.column"
                required
              />
              <VCheckbox
                v-model="isSubColumn"
                class="w-fit"
              >
                Добавить в подколонку?
              </VCheckbox>
              <VInput
                v-if="isSubColumn"
                v-model="addSoundForm.sub_column"
                label="Подколонка"
                :errors="addSoundForm.errors.sub_column"
              />

              <button
                class="btn btn-sm btn-success w-fit"
                @click="applyAddSoundForm"
              >
                Добавить
              </button>
            </div>
          </template>
        </VModal>
        <!-- End modal adding sound -->

        <VModal
          button-class="btn btn-sm btn-warning"
          header="Список добавленных звуков"
        >
          Список добавленных звуков

          <template #postHeader>
            <VSaveLoader :is-save="!deleteSoundForm.processing" />
          </template>

          <template #content>
            <div class="flex flex-col gap-2">
              <div
                v-for="sound in sounds.filter(el => el.language_id !== null)"
                :key="sound.id"
                class="w-fit"
              >
                <button
                  class="ms-2 btn btn-xs btn-error float-right border-neutral-800"
                  @click="deleteSound(sound)"
                >
                  Удалить
                </button>
                <SoundString :sound="sound" />
              </div>
            </div>
          </template>
        </VModal>
      </div>
    </div>
    <!-- End adding custom sounds -->

    <!-- Article -->
    <div class="mt-4 p-4 card border-t">
      <div
        v-if="language.can_edit"
        class="flex flex-row justify-between items-center"
      >
        <span />

        <VModal
          ref="articleEditModal"
          :without-button="!language.base_articles?.phonetic || mode !== 'edit'"
          header="Изменить статью"
          button-class="btn btn-sm btn-warning"
        >
          <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"/></svg>

          <template #postHeader>
            <button class="btn btn-sm btn-success">
              Сохранить
            </button>

            <VSaveLoader :is-save="!phoneticForm.isDirty" />
          </template>

          <template #content>
            <VMarkdownEditor
              v-model="phoneticForm.phonetic"
              :language="language"
            />
          </template>
        </VModal>
      </div>

      <VMarkdownViewer
        v-if="language.base_articles?.phonetic"
        :content="language.base_articles?.phonetic"
      />
      <LanguageTodoAction
        v-else-if="language.can_edit && mode !== 'add'"
        :action="{ message: 'Напишите статью про устройство фонетики в вашем языке', button: 'Написать' }"
        @click="articleEditModal?.open()"
      />
    </div>
    <!-- End article -->

    <!-- Allophone selector modal -->
    <VModal
      ref="editModal"
      without-button
      :header="'Изменить звук \'' + lsound?.sound.sub_column + ' ' + lsound?.sound.column + ' ' + lsound?.sound.row + ': ' + lsound?.sound.sound + '\''"
      class="max-w-screen-sm"
    >
      <template #content>
        <SoundSelector
          v-model="tdEditForm.allophone_of"
          :sounds="lsounds"
          :language="language"
          @change="applyEditForm"
        />
      </template>
    </VModal>
  </LanguageLayout>
</template>

<style scoped>

</style>