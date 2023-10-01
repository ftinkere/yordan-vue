<script setup>
import LanguageLayout from "@/Layouts/LanguageLayout.vue";
import EditButton from "@/Components/EditButton.vue";
import { computed, ref } from "vue";
import VSaveLoader from "@/Components/VSaveLoader.vue";
import VSortable from "@/Components/VSortable.vue";
import { useForm } from "@inertiajs/vue3";
import VModal from "@/Components/VModal.vue";
import VInput from "@/Components/VInput.vue";
import route from "ziggy-js";
import VCheckbox from "@/Components/VCheckbox.vue";
import _ from "lodash";

const { language } = defineProps({
    language: {
        type: Object,
        required: true,
    }
})

const isEdit = ref(false)

const orthographemeModal = ref(null)

const orthographemeForm = useForm({
    id: 0,
    lowercase: '',
    uppercase: '',
    is_alphabet: false,
})

const openOrthographemeModal = function (orthographeme) {
    if (!language.can_edit || !isEdit.value) {
        return ;
    }

    orthographemeForm.id = orthographeme.id
    orthographemeForm.lowercase = orthographeme.lowercase
    orthographemeForm.uppercase = orthographeme.uppercase
    orthographemeForm.is_alphabet = !!orthographeme.is_alphabet

    orthographemeModal.value.open()
}

const applyOrthographemeForm = function() {
    orthographemeForm.post(route('languages.orthography.update', [language.id, orthographemeForm.id]))
}

const deleteOrthographemeForm = useForm({})

const deleteOrthographeme = function() {
    deleteOrthographemeForm.delete(route('languages.orthography.delete', [language.id, orthographemeForm.id]))
}

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
        class="flex flex-row flex-wrap items-center"
      >
        <!-- Edit mode -->
        <VSaveLoader :is-save="!sortableForm.isDirty" />

        <button
          class="btn btn-sm btn-success"
          @click="isEdit = false"
        >
          Сохранить
        </button>
      </div>
    </div>
    <!-- End buttons -->

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
        @click="openOrthographemeModal(orthographeme)"
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

      <div
        v-if="language.can_edit && isEdit"
        class="fill-white ms-4 p-2 btn btn-sm btn-success"
      >
        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H192V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H400c17.7 0 32-14.3 32-32s-14.3-32-32-32H256V80z"/></svg>
      </div>
    </VSortable>
    <!-- End orthographemes -->

    <VModal
      ref="orthographemeModal"
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

        <VSaveLoader :is-save="!orthographemeForm.processing" />
      </template>

      <template #content>
        <div class="flex flex-col gap-2">
          <div class="flex flex-col sm:grid sm:grid-cols-2">
            <VInput
              v-model="orthographemeForm.uppercase"
              label="Заглавная орфографема"
            />
            <VInput
              v-model="orthographemeForm.lowercase"
              label="Малая орфографема"
              required
            />
          </div>

          <VCheckbox
            v-model="orthographemeForm.is_alphabet"
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
  </LanguageLayout>
</template>

<style scoped>

</style>