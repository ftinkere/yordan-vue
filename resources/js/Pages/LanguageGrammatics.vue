<script setup>
import LanguageLayout from "@/Layouts/LanguageLayout.vue";
import VSaveLoader from "@/Components/VSaveLoader.vue";
import EditButton from "@/Components/EditButton.vue";
import VFlashSuccess from "@/Components/VFlashSuccess.vue";
import { nextTick, ref } from "vue";
import VMarkdownViewer from "@/Components/VMarkdownViewer.vue";
import VModal from "@/Components/VModal.vue";
import VInput from "@/Components/VInput.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import route from "ziggy-js";
import VMarkdownEditor from "@/Components/VMarkdownEditor.vue";
import LanguageTodoAction from "@/Components/LanguageTodoAction.vue";

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

// Edit grammatic part
const editGrammaticFlash = ref(null)
const editGrammaticModal = ref(null)
const editUpdate = ref(true)

// Delete grammatic

const deleteGrammaticForm = useForm({
    id: null,
    _token,
})

const deleteGrammatic = function () {
    deleteGrammaticForm.delete(route('languages.grammatics.delete', [language.id, deleteGrammaticForm.id]), {
        onSuccess: () => editGrammaticModal.value?.close(),
    })
}

// Edit grammatic

const editGrammaticForm = useForm({
    id: null,
    name: null,
    article: null,
    _token,
})

const applyEditGrammatic = function () {
    editGrammaticForm.post(route('languages.grammatics.update', [language.id, editGrammaticForm.id]), {
        onSuccess: () => editGrammaticFlash.value?.flash(),
    })
}



const openEditGrammaticModal = function (grammatic) {
    if (!language.can_edit || !isEdit.value) {
        return ;
    }

    editGrammaticForm.id = grammatic.id
    editGrammaticForm.name = grammatic.name
    editGrammaticForm.article = grammatic.article

    deleteGrammaticForm.id = grammatic.id

    editGrammaticForm.clearErrors()

    editUpdate.value = false

    nextTick(() => {
        editUpdate.value = true

        nextTick(() => {
            editGrammaticModal.value?.open()
        })
    })
}

// Add grammatic value
const addGrammaticModal = ref(null)
const addGrammaticUpdate = ref(true)

const addGrammaticForm = useForm({
    name: null,
    article: null,
})

const addGrammatic = function () {
    addGrammaticForm.post(route('languages.grammatics.store', language.id), {
        onSuccess: () => {
            addGrammaticModal.value?.close()
            addGrammaticForm.reset()
            addGrammaticForm.clearErrors()

            addGrammaticUpdate.value = false

            nextTick(() => {
                addGrammaticUpdate.value = true
            })
        },
    })
}


// Edit value part
const editValueFlash = ref(null)
const editValueModal = ref(null)
const editValueUpdate = ref(true)

// Delete grammatic value

const deleteValueForm = useForm({
    id: null,
    grammatic_id: null,
    _token,
})

const deleteValue = function () {
    deleteValueForm.delete(route('languages.grammatics.values.delete', [language.id, deleteValueForm.grammatic_id, deleteValueForm.id]), {
        onSuccess: () => editValueModal.value?.close(),
    })
}

// Edit grammatic value

const editValueForm = useForm({
    id: null,
    grammatic_id: null,
    value: null,
    short: null,
    article: null,
})

const applyEditValue = function () {
    editValueForm.post(route('languages.grammatics.values.update', [language.id, editValueForm.grammatic_id, editValueForm.id]), {
        onSuccess: () => editValueFlash.value?.flash(),
    })
}

const openEditValueModal = function (grammatic, value) {
    if (!language.can_edit || !isEdit.value) {
        return ;
    }

    editValueForm.id = value.id
    editValueForm.grammatic_id = grammatic.id
    editValueForm.value = value.value
    editValueForm.short = value.short
    editValueForm.article = value.article

    deleteValueForm.id = value.id
    deleteValueForm.grammatic_id = grammatic.id

    editValueForm.clearErrors()

    editValueUpdate.value = false

    nextTick(() => {
        editValueUpdate.value = true

        nextTick(() => {
            editValueModal.value?.open()
        })
    })
}

// Add grammatic value
const addValueModal = ref(null)
const addValueUpdate = ref(true)

const addValueForm = useForm({
    grammatic_id: null,
    value: null,
    short: null,
    article: null,
})

