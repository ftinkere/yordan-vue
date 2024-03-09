<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { inject, nextTick, ref } from "vue";
import _ from "lodash";
import TableHeaderEditModal from "@/Components/modals/TableHeaderEditModal.vue";
import VMarkdownViewer from "@/Components/VMarkdownViewer.vue";
import EditButton from "@/Components/buttons/EditButton.vue";
import DownIcon from "@/Components/icons/DownIcon.vue";
import UpIcon from "@/Components/icons/UpIcon.vue";
import TrashIcon from "@/Components/icons/TrashIcon.vue";
import route from "ziggy-js";

const { table, isEdit } = defineProps({
    table: {
        type: Object,
        required: true,
    },
    isEdit: {
        type: Boolean,
        default: false,
    }
})
const language = inject('language')
const _token = usePage().props.csrf_token

const emits = defineEmits(['success'])

const editModal = ref(null)

const update = ref(true)

function good_styles(styles) {
    return _.merge(... _.map(styles, style => {
        let obj = {}
        obj[style.style] = style.value
        // obj[style.style] = style.style.includes('color') ? shadow(style.value) : style.value
        return obj
    }))
}

function move(direction) {
    if (!isEdit || !language.can_edit) {
        return
    }

    useForm({
        direction,
        _token,
    }).post(route('languages.tables.move', [language.id, table.id]), {
        preserveScroll: true,
        onSuccess: () => {
            emits('success')
        }
    })
}

function updateTable() {
    if (!isEdit || !language.can_edit) {
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
                    }).post(route('languages.tables.import.update', [language.id, table.id]), {
                        preserveScroll: true,

                        onSuccess: () => {
                            emits('success')
                        }
                    })
                }
            });
        });
    })
}

function deleteTable() {
    if (confirm('Действительно удалить таблицу?')) {
        useForm({ _token }).delete(route('languages.tables.delete', [language.id, table.id]), {
            preserveScroll: true,

            onSuccess: () => {
                emits('success')
            }
        })
    }
}
</script>

<template>
  <div class="my-2 flex flex-col gap-2">
    <div class="mx-auto overflow-x-auto">
      <table
        class="mx-auto table table-auto border-y w-fit h-fit"
        :class="{ 'table-zebra': table.is_zebra }"
        :data-id="table.id"
        :data-order="table.order"
        @click.stop
      >
        <!-- Caption with name of table -->
        <caption class="mb-1 table-caption">
          <h4 class="inline-flex text-xl font-bold gap-2">
            {{ table.name }}
            <EditButton
              v-if="isEdit"
              @click="editModal?.open()"
            />
          </h4>
          <br>
          {{ table.caption }}
        </caption>
        <!-- End caption with name of table -->

        <!-- Rows -->
        <tr
          v-for="row in table.rows"
          :key="row.id"
        >
          <template
            v-for="cell in row.cells"
            :key="cell.id"
          >
            <td
              :is="cell.is_header ? 'th' : 'td'"
              v-if="cell.merged_in === null || cell.merged_in === cell.id"
              class="border-x-neutral-600 border-y-neutral-200 border"
              :class="{
                'font-bold bg-neutral-800': cell.is_header,
              }"
              :colspan="cell.colspan"
              :rowspan="cell.rowspan"
              :style="good_styles(cell.styles)"
              :data-id="cell.id"
              :data-order="cell.order"
              :data-row-order="row.order"
            >
              {{ cell.content }}
            </td>
          </template>
        </tr>
        <!-- End rows -->
      </table>

      <article v-if="table.comments && update">
        <VMarkdownViewer :content="table.comments" />
      </article>
    </div>

    <div
      v-if="isEdit"
      class="flex flex-row flex-wrap justify-between"
    >
      <div class="flex flex-row gap-2 items-center">
        <button
          class="btn btn-sm btn-warning tooltip tooltip-top w-fit"
          data-tip="Переместить вверх"
          @click="move('up')"
        >
          <UpIcon class="text-2xl" />
        </button>

        <button
          class="btn btn-sm btn-warning tooltip tooltip-top w-fit"
          data-tip="Переместить вниз"
          @click="move('down')"
        >
          <DownIcon class="text-2xl" />
        </button>

        <button
          class="btn btn-sm btn-primary"
          @click="updateTable"
        >
          Обновить
        </button>
      </div>

      <!-- Button to delete table -->
      <button
        class="btn btn-sm btn-error tooltip tooltip-left w-fit"
        data-tip="Удалить таблицу"
        @click="deleteTable"
      >
        <TrashIcon />
      </button>
      <!-- End button to delete table -->
    </div>
  </div>

  <TableHeaderEditModal
    ref="editModal"
    :table="table"
    @success="emits('success'); update = false; nextTick(() => update = true)"
  />
</template>

<style scoped>

</style>