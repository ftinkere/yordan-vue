<script setup>
import { computed, onUpdated, ref } from "vue";

const { name, modelValue, value, required, errors } = defineProps({
    name: {
        type: String,
        default: null,
    },
    modelValue: {
        type: [ Boolean, Array, String, Number ],
        default: null,
    },
    value: {
        type: [ Boolean, String, Number ],
        default: true,
    },
    required: {
        type: Boolean,
        default: false,
    },
    errors: {
        type: Object,
        default: null,
    },
    inputClass: {
        type: String,
        default: "",
    },
    labelClass: {
        type: String,
        default: "",
    },
});

const emits = defineEmits([ 'update:modelValue' ])

const change = function (event) {
    if (event.target.checked) {
        emits('update:modelValue',
            Array.isArray(modelValue) ? [ ...modelValue, event.target.value ] : !!event.target.value )
    } else {
        emits('update:modelValue',
            Array.isArray(modelValue) ? modelValue.filter(el => el !== event.target.value) : false)
    }
}

</script>

<template>
  <div class="form-control">
    <label
      class="label cursor-pointer flex gap-2"
      :class="labelClass"
    >
      <input
        :model-value="modelValue"
        :checked="Array.isArray(modelValue) ? modelValue.includes(value) : modelValue === value"
        :value="value"
        :name="name"
        type="checkbox"
        class="checkbox"
        :class="inputClass"
        :required="required"
        @change="change"
      >
      <span class="label-text">
        <slot />
        <span
          v-if="required"
          class="inline-block fill-red-600 self-start"
        >
          <svg xmlns="http://www.w3.org/2000/svg" height="0.5em" viewBox="0 0 384 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M192 32c17.7 0 32 14.3 32 32V199.5l111.5-66.9c15.2-9.1 34.8-4.2 43.9 11s4.2 34.8-11 43.9L254.2 256l114.3 68.6c15.2 9.1 20.1 28.7 11 43.9s-28.7 20.1-43.9 11L224 312.5V448c0 17.7-14.3 32-32 32s-32-14.3-32-32V312.5L48.5 379.4c-15.2 9.1-34.8 4.2-43.9-11s-4.2-34.8 11-43.9L129.8 256 15.5 187.4c-15.2-9.1-20.1-28.7-11-43.9s28.7-20.1 43.9-11L160 199.5V64c0-17.7 14.3-32 32-32z"/></svg>
        </span>
      </span>
    </label>
    <label
      v-if="errors"
      class="label"
    >
      <span class="label-text-alt text-red-500">
        {{ errors }}
      </span>
    </label>
  </div>
</template>

<style scoped>

</style>
