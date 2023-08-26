<script setup>
import Editor from '@toast-ui/editor';
import { onMounted, ref, defineEmits } from "vue";
import tableMergedCell from '@toast-ui/editor-plugin-table-merged-cell';
import colorSyntax from '@toast-ui/editor-plugin-color-syntax';

import '@toast-ui/editor/dist/toastui-editor.css';
import 'tui-color-picker/dist/tui-color-picker.css';
import '@toast-ui/editor-plugin-color-syntax/dist/toastui-editor-plugin-color-syntax.css';
import '@toast-ui/editor-plugin-table-merged-cell/dist/toastui-editor-plugin-table-merged-cell.css';
import '@toast-ui/editor/dist/theme/toastui-editor-dark.css';

const { modelValue } = defineProps({
    modelValue: {
        type: String,
        required: true,
    },
});

const el = ref(null);

const emits = defineEmits(['update:modelValue']);

onMounted(function () {
    const editor = new Editor({
        el: el.value,
        initialValue: modelValue,
        theme: 'dark',
        plugins: [tableMergedCell, colorSyntax],
        usageStatistics: false,
        height: 'auto',
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
    editor.addCommand('markdown', 'x2i', cmd);
    editor.addCommand('ww', 'x2i', cmd);

    editor.insertToolbarItem({ groupIndex: 0, itemIndex: 5 }, {
        name: 'X-SAMPA to IPA',
        tooltip: 'Convert X-SAMPA to IPA',
        command: 'x2i',
        text: 'X2I',
        className: 'toastui-editor-toolbar-icons',
        style: { backgroundImage: 'none', fontSize: 'larger' }
    });

    editor.on('keyup', function () {
        emits('update:modelValue', editor.getMarkdown());
    });
    editor.on('change', function () {
        emits('update:modelValue', editor.getMarkdown());
    });
});

</script>

<template>
    <article ref="el"></article>
</template>

<style scoped>

</style>