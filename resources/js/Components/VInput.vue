<script setup>
import {ref} from "vue";

const { name, type, label, modelValue, id, inputClass, required, errors, min, max, pattern } = defineProps({
    name: {
        type: String,
        default: null,
    },
    type: {
        type: String,
        default: 'text',
    },
    label: {
      type: String,
      default: null,
    },
    modelValue: {
        type: [String, Object, Array],
        default: null,
    },
    id: {
        type: String,
        default: null,
    },
    inputClass: {
        type: [String, Object, Array, Boolean],
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
    min: {
        type: Number,
        default: null,
    },
    max: {
        type: Number,
        default: null,
    },
    pattern: {
        type: String,
        default: null,
    },
});

const value = ref(modelValue);

const emits = defineEmits(['update:modelValue']);
</script>

<template>
    <div class="form-control">
        <label
            class="label"
            :for="id"
        >
            <span class="label-text">{{ label }}</span>
        </label>
        <input
            :id="id"
            v-model="value"
            :type="type"
            :name="name"
            class="input input-bordered"
            :class="inputClass"
            :required="required"
            :min="min"
            :max="max"
            :pattern="pattern"
            @input="emits('update:modelValue', value)"
        >
        <label v-if="errors" class="label">
            <span class="label-text-alt text-red-500">{{ errors }}</span>
        </label>
    </div>
</template>

<style scoped>

</style>
