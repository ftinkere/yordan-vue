<script setup>
import _ from "lodash"

const { list } = defineProps({
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
        type: [ String, Object, Array, Number ],
        default: null,
    },
    id: {
        type: String,
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
    list: {
        type: [ Array, null ],
        default: null,
    },
    placeholder: {
        type: String,
        default: null,
    }
});

const listId = list !== null ? _.uniqueId('datalist_') : null;

const emits = defineEmits([ 'update:modelValue' ]);
</script>

<template>
  <div class="form-control">
    <label
      class="label flex flex-row justify-start"
      :for="id"
    >
      <span class="label-text inline-block">{{ label }}</span>
      <span
        v-if="required"
        class="inline-block fill-red-600 self-start"
      >
        <svg xmlns="http://www.w3.org/2000/svg" height="0.5em" viewBox="0 0 384 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M192 32c17.7 0 32 14.3 32 32V199.5l111.5-66.9c15.2-9.1 34.8-4.2 43.9 11s4.2 34.8-11 43.9L254.2 256l114.3 68.6c15.2 9.1 20.1 28.7 11 43.9s-28.7 20.1-43.9 11L224 312.5V448c0 17.7-14.3 32-32 32s-32-14.3-32-32V312.5L48.5 379.4c-15.2 9.1-34.8 4.2-43.9-11s-4.2-34.8 11-43.9L129.8 256 15.5 187.4c-15.2-9.1-20.1-28.7-11-43.9s28.7-20.1 43.9-11L160 199.5V64c0-17.7 14.3-32 32-32z"/></svg>
      </span>
    </label>

    <div class="w-full flex flex-row">
      <input
        v-if="type !== 'textarea'"
        :id="id"
        :value="modelValue"
        :type="type"
        :name="name"
        class="grow input input-bordered"
        :class="inputClass"
        :placeholder="placeholder"
        :list="listId"
        :required="required"
        @input="emits('update:modelValue', $event.target.value)"
      >

      <datalist
        v-if="list"
        :id="listId"
      >
        <option
          v-for="item in list"
          :key="item"
        >
          {{ item }}
        </option>
      </datalist>

      <textarea
        v-if="type === 'textarea'"
        :id="id"
        :value="modelValue"
        :name="name"
        class="grow input input-bordered"
        :class="inputClass"
        :required="required"
        @input="emits('update:modelValue', $event.target.value)"
      />

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
