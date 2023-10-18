<script setup>
    import LanguageLayout from "@/Layouts/LanguageLayout.vue";
    import { computed, defineComponent, nextTick, onMounted, reactive, ref } from "vue";
    import EditButton from "@/Components/EditButton.vue";
    import VSaveLoader from "@/Components/VSaveLoader.vue";
    import VFlashSuccess from "@/Components/VFlashSuccess.vue";
    import { useForm } from "@inertiajs/vue3";
    import route from "ziggy-js";
    import VModal from "@/Components/VModal.vue";
    import VInput from "@/Components/VInput.vue";
    import _ from "lodash";
    import TableCellSelector from "js-table-cell-selector";

    import { Sketch } from '@ckpack/vue-color';

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

    // Edit cell
    const editTableCellContentForm = useForm({
        table_id: 0,
        cell_id: 0,
        content: '',
    })
    function prepareEditTableCell(table, cell) {
        editTableCellContentForm.table_id = table.dataset.id
        editTableCellContentForm.cell_id = cell.dataset.id
        editTableCellContentForm.content = cell.innerText

        // deleteTableCellForm.table_id = table.id
        // deleteTableCellForm.cell_id = cell.id
    }
    function editTableCell(table, cell) {
        if (table && cell) {
            prepareEditTableCell(table, cell)

            console.log(editTableCellContentForm)

            editTableCellContentForm.post(route('languages.tables.cells.update_content', [language.id, editTableCellContentForm.table_id, editTableCellContentForm.cell_id]), {
                preserveScroll: true,
                onSuccess: () => {
                    flashSuccess.value?.flash()
                }
            })
        }
    }

    // styles
    function good_styles(styles) {
        return _.merge(... _.map(styles, style => {
            let obj = {}
            obj[style.style] = style.value
            return obj
        }))
    }

    // Colors

    function componentToHex(c) {
        const hex = c.toString(16)
        return hex.length === 1 ? "0" + hex : hex
    }

    function rgbToHex(r, g, b, a = null) {
        return "#" + componentToHex(r) + componentToHex(g) + componentToHex(b) + (a !== null ? componentToHex(a) : '')
    }

    function strToRgb(str) {
        const result = /^rgba?\((\d{1,3}), (\d{1,3}), (\d{1,3})(, (\d{1,3}))?\)$/i.exec(str)
        return result ? [
            parseInt(result[1], 10),
            parseInt(result[2], 10),
            parseInt(result[3], 10),
            result[5] ? parseInt(result[5], 10) : null,
        ] : null
    }

    function strToHex(str) {
        const res = strToRgb(str);
        return res ? rgbToHex(res[0], res[1], res[2], res[3]) : null
    }



    // New pane code

    // Tables selection
    const tableEls = ref(null)
    const tableSelectors = reactive([])
    const selectedCells = ref({})
    const selectedTable = ref(null)
    const lastSelectedCell = ref(null)
    const textColor = ref({ hex: '#ffffff' })
    const backgroundColor = ref({ hex: '#000000' })
    const colorUpdate = ref(true)

    onMounted(() => {
        tableEls.value?.forEach(tableEl => {
            const options = { deselectOutTableClick: false }
            const buffer = new TableCellSelector.Buffer()
            const tcs = new TableCellSelector(tableEl, options, buffer)
            tcs.onStartSelect = (ev, cell) => {
                selectedTable.value = tableEl
                tableSelectors.forEach(selector => selector.deselect())
                lastSelectedCell.value = cell
                selectedCells.value = {}

                textColor.value.hex = strToHex(getComputedStyle(cell).color)
                backgroundColor.value.hex = strToHex(getComputedStyle(cell).backgroundColor)
                colorUpdate.value = false;
                nextTick(() => {
                    colorUpdate.value = true;
                })
            }
            tcs.onSelect = (prev, cell) => {
                selectedCells.value[cell.dataset.id] = cell
                lastSelectedCell.value = cell
            }

            tableSelectors.push(tcs)
        })
    })

    const deb = _.debounce(() => { editTableCell(selectedTable.value, lastSelectedCell.value) }, 3000)
    const lastSelectedContent = computed({
        get: () => {
            return lastSelectedCell.value?.innerText
        },
        set: value => {
            if (lastSelectedCell.value) {
                lastSelectedCell.value.innerText = value
            }
            deb()
        }
    })

    function toggleStyle(style, value) {
        if (!selectedTable.value || (selectedCells.value.length !== 0 && !lastSelectedCell.value)) {
            return
        }

        const cells = Object.keys(selectedCells.value)
        if (cells.length === 0) {
            cells.push(parseInt(lastSelectedCell.value.dataset.id))
        }

        useForm({
            cells,
            style,
            value,
        }).post(route('languages.tables.cells.styles.toggle', [language.id, selectedTable.value.dataset.id]), {
            preserveScroll: true,
            onSuccess: () => {
                flashSuccess.value?.flash()
            }
        })
    }
    function updateStyle(style, value) {
        if (!selectedTable.value || (selectedCells.value.length !== 0 && !lastSelectedCell.value)) {
            return
        }

        const cells = Object.keys(selectedCells.value)
        if (cells.length === 0) {
            cells.push(parseInt(lastSelectedCell.value.dataset.id))
        }

        useForm({
            cells,
            style,
            value,
        }).post(route('languages.tables.cells.styles.update', [language.id, selectedTable.value.dataset.id]), {
            preserveScroll: true,
            onSuccess: () => {
                flashSuccess.value?.flash()
            }
        })
    }

    function updateTextColor() {
        updateStyle('color', textColor.value.hex)
    }
    function updateBackgroundColor() {
        updateStyle('background-color', backgroundColor.value.hex)
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
          <button
            class="btn btn-sm btn-info"
            @click="isEdit = false; tableSelectors.forEach(selector => selector.deselect())"
          >
            Посмотреть
          </button>
        </div>
        <!-- End buttons in edit mode -->
      </div>
      <!-- End right buttons -->
    </div>
    <!-- End buttons -->

    <!-- Table editor pane -->
    <div
      v-if="isEdit"
      class="mb-4 sticky top-0 z-10 flex flex-col"
    >
      <div class="relative">
        <div class="flex flex-row gap-1 items-center bg-neutral-800 rounded-lg rounded-b-none overflow-x-auto overflow-y-visible w-full">
          <!-- Bold -->
          <button
            class="btn btn-square btn-ghost"
            @click="toggleStyle('font-weight', '700')"
          >
            <svg xmlns="http://www.w3.org/2000/svg" height="1.5em" viewBox="0 0 384 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M0 64C0 46.3 14.3 32 32 32H80 96 224c70.7 0 128 57.3 128 128c0 31.3-11.3 60.1-30 82.3c37.1 22.4 62 63.1 62 109.7c0 70.7-57.3 128-128 128H96 80 32c-17.7 0-32-14.3-32-32s14.3-32 32-32H48V256 96H32C14.3 96 0 81.7 0 64zM224 224c35.3 0 64-28.7 64-64s-28.7-64-64-64H112V224H224zM112 288V416H256c35.3 0 64-28.7 64-64s-28.7-64-64-64H224 112z"/></svg>
          </button>

          <!-- Italic -->
          <button
            class="btn btn-square btn-ghost"
            @click="toggleStyle('font-style', 'italic')"
          >
            <svg xmlns="http://www.w3.org/2000/svg" height="1.5em" viewBox="0 0 384 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M128 64c0-17.7 14.3-32 32-32H352c17.7 0 32 14.3 32 32s-14.3 32-32 32H293.3L160 416h64c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H90.7L224 96H160c-17.7 0-32-14.3-32-32z"/></svg>
          </button>

          <!-- Text align -->
          <div class="form-control join join-horizontal border-x">
            <!-- Align left -->
            <button
              class="btn btn-square btn-ghost join-item"
              @click="updateStyle('text-align', 'left')"
            >
              <svg xmlns="http://www.w3.org/2000/svg" height="1.5em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M288 64c0 17.7-14.3 32-32 32H32C14.3 96 0 81.7 0 64S14.3 32 32 32H256c17.7 0 32 14.3 32 32zm0 256c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H256c17.7 0 32 14.3 32 32zM0 192c0-17.7 14.3-32 32-32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32zM448 448c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32z"/></svg>
            </button>
            <!-- Align center -->
            <button
              class="btn btn-square btn-ghost join-item"
              @click="updateStyle('text-align', 'center')"
            >
              <svg xmlns="http://www.w3.org/2000/svg" height="1.5em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M352 64c0-17.7-14.3-32-32-32H128c-17.7 0-32 14.3-32 32s14.3 32 32 32H320c17.7 0 32-14.3 32-32zm96 128c0-17.7-14.3-32-32-32H32c-17.7 0-32 14.3-32 32s14.3 32 32 32H416c17.7 0 32-14.3 32-32zM0 448c0 17.7 14.3 32 32 32H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H32c-17.7 0-32 14.3-32 32zM352 320c0-17.7-14.3-32-32-32H128c-17.7 0-32 14.3-32 32s14.3 32 32 32H320c17.7 0 32-14.3 32-32z"/></svg>
            </button>
            <!-- Align right -->
            <button
              class="btn btn-square btn-ghost join-item"
              @click="updateStyle('text-align', 'right')"
            >
              <svg xmlns="http://www.w3.org/2000/svg" height="1.5em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M448 64c0 17.7-14.3 32-32 32H192c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32zm0 256c0 17.7-14.3 32-32 32H192c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32zM0 192c0-17.7 14.3-32 32-32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32zM448 448c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32z"/></svg>
            </button>
          </div>

          <div class="static dropdown dropdown-bottom overflow-visible">
            <label
              tabindex="0"
              class="btn btn-square btn-ghost join-item"
              @click="updateStyle('text-align', 'right')"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                height="1.5em"
                viewBox="0 0 448 512"
              >
                <!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                <path d="M254 52.8C249.3 40.3 237.3 32 224 32s-25.3 8.3-30 20.8L57.8 416H32c-17.7 0-32 14.3-32 32s14.3 32 32 32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32h-1.8l18-48H303.8l18 48H320c-17.7 0-32 14.3-32 32s14.3 32 32 32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H390.2L254 52.8zM279.8 304H168.2L224 155.1 279.8 304z"/>
                <path :fill="textColor.hex" d="m32,435.16771c-17.7,0 -32,14.3 -32,32s14.3,32 32,32l384,0c17.7,0 32,-14.3 32,-32s-14.3,-32 -32,-32l-384,0z"/>
              </svg>
            </label>

            <div class="dropdown-content">
              <Sketch
                v-if="colorUpdate"
                v-model="textColor"
                disable-alpha
                class="text-black"
                @update:model-value="updateTextColor"
              />
            </div>
          </div>

          <div class="static dropdown dropdown-bottom overflow-visible">
            <label
              tabindex="0"
              class="btn btn-square btn-ghost join-item"
              @click="updateStyle('text-align', 'right')"
            >
              <svg
                class="fill-white"
                xmlns="http://www.w3.org/2000/svg"
                height="1.5em"
                viewBox="0 0 448 512"
              >
                <!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                <path d="M0 96C0 60.7 28.7 32 64 32H384c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96z"/>
                <path :fill="backgroundColor.hex" d="M0 96C0 60.7 28.7 32 64 32H384c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96z"/>
              </svg>
            </label>

            <div class="dropdown-content">
              <Sketch
                v-if="colorUpdate"
                v-model="backgroundColor"
                class="text-black"
                @update:model-value="updateBackgroundColor"
              />
            </div>
          </div>

          <VFlashSuccess ref="flashSuccess" />
        </div>
      </div>

      <VInput
        v-model="lastSelectedContent"
        class="w-full"
        input-class="rounded-t-none"
        without-label
      />
    </div>
    <!-- End table editor pane -->

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
          ref="tableEls"
          class="table table-auto border-y w-fit h-fit"
          :class="{ 'table-zebra': table.is_zebra }"
          :data-id="table.id"
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
                'font-bold bg-neutral-800': cell.is_header,
                'tcs-select': isEdit && lastSelectedCell?.dataset.id == cell.id,
              }"
              :colspan="cell.colspan"
              :rowspan="cell.rowspan"
              :style="good_styles(cell.styles)"
              :data-id="cell.id"
            >
              {{ cell.content }}
            </td>
          </tr>
          <!-- End rows -->
        </table>

        <div class="flex flex-row flex-wrap justify-between">
          <span />

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
    <!-- # End modals -->
  </LanguageLayout>
</template>

<style scoped>
    .tcs-select {
        @apply bg-neutral-700
    }
</style>