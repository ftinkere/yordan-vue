<script setup>

const { lexeme } = defineProps({
    lexeme: {
        type: Object,
        required: true,
    },
    one: Boolean,
})

</script>

<template>
  <div class="inline">
    <span class="inline gap-0">
      <span v-if="lexeme.group_number !== 0">{{ lexeme.group_number }}</span>
      <span v-if="lexeme.lexeme_number !== 0 && !one">.{{ lexeme.lexeme_number }}</span>
      &thinsp;
    </span>
    <span
      v-if="lexeme.style"
      class="italic"
    >
      ({{ lexeme.style }})
    </span>
    <span>{{ lexeme.short }}</span>
    <i class="inline">
      <span v-if="lexeme.grammatics?.filter(grammatic => grammatic.is_variable == false).length > 0">
        (
        <span class="inline-flex flex-row flew-wrap items-baseline gap-2">
          <span
            v-for="grammatic in lexeme.grammatics?.filter(grammatic => grammatic.is_variable == false)"
            :key="grammatic.id"
          >
            {{ grammatic.short }}
          </span>
        </span>
        )
      </span>
      <span v-if="lexeme.grammatics?.filter(grammatic => grammatic.is_variable == true).length > 0">
        [
        <span class="inline-flex flex-row flew-wrap items-baseline gap-2">
          <span
            v-for="grammatic in lexeme.grammatics?.filter(grammatic => grammatic.is_variable == true)"
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