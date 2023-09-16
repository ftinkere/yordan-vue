<script setup>

import { ref } from "vue";

const { name, modelValue, required, errors } = defineProps({
    name: {
        type: String,
        default: null,
    },
    modelValue: {
        type: [ String, Object, Array, Boolean ],
        default: null,
    },
    required: {
        type: Boolean,
        default: null,
    },
    errors: {
        type: Object,
        default: null,
    }
});

const value = ref(modelValue);

const emits = defineEmits([ 'update:modelValue' ])
</script>

<template>
  <div class="form-control">
    <label class="label cursor-pointer flex gap-4">
      <input
        v-model="value"
        :name="name"
        type="checkbox"
        class="checkbox"
        :required="required"
        @change="emits('update:modelValue', value)"
      >
      <span class="label-text me-4">
        <slot />
      </span>
    </label>
    <label
      v-if="errors"
      class="label"
    >
      <span class="label-text-alt text-red-500">
        {{ errors }}
      </span>
      <span
        v-if="required"
        class="text-red-600"
      >
        *
      </span>
    </label>
  </div>
</template>

<style scoped>

</style>
