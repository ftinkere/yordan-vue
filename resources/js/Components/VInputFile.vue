<script setup>
import { ref } from "vue";

const { name, label, inputClass, required, errors } = defineProps({
    name: {
        type: String,
        default: null,
    },
    label: {
        type: String,
    },
    modelValue: {
        type: [ String, Object, Array ],
        default: null,
    },
    inputClass: {
        type: [ String, Object, Array, Boolean ],
        default: '',
    },
    required: {
        type: Boolean,
        default: null,
    },
    errors: {
        type: String,
        default: null,
    },
});

const input = ref(null);

const emits = defineEmits([ 'update:modelValue' ]);

function update() {
    emits('update:modelValue', input.value.files[0])
}
</script>

<template>
  <div class="form-control">
    <label
      v-if="label"
      class="label"
    >
      <span class="label-text">{{ label }}</span>
    </label>
    <div class="flex flex-row">
      <input
        ref="input"
        type="file"
        :name="name"
        class="input input-bordered grow"
        :class="inputClass"
        :required="required"
        @input="update"
      >
      <slot name="button" />
    </div>

    <label
      v-if="errors"
      class="label"
    >
      <span class="label-text-alt text-red-500">{{ errors }}</span>
    </label>
  </div>
</template>

<style scoped>

</style>
