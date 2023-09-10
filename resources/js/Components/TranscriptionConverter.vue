<script setup>
const { modelValue } = defineProps({
    modelValue: {
        type: [String, null],
        required: true
    },
});

const emits = defineEmits(['update:modelValue']);

const x2i = window.x2i;
</script>

<template>
    <label
        class="label"
        @click="emits('update:modelValue', modelValue ? x2i(modelValue) : '')"
    >
        <span class="label-text-alt link link-info">Конвертировать X-SAMPA в IPA</span>
    </label>
</template>

<style scoped>

</style>
