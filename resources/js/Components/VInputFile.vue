<script setup>
import {ref} from "vue";

const { name, label, id, inputClass, required, errors } = defineProps({
    name: {
        type: String,
        default: null,
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
});

const input = ref(null);

const emits = defineEmits(['update:modelValue']);

function update() {
    emits('update:modelValue', input.value.files[0])
    console.log(input.value.files[0]);
}
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
            ref="input"
            type="file"
            :name="name"
            class="input input-bordered"
            :class="inputClass"
            :required="required"
            @input="update"
        />
        <label v-if="errors" class="label">
            <span class="label-text-alt text-red-500">{{ errors }}</span>
        </label>
    </div>
</template>

<style scoped>

</style>
