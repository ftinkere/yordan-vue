<script setup>
import Viewer from '@toast-ui/editor/dist/toastui-editor-viewer';
import { onMounted, onUpdated, ref } from "vue";
import tableMergedCell from '@toast-ui/editor-plugin-table-merged-cell';

import '@toast-ui/editor/dist/theme/toastui-editor-dark.css'
import '@toast-ui/editor-plugin-table-merged-cell/dist/toastui-editor-plugin-table-merged-cell.css';

const { content } = defineProps({
    content: {
        type: [String, null],
        required: true,
    }
})

const el = ref(null)

const viewer = ref(null)

onMounted(function () {
    if (content) {
        viewer.value = new Viewer({
            el: el.value,
            initialValue: content,
            theme: 'dark',
            plugins: [tableMergedCell],
            usageStatistics: false,
            height: 'auto',
        });
    }
})

onUpdated(function () {
    viewer.value.setMarkdown(content)
})
</script>

<template>
  <article
    v-if="content"
    ref="el"
  />
</template>

<style scoped>
    article {
        text-indent: 2rem;
    }
</style>
