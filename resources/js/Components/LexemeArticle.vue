<script setup>
  import VMarkdownViewer from "@/Components/VMarkdownViewer.vue";
  import LexemeShort from "@/Components/LexemeShort.vue";
  import LexemeLinks from "@/Components/LexemeLinks.vue";

  defineProps({
      lexeme: {
          type: Object,
          required: true,
      },
      vocabula: {
          type: Object,
          default: null,
      },
      withoutShort: Boolean,
  })
</script>

<template>
  <div>
    <LexemeShort
      v-if="!withoutShort"
      :lexeme="lexeme"
      :one="vocabula?.lexemes.filter(lx => lx.group_number === lexeme.group_number).length === 1"
    />
    <VMarkdownViewer :content="lexeme.article" />
    <LexemeLinks class="ms-4 lg:ms-8" :links="lexeme.links" />
  </div>
</template>

<style scoped>

</style>