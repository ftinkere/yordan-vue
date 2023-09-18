<script setup>
import { computed } from "vue";

const { lexeme } = defineProps({
    lexeme: {
        type: Object,
        required: true,
    },
    one: Boolean,
})

const constant_grammatics = computed(() => {
    return lexeme.grammatics?.filter(grammatic => grammatic.is_variable == false)
})
const variable_grammatics = computed(() => {
    return lexeme.grammatics?.filter(grammatic => grammatic.is_variable == true)
})
</script>

<template>
  <div class="flex flex-row gap-1">
    <span class="inline gap-0">
      <span v-if="lexeme.group_number !== 0">{{ lexeme.group_number }}</span>
      <span v-if="lexeme.lexeme_number !== 0 && !one">.{{ lexeme.lexeme_number }}</span>
    </span>
    <span>{{ lexeme.short }}</span>
    <i>
      <span v-if="constant_grammatics.length > 0">
        (
        <span class="inline-flex flex-row flew-wrap items-baseline gap-2">
          <span
            v-for="grammatic in constant_grammatics"
            :key="grammatic.id"
          >
            {{ grammatic.short }}
          </span>
        </span>
        )
      </span>
      <span v-if="variable_grammatics.length > 0">
        [
        <span class="inline-flex flex-row flew-wrap items-baseline gap-2">
          <span
            v-for="grammatic in variable_grammatics"
            :key="grammatic.id"
          >
            {{ grammatic.short }}
          </span>
        </span>
        ]
      </span>
    </i>
  </div>
</template>

<style scoped>

</style>