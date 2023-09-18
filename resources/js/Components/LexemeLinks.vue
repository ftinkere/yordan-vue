<script setup>
  import { computed } from "vue";
  import { Link } from "@inertiajs/vue3"
  import LexemeShort from "@/Components/LexemeShort.vue";
  import _ from "lodash";

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
      class="flex flex-row gap-2"
    >
      <span class="w-fit">{{ type }}:</span>
      <div class="flex flex-col">
        <div
          v-for="link in arr"
          :key="link.id"
          class="flex flex-row flex-wrap gap-1"
        >
          <Link
            class="link hover:link-info"
            href="route('vocables.view', { id:link.to.vocabula.id })"
          >
            {{ link.to.vocabula.vocabula }}
          </Link>
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