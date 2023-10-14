<script setup>

    import LanguageLayout from "@/Layouts/LanguageLayout.vue";
    import { ref } from "vue";
    import EditButton from "@/Components/EditButton.vue";
    import VSaveLoader from "@/Components/VSaveLoader.vue";
    import VFlashSuccess from "@/Components/VFlashSuccess.vue";
    import { useForm } from "@inertiajs/vue3";
    import route from "ziggy-js";
    import VModal from "@/Components/VModal.vue";
    import VInput from "@/Components/VInput.vue";
    import VCheckbox from "@/Components/VCheckbox.vue";
    import _ from "lodash";

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

    console.log(language.tables)

    const isEdit = ref(editMode)
    const flashSuccess = ref(null)

    // Add table
    const addTableModal = ref(null)
    const addTableForm = useForm({
        name: '',
        caption: '',
    })
    function addTable() {
        addTableForm.post(route('languages.tables.store', [language.id]), {
            onSuccess: () => {
                addTableModal.value?.close()
                flashSuccess.value?.flash()
                addTableForm.reset()
            }
        })
    }

    // Edit table header
    const editTableModal = ref(null)
    const editTableFlash = ref(null)
    const editTableForm = useForm({
        id: 0,
        name: '',
        caption: '',
    })
    function openEditTableModal(table) {
        if (!language.can_edit || !isEdit.value) {
            return
        }

        editTableForm.id = table.id
        editTableForm.name = table.name
        editTableForm.caption = table.caption

        editTableModal.value?.open()
    }
    function editTable() {
        editTableForm.post(route('languages.tables.update', [language.id, editTableForm.id]), {
            onSuccess: () => {
                flashSuccess.value?.flash()
                editTableFlash.value?.flash()
            }
        })
    }

    // Delete table
    const deleteTableModal = ref(null)
    const deleteTableForm = useForm({
        table_id: 0,
    })
    function openDeleteTableModal(table) {
        if (!language.can_edit || !isEdit.value) {
            return
        }

        deleteTableForm.table_id = table.id

        deleteTableModal.value?.open()
    }
    function deleteTable() {
        deleteTableForm.delete(route('languages.tables.delete', [language.id, deleteTableForm.table_id]), {
            onSuccess: () => {
                deleteTableModal.value?.close()
                flashSuccess.value?.flash()
            }
        })
    }

    // Add row
    const addTableRowForm = useForm({})
    function addTableRow(table) {
        addTableRowForm.post(route('languages.tables.rows.store', [language.id, table.id]), {
            onSuccess: () => {
                flashSuccess.value?.flash()
            }
        })
    }


    // Delete row
    const deleteTableRowModal = ref(null)
    const deleteTableRowForm = useForm({
        table_id: 0,
        row_id: 0,
    })
    function openDeleteTableRowModal(table, row) {
        if (!language.can_edit || !isEdit.value) {
            return
        }

        deleteTableRowForm.table_id = table.id
        deleteTableRowForm.row_id = row.id

        deleteTableRowModal.value?.open()
    }
    function deleteTableRow() {
        deleteTableRowForm.delete(route('languages.tables.rows.delete', [language.id, deleteTableRowForm.table_id, deleteTableRowForm.row_id]), {
            onSuccess: () => {
                deleteTableRowModal.value?.close()
                flashSuccess.value?.flash()
            }
        })
    }

    // Add cell
    const addTableCellModal = ref(null)
    const addTableCellForm = useForm({
        table_id: 0,
        row_id: 0,
        content: '',
        is_header: false,
    })
    function openAddTableCellModal(table, row) {
        if (!language.can_edit || !isEdit.value) {
            return
        }

        addTableCellForm.table_id = table.id
        addTableCellForm.row_id = row.id

        addTableCellModal.value?.open()
    }
    function addTableCell() {
          addTableCellForm.post(route('languages.tables.cells.store', [language.id, addTableCellForm.table_id, addTableCellForm.row_id]), {
              onSuccess: () => {
                  addTableCellModal.value?.close()
                  flashSuccess.value?.flash()
                  addTableCellForm.reset()
              }
          })
    }

    // Edit cell part
    const editTableCellModal = ref(null)

    // Delete cell
    const deleteTableCellForm = useForm({
        table_id: 0,
        cell_id: 0,
    })
    function deleteTableCell() {
        editTableCellForm.delete(route('languages.tables.cells.delete', [language.id, deleteTableCellForm.table_id, deleteTableCellForm.cell_id]), {
            onSuccess: () => {
                flashSuccess.value?.flash()
                editTableCellModal.value?.close()
            }
        })
    }


    // Edit cell
    const editTableCellFlash = ref(null)
    const editTableCellForm = useForm({
        table_id: 0,
        cell_id: 0,
        content: '',
        is_header: false,
        rowspan: 1,
        colspan: 1,
    })
    function openEditTableCellModal(table, cell) {
        if (!language.can_edit || !isEdit.value) {
            return
        }

        editTableCellForm.table_id = table.id
        editTableCellForm.cell_id = cell.id
        editTableCellForm.content = cell.content
        editTableCellForm.is_header = cell.is_header
        editTableCellForm.rowspan = cell.rowspan
        editTableCellForm.colspan = cell.colspan

        deleteTableCellForm.table_id = table.id
        deleteTableCellForm.cell_id = cell.id

        editTableCellModal.value?.open()
    }
    function editTableCell() {
          editTableCellForm.post(route('languages.tables.cells.update', [language.id, editTableCellForm.table_id, editTableCellForm.cell_id]), {
              onSuccess: () => {
                  flashSuccess.value?.flash()
                  editTableCellFlash.value?.flash()
              }
          })
    }

    // styles
    function good_styles(styles) {
        return _.merge(... _.map(styles, style => {
            let obj = {}
            obj[style.style] = style.value
            return obj
        }))
    }

    const editStylesModal = ref(null)
    const editStylesForm = useForm({
        table_id: 0,
        cell_id: 0,
        style: '',
        value: '',
    })
    function openStylesModal() {
        if (!language.can_edit || !isEdit.value) {
            return
        }

        editStylesForm.table_id = editTableCellForm.table_id
        editStylesForm.cell_id = editTableCellForm.cell_id

        editStylesModal.value?.open()
    }
    function editStyle() {
        editStylesForm.post(route('languages.tables.cells.styles.update', [language.id, editStylesForm.table_id, editStylesForm.cell_id]), {
            onSuccess: () => {
                editTableCellFlash.value?.flash()
                flashSuccess.value?.flash()
            }
        })
    }

