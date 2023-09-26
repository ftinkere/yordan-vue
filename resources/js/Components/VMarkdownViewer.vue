<script setup>
import Viewer from '@toast-ui/editor/dist/toastui-editor-viewer';
import {onMounted, ref} from "vue";
import tableMergedCell from '@toast-ui/editor-plugin-table-merged-cell';

import '@toast-ui/editor/dist/theme/toastui-editor-dark.css'
import '@toast-ui/editor-plugin-table-merged-cell/dist/toastui-editor-plugin-table-merged-cell.css';

const { content } = defineProps({
    content: {
        type: [String, null],
        required: true,
    }
});

const el = ref(null);

onMounted(function () {
    if (content) {
        new Viewer({
            el: el.value,
            initialValue: content,
            theme: 'dark',
            plugins: [tableMergedCell],
            usageStatistics: false,
            height: 'auto',
        });
    }
});
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
