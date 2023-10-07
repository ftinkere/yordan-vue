<script setup>
import LanguageLayout from "@/Layouts/LanguageLayout.vue";
import EditButton from "@/Components/EditButton.vue";
import { computed, ref } from "vue";
import VSaveLoader from "@/Components/VSaveLoader.vue";
import VSortable from "@/Components/VSortable.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import VModal from "@/Components/VModal.vue";
import VInput from "@/Components/VInput.vue";
import route from "ziggy-js";
import VCheckbox from "@/Components/VCheckbox.vue";
import _ from "lodash";
import LanguageTodoAction from "@/Components/LanguageTodoAction.vue";
import VFlashSuccess from "@/Components/VFlashSuccess.vue";
import SoundSelector from "@/Components/SoundSelector.vue";

const { language, editMode } = defineProps({
    language: {
        type: Object,
        required: true,
    },
    editMode: {
        type: Boolean,
        default: false,
    }
})

const _token = usePage().props.csrf_token
const isEdit = ref(editMode)


// General
const orthographemeFlash = ref(null)

// Add
const addOrthographemeForm = useForm({
    lowercase: '',
    uppercase: '',
    is_alphabet: true,
    _token
})

const addOrthographemeModal = ref(null)
const addOrthographemeFlash = ref(null)

const applyAddOrthographemeForm = function () {
    addOrthographemeForm.post(route('languages.orthography.store', language.id), {
        onSuccess: () => {
            addOrthographemeForm.reset()
            addOrthographemeFlash.value.flash()
        },
    })
}

// Edit
const editOrthographemeModal = ref(null)

const editOrthographemeForm = useForm({
    id: 0,
    lowercase: '',
    uppercase: '',
    is_alphabet: false,
    _token
})

const openEditOrthographemeModal = function (orthographeme) {
    if (!language.can_edit || !isEdit.value) {
        return ;
    }

    editOrthographemeForm.id = orthographeme.id
    editOrthographemeForm.lowercase = orthographeme.lowercase
    editOrthographemeForm.uppercase = orthographeme.uppercase
    editOrthographemeForm.is_alphabet = !!orthographeme.is_alphabet

    editOrthographemeModal.value.open()
}

const applyOrthographemeForm = function() {
    editOrthographemeForm.post(route('languages.orthography.update', [language.id, editOrthographemeForm.id]))
}

// Delete

const deleteOrthographemeForm = useForm({
    _token
})

const deleteOrthographeme = function() {
    deleteOrthographemeForm.delete(route('languages.orthography.delete', [language.id, editOrthographemeForm.id]), {
        onSuccess: () => {
            orthographemeFlash.value.flash()
            editOrthographemeModal.value.close()
        }
    })
}

// Sort
const sortableForm = useForm({
    order: null
})

const changeSortable = function(sortable) {
    sortableForm.order = 1
    _.debounce(() => {
        sortableForm.transform(data => {
            data.order = sortable.toArray()
            return data
        }).post(route('languages.orthography.order', language.id), {
            onSuccess: () => sortableForm.reset()
        })
    }, 2000)()
}

// Edit pronunciation part

const editPronunciationModal = ref(null)
const editPronunciationFlash = ref(null)

// Delete pronunciation

const deletePronunciationForm = useForm({
    id: null,
    orthographeme_id: null,
    _token
})

const deletePronunciation = function () {
    deletePronunciationForm.delete(route('languages.orthography.pronunciations.delete', [language.id, deletePronunciationForm.orthographeme_id, deletePronunciationForm.id]), {
        onSuccess: () => editPronunciationModal.value.close(),
    })
}

// Edit pronunciation

const editPronunciationForm = useForm({
    id: null,
    orthographeme_id: null,
    pronunciation: null,
    context: null,
    _token
})

const applyEditPronunciation = function () {
    editPronunciationForm.post(route('languages.orthography.pronunciations.update', [language.id, editPronunciationForm.orthographeme_id, editPronunciationForm.id]), {
        onSuccess: () => editPronunciationFlash.value.flash(),
    })
}

