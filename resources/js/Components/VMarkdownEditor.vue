<script setup>
import Editor from '@toast-ui/editor';
import { onMounted, ref, defineEmits, onUpdated, nextTick, watch, inject } from "vue";
import tableMergedCell from '@toast-ui/editor-plugin-table-merged-cell';
import colorSyntax from '@toast-ui/editor-plugin-color-syntax';

import '@toast-ui/editor/dist/toastui-editor.css';
import 'tui-color-picker/dist/tui-color-picker.css';
import '@toast-ui/editor-plugin-color-syntax/dist/toastui-editor-plugin-color-syntax.css';
import '@toast-ui/editor-plugin-table-merged-cell/dist/toastui-editor-plugin-table-merged-cell.css';
import '@toast-ui/editor/dist/theme/toastui-editor-dark.css';

/* global route */

const { modelValue, label } = defineProps({
    modelValue: {
        type: [String, null],
        default: '',
    },
    label: {
        type: String,
        default: null,
    },
});
const language = inject('language')

const el = ref(null);

const emits = defineEmits([ 'update:modelValue' ]);

const editor = {
    value: null,
}

onMounted(function () {
    editor.value = new Editor({
        el: el.value,
        initialValue: modelValue ?? '',
        theme: 'dark',
        plugins: [ tableMergedCell, colorSyntax ],
        usageStatistics: false,
        height: 'auto',
        autofocus: false,
        hooks: {
            addImageBlobHook: (file, cb) => {
                window.axios.post(route('languages.image', { code: language.id }), { image: file }, {
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'multipart/form-data'
                    },
                }).then((response) => {
                    const path = response.data.path;
                    const split = path.split('/');
                    cb(encodeURI(path), split[split.length - 1]);
                });
            }
        },
    });

    const cmd = function (_, state, dispatch) {
        if (!state.selection.empty) {
            const slice = state.selection.content();
            const textContent = slice.content.textBetween(0, slice.content.size);
            const text = state.schema.text(window.x2i(textContent));

            const tr = state.tr;

            tr.replaceSelectionWith(text);

            dispatch(tr);

            return true
        }
        return false;
    }
    editor.value.addCommand('markdown', 'x2i', cmd);
    editor.value.addCommand('ww', 'x2i', cmd);

    editor.value.insertToolbarItem({ groupIndex: 0, itemIndex: 5 }, {
        name: 'X-SAMPA to IPA',
        tooltip: 'Convert X-SAMPA to IPA',
        command: 'x2i',
        text: 'X2I',
        className: 'toastui-editor-toolbar-icons',
        style: { backgroundImage: 'none', fontSize: 'larger' }
    });

    editor.value.on('keyup', function () {
        emits('update:modelValue', editor.value.getMarkdown());
    });
    editor.value.on('change', function () {
        emits('update:modelValue', editor.value.getMarkdown());
    });
})

</script>

<template>
  <div>
    <label
      v-if="label"
      class="label"
      for="about"
    >
      <span class="label-text">{{ label }}</span>
    </label>

    <article ref="el" />
  </div>
</template>

<style scoped>

</style>
