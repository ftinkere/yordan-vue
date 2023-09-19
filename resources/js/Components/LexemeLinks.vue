<script setup>
  import { computed } from "vue";
  import { Link } from "@inertiajs/vue3"
  import LexemeShort from "@/Components/LexemeShort.vue";
  import _ from "lodash";
  import route from "ziggy-js";
  import LexemeLink from "@/Components/LexemeLink.vue";

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
      class="inline"
    >
      <span class="w-fit h-fit float-left">{{ type }}:&thinsp;&thinsp;&thinsp;&thinsp;</span>
      <div class="flex flex-col">
        <div
          v-for="link in arr"
          :key="link.id"
          class="inline"
        >
          <LexemeLink :link="link" />
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>