const openAddValueModal = function (grammatic) {
    addValueForm.grammatic_id = grammatic.id

    addValueModal.value?.open()
}

const addValue = function () {
    addValueForm.post(route('languages.grammatics.values.store', [language.id, addValueForm.grammatic_id]), {
        onSuccess: () => {
            addValueModal.value?.close()
            addValueForm.reset()
            addValueForm.clearErrors()

            addValueUpdate.value = false

            nextTick(() => {
                addValueUpdate.value = true
            })
        },
    })
}

</script>

<template>
  <LanguageLayout :language="language">
    <!-- Buttons -->
    <div
      v-if="language.can_edit"
      class="flex flex-row justify-between"
    >
      <div v-if="isEdit">
        <button
          class="btn btn-sm btn-primary"
          @click="addGrammaticModal?.open()"
        >
          <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H192V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H400c17.7 0 32-14.3 32-32s-14.3-32-32-32H256V80z"/></svg>
        </button>
      </div>
      <span v-else />

      <EditButton
        v-if="!isEdit"
        @click="isEdit = true"
      />
      <div
        v-else
        class="flex flex-row flex-wrap items-center gap-2"
      >
        <!-- Edit mode -->
        <VSaveLoader :is-save="true" />

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
      v-if="language.grammatics.length === 0"
      :action="{ message: 'Добавьте первую грамматическую категорию.' }"
      class="mt-4"
      @click="addGrammaticModal?.open()"
    />

    <!-- Article about grammatic -->
    <VMarkdownViewer
      :content="language.base_articles?.grammatic"
      class="mt-4"
    />

    <!-- Container of cards of grammatic -->
    <div class="mt-4">
      <!-- Grammatic card -->
      <div
        v-for="grammatic in language.grammatics"
        :key="grammatic.id"
        class="p-4 card card-bordered border-neutral-500 border-x-0"
      >
        <div class="flex flex-row flex-wrap justify-between items-center">
          <h3 class="card-title">
            {{ grammatic.name }}
          </h3>

          <EditButton
            v-if="isEdit"
            @click="openEditGrammaticModal(grammatic)"
          />
        </div>

        <VMarkdownViewer
          :content="grammatic.article"
          class="card-body p-2 break-words"
        />

        <!-- List of values -->
        <div class="flex flex-col gap-2">
          <!-- Grammatic value -->
          <div
            v-for="value in grammatic.grammatic_values"
            :key="value.id"
            class="flex flex-row flex-wrap items-center"
          >
            <button
              v-if="isEdit"
              class="me-2 inline-block btn btn-xs btn-warning"
              @click="openEditValueModal(grammatic, value)"
            >
              <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"/></svg>
            </button>

            <span class="float-left underline">
              {{ value.value }} ({{ value.short }}):
            </span>
            <VMarkdownViewer
              :content="value.article"
              class="ms-2 break-words"
            />
          </div>
          <!-- End grammatic value -->

          <button
            v-if="isEdit"
            class="me-2 inline-block btn btn-xs btn-primary w-fit"
            @click="openAddValueModal(grammatic)"
          >
            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H192V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H400c17.7 0 32-14.3 32-32s-14.3-32-32-32H256V80z"/></svg>
          </button>
        </div>
        <!-- End list of values -->
      </div>
      <!-- End grammatic card -->
    </div>
    <!-- End container of cards of grammatic -->

    <!-- Modals -->
    <!-- Edit grammatic modal -->
    <VModal
      v-if="isEdit && editUpdate"
      ref="editGrammaticModal"
      without-button
      header="Изменить грамматическую категорию"
    >
      <template #postHeader>
        <button
          class="btn btn-sm btn-success"
          @click="applyEditGrammatic"
        >
          Сохранить
        </button>

        <VSaveLoader :is-save="!editGrammaticForm.processing" />
        <VFlashSuccess ref="editGrammaticFlash" />
      </template>

      <template #content>
        <div class="flex flex-col gap-2">
          <VInput
            v-model="editGrammaticForm.name"
            label="Название категории"
            :errors="editGrammaticForm.errors.name"
            required
          />

          <VMarkdownEditor
            v-model="editGrammaticForm.article"
            :language="language"
            label="Статья о грамматической категории"
          />

          <!-- Buttons -->
          <div class="mt-6 flex flex-row flex-wrap items-center justify-between">
            <div class="inline-flex flex-row items-center">
              <button
                class="btn btn-sm btn-error"
                @click="deleteGrammatic"
              >
                Удалить
              </button>
              <VSaveLoader :is-save="!deleteGrammaticForm.processing" />
            </div>

            <button
              class="btn btn-sm btn-success"
              @click="applyEditGrammatic"
            >
              Сохранить
            </button>
          </div>
          <!-- End buttons -->
        </div>
      </template>
    </VModal>
    <!-- End edit grammatic modal -->

    <!-- Edit grammatic value modal -->
    <VModal
      v-if="isEdit && editValueUpdate"
      ref="editValueModal"
      without-button
      header="Изменить грамматическое значение"
    >
      <template #postHeader>
        <button
          class="btn btn-sm btn-success"
          @click="applyEditValue"
        >
          Сохранить
        </button>

        <VSaveLoader :is-save="!editValueForm.processing" />
        <VFlashSuccess ref="editValueFlash" />
      </template>

      <template #content>
        <div class="flex flex-col gap-2">
          <VInput
            v-model="editValueForm.value"
            label="Название значения"
            :errors="editValueForm.errors.value"
            required
          />
          <VInput
            v-model="editValueForm.short"
            label="Короткое обозначение (используется в словаре)"
            :errors="editValueForm.errors.short"
            required
          />

          <VMarkdownEditor
            v-model="editValueForm.article"
            :language="language"
            label="Статья о грамматическом значении"
          />

          <!-- Buttons -->
          <div class="mt-6 flex flex-row flex-wrap items-center justify-between">
            <div class="inline-flex flex-row items-center">
              <button
                class="btn btn-sm btn-error"
                @click="deleteValue"
              >
                Удалить
              </button>
              <VSaveLoader :is-save="!deleteValueForm.processing" />
            </div>

            <button
              class="btn btn-sm btn-success"
              @click="applyEditValue"
            >
              Сохранить
            </button>
          </div>
          <!-- End buttons -->
        </div>
      </template>
    </VModal>
    <!-- End edit grammatic value modal -->

    <!-- Add grammatic value modal -->
    <VModal
      v-if="isEdit && addValueUpdate"
      ref="addValueModal"
      without-button
      header="Добавить грамматическое значение"
    >
      <template #postHeader>
        <button
          class="btn btn-sm btn-success"
          @click="addValue"
        >
          Сохранить
        </button>

        <VSaveLoader :is-save="!addValueForm.processing" />
      </template>

      <template #content>
        <div class="flex flex-col gap-2">
          <VInput
            v-model="addValueForm.value"
            label="Название значения"
            :errors="addValueForm.errors.value"
            required
          />
          <VInput
            v-model="addValueForm.short"
            label="Короткое обозначение (используется в словаре)"
            :errors="addValueForm.errors.short"
            required
          />

          <VMarkdownEditor
            v-model="addValueForm.article"
            :language="language"
            label="Статья о грамматическом значении"
          />

          <!-- Buttons -->
          <div class="mt-6 flex flex-row flex-wrap items-center justify-between">
            <span />

            <button
              class="btn btn-sm btn-success"
              @click="addValue"
            >
              Сохранить
            </button>
          </div>
          <!-- End buttons -->
        </div>
      </template>
    </VModal>
    <!-- End edit grammatic value modal -->

    <!-- Add grammatic modal -->
    <VModal
      v-if="addGrammaticUpdate"
      ref="addGrammaticModal"
      without-button
      header="Добавить грамматическую категорию"
    >
      <template #postHeader>
        <button
          class="btn btn-sm btn-success"
          @click="addGrammatic"
        >
          Сохранить
        </button>

        <VSaveLoader :is-save="!addGrammaticForm.processing" />
      </template>

      <template #content>
        <div class="flex flex-col gap-2">
          <VInput
            v-model="addGrammaticForm.name"
            label="Название категории"
            :errors="addGrammaticForm.errors.name"
            required
          />

          <VMarkdownEditor
            v-model="addGrammaticForm.article"
            :language="language"
            label="Статья о грамматической категории"
          />

          <!-- Buttons -->
          <div class="mt-6 flex flex-row flex-wrap items-center justify-between">
            <span />

            <button
              class="btn btn-sm btn-success"
              @click="addGrammatic"
            >
              Сохранить
            </button>
          </div>
          <!-- End buttons -->
        </div>
      </template>
    </VModal>
    <!-- End edit grammatic value modal -->
    <!-- End modals -->
  </LanguageLayout>
</template>

<style scoped>

</style>