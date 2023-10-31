<script setup>
    import LanguageLayout from "@/Layouts/LanguageLayout.vue";
    import { computed, nextTick, onMounted, onUpdated, ref } from "vue";
    import EditButton from "@/Components/EditButton.vue";
    import VSaveLoader from "@/Components/VSaveLoader.vue";
    import VFlashSuccess from "@/Components/VFlashSuccess.vue";
    import { useForm, usePage } from "@inertiajs/vue3";
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

    const _token = usePage().props.csrf_token

    const isEdit = ref(editMode)
    const flashSuccess = ref(null)

    // Edit table header
    const editTableModal = ref(null)
    const editTableFlash = ref(null)
    const editTableForm = useForm({
        id: 0,
        name: '',
        caption: '',
        _token
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
        _token
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
        _token
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
    const tableSelectors = ref([])
    const selectedCells = ref({})
    const selectedTable = ref(null)
    const lastSelectedCell = ref(null)
    const textColor = ref({ hex: '#ffffff' })
    const backgroundColor = ref({ hex: '#000000' })
    const colorUpdate = ref(true)

    function createTSC(tableEl) {
        const options = { deselectOutTableClick: false }
        const buffer = new TableCellSelector.Buffer()
        const tcs = new TableCellSelector(tableEl, options, buffer)
        tcs.onStartSelect = (ev, cell) => {
            selectedTable.value = tableEl
            tableSelectors.value.forEach(selector => selector.deselect())
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

        tableSelectors.value.push(tcs)
    }

    function updateTCS() {
        tableSelectors.value = []

        tableEls.value?.forEach(createTSC)
    }

    onMounted(updateTCS)
    onUpdated(updateTCS)

    const debs = ref({})

    function newDeb() {
        return _.debounce((table, cell) => {
            editTableCell(table, cell)
        }, 2000)
    }

    const lastSelectedContent = computed({
        get: () => {
            return lastSelectedCell.value?.innerText ?? ''
        },
        set: value => {
            if (lastSelectedCell.value) {
                lastSelectedCell.value.innerText = value
            }

            if (!debs[lastSelectedCell.value.dataset.id]) {
                debs[lastSelectedCell.value.dataset.id] = newDeb()
            }

            debs[lastSelectedCell.value.dataset.id](selectedTable.value, lastSelectedCell.value)
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
            _token
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
            _token
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

    // Add table
    const addTableModal = ref(null)
    const addTableForm = useForm({
        name: '',
        caption: '',
        height: 2,
        width: 2,
        _token
    })
    function addTable() {
        addTableForm.post(route('languages.tables.store', [language.id]), {
            preserveScroll: true,
            onSuccess: () => {
                addTableModal.value?.close()
                flashSuccess.value?.flash()
                addTableForm.reset()

                updateTCS()
            }
        })
    }

    // Add row
    const addTableRowForm = useForm({
        after: null,
        _token
    })
    function addTableRow() {
        if (!selectedTable.value || !lastSelectedCell.value) {
            return
        }

        if (lastSelectedCell.value) {
            addTableRowForm.after = parseInt(lastSelectedCell.value.dataset.rowOrder)
        }

        addTableRowForm.post(route('languages.tables.rows.store', [language.id, parseInt(selectedTable.value.dataset.id)]), {
            preserveScroll: true,
            onSuccess: () => {
                addTableRowForm.reset()
                flashSuccess.value?.flash()

                updateTCS()
            }
        })
    }

    // Add column
    const addTableColumnForm = useForm({
        after: null,
        _token
    })
    function addTableColumn() {
        if (!selectedTable.value || !lastSelectedCell.value) {
            return
        }

        if (lastSelectedCell.value) {
            addTableColumnForm.after = parseInt(lastSelectedCell.value.dataset.order)
        }

        addTableColumnForm.post(route('languages.tables.columns.store', [language.id, parseInt(selectedTable.value.dataset.id)]), {
            preserveScroll: true,
            onSuccess: () => {
                addTableColumnForm.reset()
                flashSuccess.value?.flash()

                updateTCS()
            }
        })
    }

    function updateMerge() {
        const cells = Object.keys(selectedCells.value)
        if (cells.length === 0) {
            return
        }

        useForm({
            cells,
            _token
        }).post(route('languages.tables.cells.merge', [language.id, selectedTable.value.dataset.id]), {
            preserveScroll: true,
            onSuccess: () => {
                flashSuccess.value?.flash()

                updateTCS()
            }
        })
    }
    function updateUnmerge() {
        if (!lastSelectedCell.value) {
            return
        }

        useForm({
            _token
        }).post(route('languages.tables.cells.unmerge', [language.id, selectedTable.value.dataset.id, lastSelectedCell.value.dataset.id]), {
            preserveScroll: true,
            onSuccess: () => {
                flashSuccess.value?.flash()

                updateTCS()
            }
        })
    }

    const borderColor = ref('#ffffff')
    const borderStyle = ref('solid')
    const borderSize = ref('1px')

    function borderChange(type) {
        let cells = Object.keys(selectedCells.value);
        if (cells.length === 0) {
            cells.push(lastSelectedCell.value.dataset.id);
        }

        useForm({
            cells,
            color: borderColor.value,
            style: borderStyle.value,
            size: borderSize.value,
            type,
            _token,
        }).post(route('languages.tables.cells.styles.border', [language.id, selectedTable.value.dataset.id]), {
            preserveScroll: true,
            onSuccess: () => {
                flashSuccess.value?.flash()

                updateTCS()
            }
        });
    }

    // const test = ref(null)
    // function onPaste(event) {
    //     console.log(event)
    //     const clipboardData = event.clipboardData
    //     const items = clipboardData.items
    //     const gsheets = JSON.parse(clipboardData.getData('application/x-vnd.google-docs-embedded-grid_range_clip+wrapped'))
    //     console.log(gsheets)
    //     test.value.innerHTML = JSON.parse(gsheets.data).grh
    //     for (const index in items) {
    //         const item = items[index]
    //         console.log(item)
    //     }
    // }

</script>

<template>
  <LanguageLayout :language="language">
    <div @paste="onPaste">
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
          <button
            class="btn btn-sm btn-primary"
            @click="test"
          >
            Тест
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
        <div>
          <div class="flex flex-row gap-1 items-center bg-neutral-800 rounded-lg rounded-b-none flex-wrap w-full">
            <!-- Bold -->
            <button
              class="btn btn-square btn-ghost tooltip tooltip-top flex justify-center disabled:fill-neutral-500"
              data-tip="Жирный"
              :disabled="lastSelectedCell === null && Object.keys(selectedCells).length === 0"
              @click="toggleStyle('font-weight', '700')"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                height="1.5em"
                viewBox="0 0 384 512"
              ><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M0 64C0 46.3 14.3 32 32 32H80 96 224c70.7 0 128 57.3 128 128c0 31.3-11.3 60.1-30 82.3c37.1 22.4 62 63.1 62 109.7c0 70.7-57.3 128-128 128H96 80 32c-17.7 0-32-14.3-32-32s14.3-32 32-32H48V256 96H32C14.3 96 0 81.7 0 64zM224 224c35.3 0 64-28.7 64-64s-28.7-64-64-64H112V224H224zM112 288V416H256c35.3 0 64-28.7 64-64s-28.7-64-64-64H224 112z" /></svg>
            </button>

            <!-- Italic -->
            <button
              class="btn btn-square btn-ghost tooltip tooltip-top flex justify-center disabled:fill-neutral-500"
              data-tip="Курсив"
              :disabled="lastSelectedCell === null && Object.keys(selectedCells).length === 0"
              @click="toggleStyle('font-style', 'italic')"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                height="1.5em"
                viewBox="0 0 384 512"
              ><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M128 64c0-17.7 14.3-32 32-32H352c17.7 0 32 14.3 32 32s-14.3 32-32 32H293.3L160 416h64c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H90.7L224 96H160c-17.7 0-32-14.3-32-32z" /></svg>
            </button>

            <!-- Text align -->
            <div class="dropdown dropdown-bottom">
              <button
                class="btn btn-square btn-ghost join-item tooltip tooltip-top flex justify-center disabled:fill-neutral-500"
                :disabled="lastSelectedCell === null && Object.keys(selectedCells).length === 0"
                data-tip="Выравнивание"
              >
                <svg v-if="lastSelectedCell?.style.textAlign === 'center'" xmlns="http://www.w3.org/2000/svg" height="1.5em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M352 64c0-17.7-14.3-32-32-32H128c-17.7 0-32 14.3-32 32s14.3 32 32 32H320c17.7 0 32-14.3 32-32zm96 128c0-17.7-14.3-32-32-32H32c-17.7 0-32 14.3-32 32s14.3 32 32 32H416c17.7 0 32-14.3 32-32zM0 448c0 17.7 14.3 32 32 32H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H32c-17.7 0-32 14.3-32 32zM352 320c0-17.7-14.3-32-32-32H128c-17.7 0-32 14.3-32 32s14.3 32 32 32H320c17.7 0 32-14.3 32-32z" /></svg>
                <svg v-else-if="lastSelectedCell?.style.textAlign === 'right'" xmlns="http://www.w3.org/2000/svg" height="1.5em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M448 64c0 17.7-14.3 32-32 32H192c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32zm0 256c0 17.7-14.3 32-32 32H192c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32zM0 192c0-17.7 14.3-32 32-32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32zM448 448c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32z" /></svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" height="1.5em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M288 64c0 17.7-14.3 32-32 32H32C14.3 96 0 81.7 0 64S14.3 32 32 32H256c17.7 0 32 14.3 32 32zm0 256c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H256c17.7 0 32 14.3 32 32zM0 192c0-17.7 14.3-32 32-32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32zM448 448c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32z" /></svg>

              </button>

              <div class="dropdown-content">
                <div class="form-control join join-horizontal bg-neutral-800 border">
                  <!-- Align left -->
                  <button
                    class="btn btn-square btn-ghost join-item tooltip tooltip-bottom flex justify-center disabled:fill-neutral-500"
                    data-tip="Выравнивание налево"
                    :disabled="lastSelectedCell === null && Object.keys(selectedCells).length === 0"
                    @click="updateStyle('text-align', 'left')"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" height="1.5em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M288 64c0 17.7-14.3 32-32 32H32C14.3 96 0 81.7 0 64S14.3 32 32 32H256c17.7 0 32 14.3 32 32zm0 256c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H256c17.7 0 32 14.3 32 32zM0 192c0-17.7 14.3-32 32-32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32zM448 448c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32z" /></svg>
                  </button>
                  <!-- Align center -->
                  <button
                    class="btn btn-square btn-ghost join-item tooltip tooltip-bottom flex justify-center disabled:fill-neutral-500"
                    data-tip="Выравнивание по центру"
                    :disabled="lastSelectedCell === null && Object.keys(selectedCells).length === 0"
                    @click="updateStyle('text-align', 'center')"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" height="1.5em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M352 64c0-17.7-14.3-32-32-32H128c-17.7 0-32 14.3-32 32s14.3 32 32 32H320c17.7 0 32-14.3 32-32zm96 128c0-17.7-14.3-32-32-32H32c-17.7 0-32 14.3-32 32s14.3 32 32 32H416c17.7 0 32-14.3 32-32zM0 448c0 17.7 14.3 32 32 32H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H32c-17.7 0-32 14.3-32 32zM352 320c0-17.7-14.3-32-32-32H128c-17.7 0-32 14.3-32 32s14.3 32 32 32H320c17.7 0 32-14.3 32-32z" /></svg>
                  </button>
                  <!-- Align right -->
                  <button
                    class="btn btn-square btn-ghost join-item tooltip tooltip-bottom flex justify-center disabled:fill-neutral-500"
                    data-tip="Выравнивание направо"
                    :disabled="lastSelectedCell === null && Object.keys(selectedCells).length === 0"
                    @click="updateStyle('text-align', 'right')"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" height="1.5em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M448 64c0 17.7-14.3 32-32 32H192c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32zm0 256c0 17.7-14.3 32-32 32H192c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32zM0 192c0-17.7 14.3-32 32-32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32zM448 448c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32z" /></svg>
                  </button>
                </div>
              </div>
            </div>

            <!-- Text vertical align -->
            <div class="dropdown dropdown-bottom">
              <button
                class="btn btn-square btn-ghost join-item tooltip tooltip-top flex justify-center disabled:fill-neutral-500"
                :disabled="lastSelectedCell === null && Object.keys(selectedCells).length === 0"
                data-tip="Вертикальное выравнивание"
              >
                <svg v-if="lastSelectedCell?.style.verticalAlign === 'bottom'" class="rotate-90" xmlns="http://www.w3.org/2000/svg" height="1.5em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M448 64c0 17.7-14.3 32-32 32H192c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32zm0 256c0 17.7-14.3 32-32 32H192c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32zM0 192c0-17.7 14.3-32 32-32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32zM448 448c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32z" /></svg>
                <svg v-else-if="lastSelectedCell?.style.verticalAlign === 'top'" class="rotate-90" xmlns="http://www.w3.org/2000/svg" height="1.5em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M288 64c0 17.7-14.3 32-32 32H32C14.3 96 0 81.7 0 64S14.3 32 32 32H256c17.7 0 32 14.3 32 32zm0 256c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H256c17.7 0 32 14.3 32 32zM0 192c0-17.7 14.3-32 32-32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32zM448 448c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32z" /></svg>
                <svg v-else class="rotate-90" xmlns="http://www.w3.org/2000/svg" height="1.5em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M352 64c0-17.7-14.3-32-32-32H128c-17.7 0-32 14.3-32 32s14.3 32 32 32H320c17.7 0 32-14.3 32-32zm96 128c0-17.7-14.3-32-32-32H32c-17.7 0-32 14.3-32 32s14.3 32 32 32H416c17.7 0 32-14.3 32-32zM0 448c0 17.7 14.3 32 32 32H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H32c-17.7 0-32 14.3-32 32zM352 320c0-17.7-14.3-32-32-32H128c-17.7 0-32 14.3-32 32s14.3 32 32 32H320c17.7 0 32-14.3 32-32z" /></svg>

              </button>

              <div class="dropdown-content">
                <div class="form-control join join-horizontal bg-neutral-800 border">
                  <!-- Align left -->
                  <button
                    class="btn btn-square btn-ghost join-item tooltip tooltip-bottom flex justify-center disabled:fill-neutral-500"
                    data-tip="Выравнивание вверх"
                    :disabled="lastSelectedCell === null && Object.keys(selectedCells).length === 0"
                    @click="updateStyle('vertical-align', 'top')"
                  >
                    <svg class="rotate-90" xmlns="http://www.w3.org/2000/svg" height="1.5em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M288 64c0 17.7-14.3 32-32 32H32C14.3 96 0 81.7 0 64S14.3 32 32 32H256c17.7 0 32 14.3 32 32zm0 256c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H256c17.7 0 32 14.3 32 32zM0 192c0-17.7 14.3-32 32-32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32zM448 448c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32z" /></svg>
                  </button>
                  <!-- Align center -->
                  <button
                    class="btn btn-square btn-ghost join-item tooltip tooltip-bottom flex justify-center disabled:fill-neutral-500"
                    data-tip="Выравнивание по центру"
                    :disabled="lastSelectedCell === null && Object.keys(selectedCells).length === 0"
                    @click="updateStyle('vertical-align', 'middle')"
                  >
                    <svg class="rotate-90" xmlns="http://www.w3.org/2000/svg" height="1.5em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M352 64c0-17.7-14.3-32-32-32H128c-17.7 0-32 14.3-32 32s14.3 32 32 32H320c17.7 0 32-14.3 32-32zm96 128c0-17.7-14.3-32-32-32H32c-17.7 0-32 14.3-32 32s14.3 32 32 32H416c17.7 0 32-14.3 32-32zM0 448c0 17.7 14.3 32 32 32H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H32c-17.7 0-32 14.3-32 32zM352 320c0-17.7-14.3-32-32-32H128c-17.7 0-32 14.3-32 32s14.3 32 32 32H320c17.7 0 32-14.3 32-32z" /></svg>
                  </button>
                  <!-- Align right -->
                  <button
                    class="btn btn-square btn-ghost join-item tooltip tooltip-bottom flex justify-center disabled:fill-neutral-500"
                    data-tip="Выравнивание вниз"
                    :disabled="lastSelectedCell === null && Object.keys(selectedCells).length === 0"
                    @click="updateStyle('vertical-align', 'bottom')"
                  >
                    <svg class="rotate-90" xmlns="http://www.w3.org/2000/svg" height="1.5em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M448 64c0 17.7-14.3 32-32 32H192c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32zm0 256c0 17.7-14.3 32-32 32H192c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32zM0 192c0-17.7 14.3-32 32-32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32zM448 448c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32z" /></svg>
                  </button>
                </div>
              </div>
            </div>

            <!-- Text color -->
            <div class="dropdown dropdown-bottom overflow-visible">
              <button
                tabindex="0"
                class="btn btn-square btn-ghost join-item tooltip tooltip-top flex justify-center disabled:fill-neutral-500"
                data-tip="Цвет текста"
                :disabled="lastSelectedCell === null && Object.keys(selectedCells).length === 0"
                @click="updateStyle('text-align', 'right')"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  height="1.5em"
                  viewBox="0 0 448 512"
                >
                  <!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                  <path d="M254 52.8C249.3 40.3 237.3 32 224 32s-25.3 8.3-30 20.8L57.8 416H32c-17.7 0-32 14.3-32 32s14.3 32 32 32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32h-1.8l18-48H303.8l18 48H320c-17.7 0-32 14.3-32 32s14.3 32 32 32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H390.2L254 52.8zM279.8 304H168.2L224 155.1 279.8 304z" />
                  <path
                    :fill="textColor.hex"
                    d="m32,435.16771c-17.7,0 -32,14.3 -32,32s14.3,32 32,32l384,0c17.7,0 32,-14.3 32,-32s-14.3,-32 -32,-32l-384,0z"
                  />
                </svg>
              </button>

              <div class="dropdown-content">
                <!--<Sketch-->
                <!--  v-if="colorUpdate"-->
                <!--  v-model="textColor"-->
                <!--  disable-alpha-->
                <!--  class="text-black"-->
                <!--  @update:model-value="updateTextColor"-->
                <!--/>-->
                <input
                  v-model="textColor.hex"
                  class="btn btn-square btn-ghost"
                  type="color"
                  @update:model-value="updateTextColor"
                >
              </div>
            </div>

            <!-- Background color -->
            <div class="dropdown dropdown-bottom overflow-visible">
              <button
                tabindex="0"
                class="btn btn-square btn-ghost join-item tooltip tooltip-top flex justify-center disabled:fill-neutral-500"
                data-tip="Цвет фона"
                :disabled="lastSelectedCell === null && Object.keys(selectedCells).length === 0"
                @click="updateStyle('text-align', 'right')"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  height="1.5em"
                  viewBox="0 0 576 512"
                >
                  <!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                  <path d="M41.4 9.4C53.9-3.1 74.1-3.1 86.6 9.4L168 90.7l53.1-53.1c28.1-28.1 73.7-28.1 101.8 0L474.3 189.1c28.1 28.1 28.1 73.7 0 101.8L283.9 481.4c-37.5 37.5-98.3 37.5-135.8 0L30.6 363.9c-37.5-37.5-37.5-98.3 0-135.8L122.7 136 41.4 54.6c-12.5-12.5-12.5-32.8 0-45.3zm176 221.3L168 181.3 75.9 273.4c-4.2 4.2-7 9.3-8.4 14.6H386.7l42.3-42.3c3.1-3.1 3.1-8.2 0-11.3L277.7 82.9c-3.1-3.1-8.2-3.1-11.3 0L213.3 136l49.4 49.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0zM512 512c-35.3 0-64-28.7-64-64c0-25.2 32.6-79.6 51.2-108.7c6-9.4 19.5-9.4 25.5 0C543.4 368.4 576 422.8 576 448c0 35.3-28.7 64-64 64z" />
                  <path
                    :fill="backgroundColor.hex"
                    d="m32,435.16771c-17.7,0 -32,14.3 -32,32s14.3,32 32,32l384,0c17.7,0 32,-14.3 32,-32s-14.3,-32 -32,-32l-384,0z"
                  />
                </svg>
              </button>

              <div class="dropdown-content flex flex-row bg-neutral-800 rounded-lg">
                <!--<Sketch-->
                <!--  v-if="colorUpdate"-->
                <!--  v-model="backgroundColor"-->
                <!--  class="text-black"-->
                <!--  @update:model-value="updateBackgroundColor"-->
                <!--/>-->
                <input
                  v-model="backgroundColor.hex"
                  class="btn btn-square btn-ghost"
                  type="color"
                  @update:model-value="updateBackgroundColor"
                >
                <button
                  class="btn btn-ghost"
                  @click="updateStyle('background-color', 'inherit')"
                >
                  Сбросить
                </button>
              </div>
            </div>

            <!-- Adding -->
            <div class="form-control join join-horizontal border-x">
              <!-- Add row -->
              <button
                class="btn btn-square btn-ghost join-item tooltip tooltip-top flex justify-center disabled:fill-neutral-500"
                data-tip="Добавить строку"
                :disabled="lastSelectedCell === null && Object.keys(selectedCells).length === 0"
                @click="addTableRow"
              >
                <svg viewBox="0 0 16 16" height="1.5em" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"/><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/><g id="SVGRepo_iconCarrier"> <rect id="icon-bound" width="16" height="16" fill="none"/> <path id="table-add-row" d="M11,12l2,0l0,2l-2,0l0,2l-2,0l0,-2l-2,0l0,-2l2,0l0,-2l2,0l0,2Zm-5,2l-5,-0c-0.265,0 -0.52,-0.105 -0.707,-0.293c-0.188,-0.187 -0.293,-0.442 -0.293,-0.707l-0,-10c-0,-0.552 0.448,-1 1,-1c2.577,0 11.423,-0 14,0c0.552,0 1,0.448 1,1c-0,1.916 0,8.084 -0,10c-0,0.552 -0.448,1 -1,1l-1,-0l0,-5l-12,-0l0,3l4,0l0,2Zm-4,-10l0,3l12,0l0,-3l-12,0Z"/> </g></svg>
              </button>
              <!-- Add column -->
              <button
                class="btn btn-square btn-ghost join-item tooltip tooltip-top flex justify-center disabled:fill-neutral-500"
                data-tip="Добавить столбец"
                :disabled="lastSelectedCell === null && Object.keys(selectedCells).length === 0"
                @click="addTableColumn"
              >
                <svg viewBox="0 0 16 16" height="1.5em" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"/><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/><g id="SVGRepo_iconCarrier"> <rect id="icon-bound" width="16" height="16" fill="none"/> <path id="table-add-column" d="M12,11l-0,2l2,0l-0,-2l2,0l-0,-2l-2,0l-0,-2l-2,0l-0,2l-2,0l-0,2l2,0Zm2,-5l-0,-5c0,-0.265 -0.105,-0.52 -0.293,-0.707c-0.187,-0.188 -0.442,-0.293 -0.707,-0.293l-10,-0c-0.552,-0 -1,0.448 -1,1c-0,2.577 -0,11.423 0,14c0,0.552 0.448,1 1,1c1.916,0 8.084,0 10,0c0.552,-0 1,-0.448 1,-1l-0,-1l-5,0l-0,-12l3,0l-0,4l2,0Zm-10,-4l3,0l-0,12l-3,0l-0,-12Z"/> </g></svg>
              </button>
            </div>

            <!-- Merging -->
            <div class="form-control join join-horizontal">
              <!-- Merge -->
              <button
                class="btn btn-square btn-ghost join-item tooltip tooltip-top flex justify-center disabled:fill-neutral-500"
                data-tip="Объединить ячейки"
                :disabled="lastSelectedCell === null && Object.keys(selectedCells).length === 0"
                @click="updateMerge"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  height="1.5em"
                  viewBox="0 0 448 512"
                ><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M160 64c0-17.7-14.3-32-32-32s-32 14.3-32 32v64H32c-17.7 0-32 14.3-32 32s14.3 32 32 32h96c17.7 0 32-14.3 32-32V64zM32 320c-17.7 0-32 14.3-32 32s14.3 32 32 32H96v64c0 17.7 14.3 32 32 32s32-14.3 32-32V352c0-17.7-14.3-32-32-32H32zM352 64c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7 14.3 32 32 32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H352V64zM320 320c-17.7 0-32 14.3-32 32v96c0 17.7 14.3 32 32 32s32-14.3 32-32V384h64c17.7 0 32-14.3 32-32s-14.3-32-32-32H320z" /></svg>
              </button>
              <!-- Unmerge -->
              <button
                class="btn btn-square btn-ghost join-item tooltip tooltip-top flex justify-center disabled:fill-neutral-500"
                data-tip="Разъединить ячейки"
                :disabled="lastSelectedCell === null && Object.keys(selectedCells).length === 0"
                @click="updateUnmerge"
              >
                <svg xmlns="http://www.w3.org/2000/svg" height="1.5em" viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V96c0-35.3-28.7-64-64-64H64zm88 64v64H64V96h88zm56 0h88v64H208V96zm240 0v64H360V96h88zM64 224h88v64H64V224zm232 0v64H208V224h88zm64 0h88v64H360V224zM152 352v64H64V352h88zm56 0h88v64H208V352zm240 0v64H360V352h88z" /></svg>
              </button>
            </div>

            <div class="join join-horizontal border-x">
              <!-- Borders -->
              <div class="dropdown dropdown-bottom">
                <button
                  class="btn btn-square btn-ghost join-item tooltip tooltip-top flex justify-center stroke-white disabled:fill-neutral-500 disabled:stroke-neutral-500"
                  data-tip="Границы"
                  :disabled="lastSelectedCell === null && Object.keys(selectedCells).length === 0"
                >
                  <svg viewBox="0 0 24 24" height="2.2em" fill="none" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.768"></g><g> <path d="M12 4V20M4 12H20M6 20H18C19.1046 20 20 19.1046 20 18V6C20 4.89543 19.1046 4 18 4H6C4.89543 4 4 4.89543 4 6V18C4 19.1046 4.89543 20 6 20Z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                </button>

                <div class="dropdown-content">
                  <div class="p-1 flex flex-col bg-neutral-800 border">
                    <!-- Первая строка-->
                    <div class="flex flex-row items-center">
                      <!-- All -->
                      <button
                        class="btn btn-square btn-ghost flex justify-center stroke-white"
                        @click="borderChange('all')"
                      >
                        <svg viewBox="0 0 24 24" height="2.2em" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.768"></g><g> <path d="M12 4V20M4 12H20M6 20H18C19.1046 20 20 19.1046 20 18V6C20 4.89543 19.1046 4 18 4H6C4.89543 4 4 4.89543 4 6V18C4 19.1046 4.89543 20 6 20Z" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                      </button>
                      <!-- Inner -->
                      <button
                        class="btn btn-square btn-ghost flex justify-center stroke-white"
                        @click="borderChange('inner')"
                      >
                        <svg viewBox="0 0 24 24" height="2.2em" fill="none" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round"></g><g> <path d="M12 4V12M12 12V20M12 12H4M12 12H20M20 7.99999V7.98999M20 16V15.99M20 20V19.99M20 3.99999V3.98999M16 20V19.99M16 3.99999V3.98999M8 20V19.99M8 3.99999V3.98999M4 7.99999V7.98999M4 16V15.99M4 20V19.99M4 3.99999V3.98999" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                      </button>
                      <!-- Horizontal -->
                      <button
                        class="btn btn-square btn-ghost flex justify-center stroke-white"
                        @click="borderChange('horizontals')"
                      >
                        <svg viewBox="0 0 24 24" height="2.2em" fill="none" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round"></g><g> <path d="M16 4H16.01M12 4H12.01M12 8H12.01M12 16H12.01M12 20H12.01M16 20H16.01M8 4H8.01M4 4H4.01M4 8H4.01M4 16H4.01M4 20H4.01M8 20H8.01M20 4H20.01M20 8H20.01M20 16H20.01M20 20H20.01M20 12H4" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                      </button>
                      <!-- Vertical -->
                      <button
                        class="btn btn-square btn-ghost justify-center stroke-white"
                        @click="borderChange('verticals')"
                      >
                        <svg viewBox="0 0 24 24" height="2.2em" fill="none" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round"></g><g> <path d="M16 4H16.01M16 12H16.01M16 20H16.01M8 4H8.01M8 12H8.01M4 4H4.01M4 8H4.01M4 12H4.01M4 16H4.01M4 20H4.01M8 20H8.01M20 4H20.01M20 8H20.01M20 12H20.01M20 16H20.01M20 20H20.01M12.0117 4V20" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                      </button>
                      <!-- Outer -->
                      <button
                        class="btn btn-square btn-ghost flex justify-center stroke-white"
                        @click="borderChange('outer')"
                      >
                        <svg viewBox="0 0 24 24" height="2.2em" fill="none" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round"></g><g> <path d="M12 12H12.01M12 16H12.01M12 8H12.01M16 12H16.01M8 12H8.01M7.2 20H16.8C17.9201 20 18.4802 20 18.908 19.782C19.2843 19.5903 19.5903 19.2843 19.782 18.908C20 18.4802 20 17.9201 20 16.8V7.2C20 6.0799 20 5.51984 19.782 5.09202C19.5903 4.71569 19.2843 4.40973 18.908 4.21799C18.4802 4 17.9201 4 16.8 4H7.2C6.0799 4 5.51984 4 5.09202 4.21799C4.71569 4.40973 4.40973 4.71569 4.21799 5.09202C4 5.51984 4 6.07989 4 7.2V16.8C4 17.9201 4 18.4802 4.21799 18.908C4.40973 19.2843 4.71569 19.5903 5.09202 19.782C5.51984 20 6.07989 20 7.2 20Z" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                      </button>
                    </div>

                    <!-- Вторая строка-->
                    <div class="flex flex-row items-center">
                      <!-- Left -->
                      <button
                        class="btn btn-square btn-ghost flex justify-center stroke-white"
                        @click="borderChange('left')"
                      >
                        <svg viewBox="0 0 24 24" height="2.2em" fill="none" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round"></g><g> <path d="M16 4H16.01M16 12H16.01M12 4H12.01M12 8H12.01M12 12H12.01M12 16H12.01M12 20H12.01M16 20H16.01M8 4H8.01M8 12H8.01M8 20H8.01M20 4H20.01M20 8H20.01M20 12H20.01M20 16H20.01M20 20H20.01M4 4V20" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                      </button>
                      <!-- Top -->
                      <button
                        class="btn btn-square btn-ghost flex justify-center stroke-white"
                        @click="borderChange('top')"
                      >
                        <svg viewBox="0 0 24 24" height="2.2em" fill="none" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round"></g><g> <path d="M16 12H16.01M12 8H12.01M12 12H12.01M12 16H12.01M12 20H12.01M16 20H16.01M8 12H8.01M4 8H4.01M4 12H4.01M4 16H4.01M4 20H4.01M8 20H8.01M20 8H20.01M20 12H20.01M20 16H20.01M20 20H20.01M20 4H4" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                      </button>
                      <!-- Right -->
                      <button
                        class="btn btn-square btn-ghost flex justify-center stroke-white"
                        @click="borderChange('right')"
                      >
                        <svg viewBox="0 0 24 24" height="2.2em" fill="none" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round"></g><g> <path d="M8.01195 4H8.00195M8.01195 12H8.00195M12.012 4H12.002M12.012 8H12.002M12.012 12H12.002M12.012 16H12.002M12.012 20H12.002M8.01195 20H8.00195M16.012 4H16.002M16.012 12H16.002M16.012 20H16.002M4.01195 4H4.00195M4.01195 8H4.00195M4.01195 12H4.00195M4.01195 16H4.00195M4.01195 20H4.00195M20.012 4V20" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                      </button>
                      <!-- Bottom -->
                      <button
                        class="btn btn-square btn-ghost flex justify-center stroke-white"
                        @click="borderChange('bottom')"
                      >
                        <svg viewBox="0 0 24 24" height="2.2em" fill="none" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round"></g><g> <path d="M20 20H4M16 4H16.01M16 12H16.01M12 4H12.01M12 8H12.01M12 12H12.01M12 16H12.01M8 4H8.01M8 12H8.01M4 4H4.01M4 8H4.01M4 12H4.01M4 16H4.01M20 4H20.01M20 8H20.01M20 12H20.01M20 16H20.01" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                      </button>
                      <!-- None -->
                      <button
                        class="btn btn-square btn-ghost flex justify-center stroke-white"
                        @click="borderChange('none')"
                      >
                        <svg viewBox="0 0 24 24" height="2.2em" fill="none" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round"></g><g> <path d="M16 4H16.01M16 12H16.01M12 4H12.01M12 8H12.01M12 12H12.01M12 16H12.01M12 20H12.01M16 20H16.01M8 4H8.01M8 12H8.01M4 4H4.01M4 8H4.01M4 12H4.01M4 16H4.01M4 20H4.01M8 20H8.01M20 4H20.01M20 8H20.01M20 12H20.01M20 16H20.01M20 20H20.01" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                      </button>
                      <!-- Reset -->
                      <button
                        class="btn btn-ghost"
                        @click="borderChange('reset')"
                      >
                        Сбросить
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- End borders -->

              <!-- Color -->
              <div class="dropdown dropdown-bottom">
                <button
                  class="btn btn-square btn-ghost join-item rounded-s-none tooltip tooltip-top flex justify-center stroke-white disabled:fill-neutral-500 disabled:stroke-neutral-500"
                  data-tip="Цвет границы"
                  :disabled="lastSelectedCell === null && Object.keys(selectedCells).length === 0"
                >
                  <svg class="ms-1"
                       xmlns="http://www.w3.org/2000/svg"
                       height="1.5em"
                       viewBox="0 0 512 512">
                    <!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                    <path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"/>
                    <path
                      :fill="borderColor"
                      d="m32,435.16771c-17.7,0 -32,14.3 -32,32s14.3,32 32,32l384,0c17.7,0 32,-14.3 32,-32s-14.3,-32 -32,-32l-384,0z"
                    />
                  </svg>
                </button>

                <div class="dropdown-content">
                  <!--<Sketch-->
                  <!--  v-model="borderColor"-->
                  <!--  disable-alpha-->
                  <!--  class="text-black"-->
                  <!--/>-->
                  <input
                    v-model="borderColor"
                    class="btn btn-square btn-ghost"
                    type="color"
                  >
                </div>
              </div>
              <!-- Border style -->
              <button
                class="btn btn-square btn-ghost join-item tooltip tooltip-top flex justify-center stroke-white disabled:fill-neutral-500 disabled:stroke-neutral-500"
                data-tip="Стиль границы"
                :disabled="lastSelectedCell === null && Object.keys(selectedCells).length === 0"
              >
                <svg class="ms-1" viewBox="0 0 20 20" height="1.5em" xmlns="http://www.w3.org/2000/svg">
                  <g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round"></g><g><path d="M0 0h20v5H0V0zm0 7h20v4H0V7zm0 6h20v3H0v-3zm0 5h20v2H0v-2z"></path></g>
                </svg>
              </button>
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
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    height="1em"
                    viewBox="0 0 512 512"
                  ><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z" /></svg>
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
                    'tcs-select': isEdit && (lastSelectedCell?.dataset.id == cell.id || selectedCells[cell.id]),
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

          <div class="flex flex-row flex-wrap justify-between">
            <span />

            <!-- Button to delete table -->
            <button
              v-if="isEdit"
              class="btn btn-sm btn-error tooltip tooltip-left w-fit"
              data-tip="Удалить таблицу"
              @click="openDeleteTableModal(table)"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                height="1em"
                viewBox="0 0 448 512"
              ><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z" /></svg>
            </button>
            <!-- End button to delete table -->
          </div>

          <hr v-if="isEdit">
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

            <div class="grid grid-cols-1 sm:grid-cols-2 items-center">
              <VInput
                v-model="addTableForm.height"
                type="number"
                label="Высота в строках"
              />
              <VInput
                v-model="addTableForm.width"
                type="number"
                label="Ширина в столбцах"
              />
            </div>

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
    </div>
  </LanguageLayout>
</template>

<style scoped>
    .tcs-select {
        @apply bg-neutral-700 !important;
    }
</style>