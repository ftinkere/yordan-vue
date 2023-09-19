<script setup>
  import { computed } from "vue";
  import { Link } from "@inertiajs/vue3"
  import LexemeShort from "@/Components/LexemeShort.vue";
  import _ from "lodash";
  import route from "ziggy-js";

  const { links } = defineProps({
      link: {
          type: Object,
          required: true
      }
  })

</script>

<template>
  <div>
    <Link
      class="link hover:link-info"
      :href="route('languages.vocabulary.view', { code: link.to.vocabula.language_id, vocabula: link.to.vocabula.id })"
    >
      {{ link.to.vocabula.vocabula }}
    </Link>
    <span>&thinsp;&thinsp;</span>
    <LexemeShort :lexeme="link.to" />
    <span v-if="link.comment">
      // {{ link.comment }}
    </span>
  </div>
</template>

<style scoped>

</style>