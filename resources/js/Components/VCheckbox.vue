<script setup>
import { ref } from "vue";

const { name, modelValue, value, required, errors } = defineProps({
    name: {
        type: String,
        default: null,
    },
    modelValue: {
        type: [ String, Object, Array, Boolean ],
        default: null,
    },
    value: {
        type: [ String ],
        default: null,
    },
    required: {
        type: Boolean,
        default: null,
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

const model = ref(modelValue);

const emits = defineEmits([ 'update:modelValue' ])
</script>

<template>
  <div class="form-control">
    <label
      class="label cursor-pointer flex gap-2"
      :class="labelClass"
    >
      <input
        v-model="model"
        :value="value"
        :name="name"
        type="checkbox"
        class="checkbox"
        :class="inputClass"
        :required="required"
        @change="emits('update:modelValue', model)"
      >
      <span class="label-text">
        <slot />
        <span
          v-if="required"
          class="text-red-600"
        >
          *
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
