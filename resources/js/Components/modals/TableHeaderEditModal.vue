<script setup>
import VModalN from "@/Components/VModalN.vue";
import VMarkdownEditor from "@/Components/VMarkdownEditor.vue";
import VInput from "@/Components/VInput.vue";
import VFlashSuccess from "@/Components/VFlashSuccess.vue";
import { inject, ref } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import route from "ziggy-js";

const { table } = defineProps({
    table: {
        type: Object,
        required: true,
    }
})
const language = inject('language')
const _token = usePage().props.csrf_token

const modal = ref(null)
const flashSuccess = ref(null)

const emits = defineEmits(['success'])

function close() {
    modal.value?.close()
}
function open() {
    modal.value?.open()
}

defineExpose({ close, open })


const editTableForm = useForm({
    name: table.name,
    caption: table.caption,
    comments: table.comments,
    _token
})

function editTable() {
    editTableForm.post(route('languages.tables.update', [language.id, table.id]), {
        preserveScroll: true,

        onSuccess: () => {
            flashSuccess.value?.flash()
            emits('success')
        }
    })
}

</script>

<template>
  <!-- Edit table modal -->
  <VModalN
    ref="modal"
    header="Изменить заголовок таблицы"
    class="max-w-screen-md"
  >
    <template #postHeader>
      <button
        class="btn btn-sm btn-success"
        @click="editTable"
      >
        Сохранить
      </button>

      <VFlashSuccess ref="flashSuccess" />
    </template>

    <template #content>
      <div class="flex flex-col">
        <VInput
          v-model="editTableForm.name"
          label="Название таблицы"
        />
        <VInput
          v-model="editTableForm.caption"
          type="textarea"
          label="Описание таблицы"
        />

        <VMarkdownEditor
          v-model="editTableForm.comments"
          label="Комментарии"
          :language="language"
        />

        <div class="mt-4 flex flex-row justify-between">
          <span />

          <button
            class="btn btn-sm btn-success w-fit"
            @click="editTable"
          >
            Сохранить
          </button>
        </div>
      </div>
    </template>
  </VModalN>
  <!-- End edit table modal -->
</template>

<style scoped>

</style>