</script>

<template>
  <LanguageLayout :language="language">
    <!-- Buttons -->
    <div class="mb-4 flex flex-row flex-wrap justify-between">
      <!-- Left buttons -->
      <span v-if="!isEdit" />
      <div
        v-else
        class="flex flex-row flex-wrap gap-2"
      >
        <button
          class="btn btn-sm btn-primary"
          @click="addTableModal?.open()"
        >
          Добавить таблицу
        </button>
      </div>
      <!-- End left buttons -->

      <!-- Right buttons -->
      <div class="flex flex-row flex-wrap items-center gap-2">
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
          <VSaveLoader :is-save="!editTableForm.processing && !addTableRowForm.processing" />
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

    <!-- Tables container -->
    <div
      class="flex gap-4"
      :class="{
        'flex-row flex-wrap': !isEdit,
        'flex-col': isEdit,
      }"
    >
      <!-- Table -->
      <div
        v-for="table in language.tables"
        :key="table.id"
        class="flex flex-col gap-2"
      >
        <table
          class="table table-auto border-y w-fit h-fit"
          :class="{ 'table-zebra': table.is_zebra }"
        >
          <!-- Caption with name of table -->
          <caption class="mb-1 table-caption">
            <h4 class="inline-flex text-xl font-bold gap-2">
              {{ table.name }}
              <button
                v-if="isEdit"
                class="btn btn-xs btn-warning tooltip tooltip-right"
                data-tip="Редактировать заголовок"
                @click="openEditTableModal(table)"
              >
                <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"/></svg>
              </button>
            </h4>
            <br>
            {{ table.caption }}
          </caption>
          <!-- End caption with name of table -->

          <!-- Rows -->
          <tr
            v-for="row in table.rows"
            :key="row.id"
            class="border-y"
          >
            <td
              v-for="cell in row.cells"
              :key="cell.id"
              :is="cell.is_header ? 'th' : 'td'"
              class="border-x border-x-neutral-600"
              :class="{
                'cursor-pointer hover:bg-neutral-700': isEdit,
                'font-bold bg-neutral-800': cell.is_header,
              }"
              :colspan="cell.colspan"
              :rowspan="cell.rowspan"
              :style="good_styles(cell.styles)"
              v-html="cell.content"
              @click="openEditTableCellModal(table, cell)"
            />

            <!-- Last cell in row in edit mode-->
            <td v-if="isEdit">
              <!-- Buttons on end row -->
              <div class="flex flex-row flex-wrap gap-4">
                <!-- Button to add table cell -->
                <button
                  class="btn btn-sm btn-primary tooltip tooltip-right w-fit"
                  data-tip="Добавить ячейку"
                  @click="openAddTableCellModal(table, row)"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H192V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H400c17.7 0 32-14.3 32-32s-14.3-32-32-32H256V80z"/></svg>
                </button>
                <!-- End button to add table cell -->

                <!-- Button to delete table row -->
                <button
                  v-if="isEdit"
                  class="btn btn-sm btn-error tooltip tooltip-right w-fit"
                  data-tip="Удалить строку"
                  @click="openDeleteTableRowModal(table, row)"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/></svg>
                </button>
                <!-- End button to delete table row -->
              </div>
              <!-- End buttons on end row -->
            </td>
            <!-- End last cell in row in edit mode-->
          </tr>
          <!-- End rows -->
        </table>

        <div class="flex flex-row flex-wrap justify-between">
          <!-- Button to add row to the table -->
          <button
            v-if="isEdit"
            class="btn btn-sm btn-primary tooltip tooltip-right w-fit"
            data-tip="Добавить строку"
            @click="addTableRow(table)"
          >
            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H192V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H400c17.7 0 32-14.3 32-32s-14.3-32-32-32H256V80z"/></svg>
          </button>
          <!-- End button to add row to the table -->

          <!-- Button to delete table -->
          <button
            v-if="isEdit"
            class="btn btn-sm btn-error tooltip tooltip-left w-fit"
            data-tip="Удалить таблицу"
            @click="openDeleteTableModal(table)"
          >
            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/></svg>
          </button>
          <!-- End button to delete table -->
        </div>

        <hr v-if="isEdit" />
      </div>
      <!-- End table -->
    </div>
    <!-- End tables container -->

    <!-- # Modals -->
    <!-- Add table modal -->
    <VModal
      ref="addTableModal"
      without-button
      header="Добавить таблицу"
      class="max-w-screen-md"
    >
      <template #postHeader>
        <button
          class="btn btn-sm btn-success"
          @click="addTable"
        >
          Сохранить
        </button>
      </template>

      <template #content>
        <div class="flex flex-col">
          <VInput
            v-model="addTableForm.name"
            label="Название таблицы"
          />
          <VInput
            v-model="addTableForm.caption"
            type="textarea"
            label="Описание таблицы"
          />

          <div class="mt-4 flex flex-row justify-between">
            <span />

            <button
              class="btn btn-sm btn-success w-fit"
              @click="addTable"
            >
              Сохранить
            </button>
          </div>
        </div>
      </template>
    </VModal>
    <!-- End add table modal -->

    <!-- Edit table modal -->
    <VModal
      v-if="isEdit"
      ref="editTableModal"
      without-button
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
        <VSaveLoader :is-save="!editTableForm.processing" />
        <VFlashSuccess ref="editTableFlash" />
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
    </VModal>
    <!-- End edit table modal -->

    <!-- Delete table modal -->
    <VModal
      v-if="isEdit"
      ref="deleteTableModal"
      without-button
      header="Удалить таблицу"
      class="max-w-screen-sm w-fit"
    >
      <template #content>
        <div class="flex flex-row gap-8 justify-between">
          <button
            class="btn btn-sm border-neutral-600"
            @click="deleteTableModal.close()"
          >
            Отменить
          </button>

          <button
            class="btn btn-sm btn-error w-fit"
            @click="deleteTable"
          >
            Удалить
          </button>
        </div>
      </template>
    </VModal>
    <!-- End delete table modal -->

    <!-- Delete table row modal -->
    <VModal
      v-if="isEdit"
      ref="deleteTableRowModal"
      without-button
      header="Удалить строку таблицы"
      class="max-w-screen-sm w-fit"
    >
      <template #content>
        <div class="flex flex-row gap-8 justify-between">
          <button
            class="btn btn-sm border-neutral-600"
            @click="deleteTableRowForm.close()"
          >
            Отменить
          </button>

          <button
            class="btn btn-sm btn-error w-fit"
            @click="deleteTableRow"
          >
            Удалить
          </button>
        </div>
      </template>
    </VModal>
    <!-- End delete table row modal -->

    <!-- Add table cell modal -->
    <VModal
      v-if="isEdit"
      ref="addTableCellModal"
      without-button
      header="Добавить ячейку к строке?"
      class="max-w-screen-md"
    >
      <template #postHeader>
        <button
          class="btn btn-sm btn-success"
          @click="addTableCell"
        >
          Сохранить
        </button>
        <VSaveLoader :is-save="!addTableCellForm.processing" />
      </template>

      <template #content>
        <div class="flex flex-col">
          <VInput
            v-model="addTableCellForm.content"
            type="textarea"
            label="Контент ячейки"
          />
          <VCheckbox
            v-model="addTableCellForm.is_header"
            class="w-fit"
          >
            Это ячейка заголовка?
          </VCheckbox>

          <div class="mt-4 flex flex-row justify-between">
            <span />

            <button
              class="btn btn-sm btn-success w-fit"
              @click="addTableCell"
            >
              Сохранить
            </button>
          </div>
        </div>
      </template>
    </VModal>
    <!-- End add table cell modal -->

    <!-- Edit table cell modal -->
    <VModal
      v-if="isEdit"
      ref="editTableCellModal"
      without-button
      header="Редактировать ячейку"
      class="max-w-screen-md"
    >
      <template #postHeader>
        <button
          class="btn btn-sm btn-success"
          @click="editTableCell"
        >
          Сохранить
        </button>
        <VSaveLoader :is-save="!editTableCellForm.processing" />
      </template>

      <template #content>
        <div class="flex flex-col">
          <VInput
            v-model="editTableCellForm.content"
            type="textarea"
            label="Контент ячейки"
          />
          <VCheckbox
            v-model="editTableCellForm.is_header"
            class="w-fit"
          >
            Это ячейка заголовка?
          </VCheckbox>
          <div class="grid grid-cols-2">
            <VInput
              v-model="editTableCellForm.rowspan"
              type="number"
              label="Количество строк"
            />
            <VInput
              v-model="editTableCellForm.colspan"
              type="number"
              label="Количество столбцов"
            />
          </div>

          <div class="mt-4 flex flex-row justify-between">
            <span />

            <button
              class="btn btn-sm btn-primary"
              @click="openStylesModal"
            >
              Стили
            </button>
          </div>

          <div class="mt-4 flex flex-row justify-between">
            <!-- Button to delete table cell -->
            <button
              class="inline-flex gap-2 btn btn-sm btn-error w-fit"
              @click="deleteTableCell"
            >
              <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/></svg>
              Удалить
            </button>
            <!-- End button to delete table cell -->

            <button
              class="btn btn-sm btn-success w-fit"
              @click="editTableCell"
            >
              Сохранить
            </button>
          </div>
        </div>
      </template>
    </VModal>
    <!-- End edit table cell modal -->

    <!-- Edit table cell styles modal -->
    <VModal
      v-if="isEdit"
      ref="editStylesModal"
      without-button
      header="Редактировать стили ячейки"
    >
      <template #postHeader>
        <VSaveLoader :is-save="!editStylesForm.processing" />
      </template>

      <template #content>
        <div class="flex flex-col">
          <article class="alert">
            На первое время, надеюсь не больше недели будет такой примитивный CSS применятор стилей к ячейке
            Слева пишете css свойство, которое нагуглили, справа его значение.
            Пока только по одной ячейке по одному свойству. Более удобный редактор как в гугл таблицах в разработке.
          </article>
          <div class="grid grid-cols-2">
            <VInput
              v-model="editStylesForm.style"
              label="CSS стиль"
              input-class="rounded-e-none"
            />
            <VInput
              v-model="editStylesForm.value"
              label="Значение стиля"
              input-class="rounded-none"
            >
              <template #button>
                <button
                  class="btn btn-success rounded-s-none"
                  @click="editStyle"
                >
                  Применить
                </button>
              </template>
            </VInput>
          </div>
        </div>
      </template>
    </VModal>
    <!-- End edit table cell styles modal -->
    <!-- # End modals -->
  </LanguageLayout>
</template>

<style scoped>

</style>