const openEditPronunciationModal = function (pronunciation) {
    if (!language.can_edit || !isEdit.value) {
        return ;
    }

    editPronunciationForm.id = pronunciation.id
    editPronunciationForm.orthographeme_id = pronunciation.orthographeme_id
    editPronunciationForm.pronunciation = pronunciation.pronunciation
    editPronunciationForm.context = pronunciation.context

    deletePronunciationForm.id = pronunciation.id
    deletePronunciationForm.orthographeme_id = pronunciation.orthographeme_id

    editPronunciationModal.value.open()
}

// Add pronunciation

const addPronunciationModal = ref(null)

const addPronunciationForm = useForm({
    orthographeme_id: null,
    pronunciation: null,
    context: null,
    _token
})

const applyAddPronunciation = function () {
    addPronunciationForm.post(route('languages.orthography.pronunciations.store', [language.id, addPronunciationForm.orthographeme_id]), {
        onSuccess: () => {
            addPronunciationModal.value.close()
            addPronunciationForm.reset()
        },
    })
}

const openAddPronunciationModal = function (orthographeme) {
    if (!language.can_edit || !isEdit.value) {
        return ;
    }

    addPronunciationForm.orthographeme_id = orthographeme.id

    addPronunciationModal.value.open()
}

</script>

<template>
  <LanguageLayout :language="language">
    <!-- Buttons -->
    <div
      v-if="language.can_edit"
      class="flex flex-row justify-between"
    >
      <span />

      <EditButton
        v-if="!isEdit"
        @click="isEdit = true"
      />
      <div
        v-else
        class="flex flex-row flex-wrap items-center gap-2"
      >
        <!-- Edit mode -->
        <VSaveLoader :is-save="!sortableForm.isDirty" />
        <VFlashSuccess ref="orthographemeFlash" />

        <button
          class="btn btn-sm btn-success"
          @click="isEdit = false"
        >
          Сохранить
        </button>
      </div>
    </div>
    <!-- End buttons -->

    <!-- Alert if no one orthographemes to make it -->
    <LanguageTodoAction
      v-if="language.can_edit && language.orthographemes.length === 0"
      :action="{ message: 'Добавьте орфографемы' }"
      class="mt-4"
      @click="addOrthographemeModal.open()"
    />

    <!-- Orthographemes -->
    <VSortable
      class="mt-6 sm:mx-10 md:mx-20 flex flex-row flex-wrap gap-y-2 items-center"
      draggable=".item"
      handle=".handle"
      :delay_="100"
      delay-on-touch-only
      @change="changeSortable"
    >
      <div
        v-for="orthographeme in language.orthographemes"
        :key="orthographeme.id"
        :data-id="orthographeme.id"
        class="item p-2 inline-flex items-center gap-2 border border-neutral-500 rounded"
        :class="{
          'border-b-neutral-900 border-b-2 dark:border-b-neutral-200': orthographeme.is_alphabet,
          'cursor-default': !language.can_edit || !isEdit,
          'cursor-pointer': language.can_edit && isEdit,
        }"
        @click="openEditOrthographemeModal(orthographeme)"
      >
        {{ (orthographeme.uppercase ?? '') + ' ' + orthographeme.lowercase }}
        <span
          v-if="isEdit"
          class="handle fill-white cursor-grab"
          @click.stop
        >
          <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 128 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M64 360a56 56 0 1 0 0 112 56 56 0 1 0 0-112zm0-160a56 56 0 1 0 0 112 56 56 0 1 0 0-112zM120 96A56 56 0 1 0 8 96a56 56 0 1 0 112 0z"/></svg>
        </span>
      </div>

      <button
        v-if="language.can_edit && isEdit"
        class="fill-white ms-4 p-2 btn btn-sm btn-success"
        @click="addOrthographemeModal.open()"
      >
        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H192V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H400c17.7 0 32-14.3 32-32s-14.3-32-32-32H256V80z"/></svg>
      </button>
    </VSortable>
    <!-- End orthographemes -->

    <!-- Table of pronunciations -->
    <div v-if="language.orthographemes.length !== 0">
      <table class="mx-auto mt-6 table w-fit text-center border-neutral-500">
        <thead>
          <tr class="text-white">
            <th>Орфографема</th>
            <th>Произношение</th>
            <th>При каких условиях</th>
          </tr>
        </thead>
        <tbody>
          <!-- Rows for one orthographeme -->
          <template
            v-for="orthographeme in language.orthographemes"
            :key="orthographeme.id"
          >
            <!-- Rows in block orthographeme with individual pronunciation -->
            <tr
              v-for="(pronunciation, i) in orthographeme.pronunciations"
              :key="pronunciation.id"
            >
              <!-- Letters for all rows in block -->
              <td
                v-if="i === 0"
                class="border border-s-0 border-neutral-500"
                :rowspan="orthographeme.pronunciations.length + (isEdit ? 1 : 0)"
              >
                {{ orthographeme.uppercase ?? '' }} {{ orthographeme.lowercase }}
              </td>
              <!-- End letters for all rows in block -->


              <td
                class="border border-neutral-500 group text-center"
                :class="{ 'cursor-pointer hover:bg-neutral-800': isEdit }"
                @click="openEditPronunciationModal(pronunciation)"
              >
                [{{ pronunciation.language_sound.sound.sound }}]
                <template v-if="pronunciation.language_sound.allophone_of !== null">
                  /{{ pronunciation.language_sound.allophone.sound.sound }}/
                </template>
              </td>
              <td class="border border-e-0 border-neutral-500">
                <div class="items-baseline flex flex-row flex-wrap gap-4">
                  <article class="font-mono text-left whitespace-pre-wrap">
                    {{ pronunciation.context }}
                  </article>
                </div>
              </td>
            </tr>
            <!-- End rows in block orthographeme with individual pronunciation -->

            <tr v-if="isEdit">
              <td
                v-if="orthographeme.pronunciations.length === 0"
                class="border border-s-0 border-neutral-500"
              >
                {{ orthographeme.uppercase ?? '' }} {{ orthographeme.lowercase }}
              </td>

              <td class="border border-neutral-500">
                <button
                  class="btn btn-sm btn-primary"
                  @click="openAddPronunciationModal(orthographeme)"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H192V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H400c17.7 0 32-14.3 32-32s-14.3-32-32-32H256V80z"/></svg>
                </button>
              </td>

              <td class="border-0" />
            </tr>
          </template>
          <!-- End rows for one orthographeme -->
        </tbody>
      </table>
    </div>
    <!-- End table of pronunciations -->

    <!-- # Modals -->
    <!-- ## Add modal -->
    <VModal
      ref="addOrthographemeModal"
      without-button
      header="Добавить орфографему"
      class="max-w-screen-md"
    >
      <template #postHeader>
        <button
          class="btn btn-sm btn-success"
          @click="applyAddOrthographemeForm"
        >
          Сохранить
        </button>

        <VSaveLoader :is-save="!addOrthographemeForm.processing" />
        <VFlashSuccess ref="addOrthographemeFlash" />
      </template>

      <template #content>
        <div class="flex flex-col gap-2">
          <div class="flex flex-col sm:grid sm:grid-cols-2">
            <VInput
              v-model="addOrthographemeForm.uppercase"
              label="Заглавная орфографема"
            />
            <VInput
              v-model="addOrthographemeForm.lowercase"
              label="Малая орфографема"
              :errors="addOrthographemeForm.errors.lowercase"
              required
            />
          </div>

          <VCheckbox
            v-model="addOrthographemeForm.is_alphabet"
            class="w-fit"
          >
            Входит в алфавит?
          </VCheckbox>

          <div class="mt-6 flex flex-row flex-wrap items-center justify-between">
            <span />

            <button
              class="btn btn-sm btn-success"
              @click="applyAddOrthographemeForm"
            >
              Сохранить
            </button>
          </div>
        </div>
      </template>
    </VModal>
    <!-- ## End add modal -->

    <!-- ## Edit orthographeme modal -->
    <VModal
      v-if="isEdit"
      ref="editOrthographemeModal"
      without-button
      header="Изменить орфографему"
      class="max-w-screen-md"
    >
      <template #postHeader>
        <button
          class="btn btn-sm btn-success"
          @click="applyOrthographemeForm"
        >
          Сохранить
        </button>

        <VSaveLoader :is-save="!editOrthographemeForm.processing" />
      </template>

      <template #content>
        <div class="flex flex-col gap-2">
          <div class="flex flex-col sm:grid sm:grid-cols-2">
            <VInput
              v-model="editOrthographemeForm.uppercase"
              label="Заглавная орфографема"
            />
            <VInput
              v-model="editOrthographemeForm.lowercase"
              label="Малая орфографема"
              :errors="editOrthographemeForm.errors.lowercase"
              required
            />
          </div>

          <VCheckbox
            v-model="editOrthographemeForm.is_alphabet"
            class="w-fit"
          >
            Входит в алфавит?
          </VCheckbox>

          <div class="mt-6 flex flex-row flex-wrap items-center justify-between">
            <div class="inline-flex flex-row items-center">
              <button
                class="btn btn-sm btn-error"
                @click="deleteOrthographeme"
              >
                Удалить
              </button>
              <VSaveLoader :is-save="!deleteOrthographemeForm.processing" />
            </div>

            <button
              class="btn btn-sm btn-success"
              @click="applyOrthographemeForm"
            >
              Сохранить
            </button>
          </div>
        </div>
      </template>
    </VModal>
    <!-- ## End edit orthographeme modal -->


    <!-- ## Edit pronunciation modal -->
    <VModal
      v-if="isEdit"
      ref="editPronunciationModal"
      without-button
      header="Изменить произношение"
      class="max-w-screen-md"
    >
      <template #postHeader>
        <button
          class="btn btn-sm btn-success"
          @click="applyEditPronunciation"
        >
          Сохранить
        </button>

        <VSaveLoader :is-save="!editPronunciationModal?.processing" />
        <VFlashSuccess ref="editPronunciationFlash" />
      </template>

      <template #content>
        <div class="flex flex-col gap-2">
          <!-- Form -->
          <SoundSelector
            v-model="editPronunciationForm.pronunciation"
            :language="language"
            label="Произношение"
            no-selected="Не указано"
            required
          />

          <VInput
            v-model="editPronunciationForm.context"
            type="textarea"
            label="Условия"
            class=""
            input-class="h-48"
          />
          <!-- End form -->
          <!-- Buttons -->
          <div class="mt-6 flex flex-row flex-wrap items-center justify-between">
            <div class="inline-flex flex-row items-center">
              <button
                class="btn btn-sm btn-error"
                @click="deletePronunciation"
              >
                Удалить
              </button>
              <VSaveLoader :is-save="!deletePronunciationForm.processing" />
            </div>

            <button
              class="btn btn-sm btn-success"
              @click="applyEditPronunciation"
            >
              Сохранить
            </button>
          </div>
        <!-- End buttons -->
        </div>
      </template>
    </VModal>
    <!-- ## End edit pronunciation modal -->

    <!-- ## Add pronunciation modal -->
    <VModal
      v-if="isEdit"
      ref="addPronunciationModal"
      without-button
      header="Добавить произношение"
      class="max-w-screen-md"
    >
      <template #postHeader>
        <button
          class="btn btn-sm btn-success"
          @click="applyAddPronunciation"
        >
          Сохранить
        </button>

        <VSaveLoader :is-save="!addPronunciationModal?.processing" />
      </template>

      <template #content>
        <div class="flex flex-col gap-2">
          <!-- Form -->
          <SoundSelector
            v-model="addPronunciationForm.pronunciation"
            :language="language"
            label="Произношение"
            no-selected="Не указано"
            required
          />

          <VInput
            v-model="addPronunciationForm.context"
            type="textarea"
            label="Условия"
            input-class="h-48"
          />
          <!-- End form -->
          <!-- Buttons -->
          <div class="mt-6 flex flex-row flex-wrap items-center justify-between">
            <span />

            <button
              class="btn btn-sm btn-success"
              @click="applyAddPronunciation"
            >
              Сохранить
            </button>
          </div>
        <!-- End buttons -->
        </div>
      </template>
    </VModal>
    <!-- ## End add pronunciation modal -->

  <!-- End modals -->
  </LanguageLayout>
</template>

<style scoped>

</style>