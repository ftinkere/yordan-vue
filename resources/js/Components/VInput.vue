<script setup>
import {ref} from "vue";

const { name, type, label, modelValue, id, inputClass, required, errors, min, max, pattern } = defineProps({
    name: {
        type: String,
        required: true,
    },
    type: {
        type: String,
        default: 'text',
    },
    label: {
      type: String,
      required: true,
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
        <label class="label"
               :for="id"
        >
            <span class="label-text">{{ label }}</span>
        </label>
        <input :type="type"
               :id="id"
               :name="name"
               class="input input-bordered"
               :class="inputClass"
               :required="required"
               v-model="value"
               @input="emits('update:modelValue', value)"
               :min="min"
               :max="max"
               :pattern="pattern"
        />
        <label v-if="errors" class="label">
            <span class="label-text-alt text-red-500">{{ errors }}</span>
        </label>
    </div>
</template>

<style scoped>

</style>
