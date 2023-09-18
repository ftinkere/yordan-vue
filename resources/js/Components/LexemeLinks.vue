<script setup>
  import { computed } from "vue";
  import { Link } from "@inertiajs/vue3"
  import LexemeShort from "@/Components/LexemeShort.vue";
  import _ from "lodash";
  import route from "ziggy-js";

  const { links } = defineProps({
      links: {
          type: Array,
          required: true
      }
  })

  const linkByTypes = computed(() => {
      return _.groupBy(links, 'type')
  })
</script>

<template>
  <div class="mt-2">
    <div
      v-for="(arr, type) in linkByTypes"
      :key="type"
      class=" inline"
    >
      <span class="w-fit h-fit float-left">{{ type }}:&thinsp;&thinsp;&thinsp;&thinsp;</span>
      <div class="flex flex-col">
        <div
          v-for="link in arr"
          :key="link.id"
          class="inline"
        >
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
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>