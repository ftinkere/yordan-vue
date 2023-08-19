<script setup>

import {ref} from "vue";

const { name, id, modelValue, required, errors } = defineProps({
   name: {
       type: String,
       required: true,
   },
    id: {
        type: String,
        default: null,
    },
    modelValue: {
        type: [String, Object, Array, Boolean],
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

const emits = defineEmits(['update:modelValue'])
</script>

<template>
    <div class="form-control">
        <label class="label cursor-pointer flex gap-4">
            <input :name="name"
                   :id="id"
                   type="checkbox"
                   class="checkbox"
                   v-model="value"
                   @change="emits('update:modelValue', value)"
                   :required="required" />
            <span class="label-text me-4">
                <slot></slot>
            </span>
        </label>
        <label v-if="errors" class="label">
            <span class="label-text-alt text-red-500">{{ errors }}</span>
        </label>
    </div>
</template>

<style scoped>

</style>
