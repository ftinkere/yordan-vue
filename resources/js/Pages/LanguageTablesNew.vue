<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { ref } from "vue";
import LanguageTable from "@/Components/LanguageTable.vue";
import LanguageLayout from "@/Layouts/LanguageLayout.vue";
import EditButton from "@/Components/buttons/EditButton.vue";
import VFlashSuccess from "@/Components/VFlashSuccess.vue";
import route from "ziggy-js";

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
const flashSuccess = ref(null)

function importTable() {
    if (!isEdit.value || !language.can_edit) {
        return
    }
    navigator.clipboard.read().then(data => {
        data[0].getType('text/html').then(data => {
            data.text().then(html => {
                if (html.includes('google-sheets-html-origin')) {
                    console.log(html);
                    useForm({
                        html,
                        _token,
                    }).post(route('languages.tables.import', [language.id]), {
                        preserveScroll: true,

                        onSuccess: () => {
                            flashSuccess.value?.flash()
                        }
                    })
                }
            });
        });
    })
}
</script>

<template>
  <LanguageLayout :language="language">
    <div class="flex flex-col gap-4">
      <!-- Buttons -->
      <div class="mb-4 flex flex-row flex-wrap justify-between">
        <!-- Left buttons -->
        <span v-if="!isEdit || !language.can_edit" />
        <div
          v-else
          class="flex flex-row flex-wrap gap-2"
        >
          <button
            class="btn btn-sm btn-primary"
            @click="importTable"
          >
            Импортировать таблицу
          </button>
        </div>
        <!-- End left buttons -->

        <!-- Right buttons -->
        <div
          v-if="language.can_edit"
          class="flex flex-row flex-wrap items-center gap-2"
        >
          <!-- Buttons in view mode -->
          <EditButton
            v-if="!isEdit"
            @click="isEdit = true"
          />
          <!-- End buttons in view mode -->

          <!-- Buttons in edit mode -->
          <div
            v-else
            class="contents"
          >
            <VFlashSuccess ref="flashSuccess" />

            <button
              class="btn btn-sm btn-info"
              @click="isEdit = false"
            >
              Посмотреть
            </button>
          </div>
          <!-- End buttons in edit mode -->
        </div>
        <!-- End right buttons -->
      </div>
      <!-- End buttons -->

      <div
        v-for="table in language.tables"
        :key="table.id"
      >
        <LanguageTable
          :table="table"
          :is-edit="isEdit"
          @success="flashSuccess?.flash()"
        />
        <hr>
      </div>
    </div>
  </LanguageLayout>
</template>

<style scoped>

</style>