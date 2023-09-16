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
        type: [ String, Object, Array ],
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
      <span class="label-text">{{ label }}</span>
      <span
        v-if="required"
        class="text-red-600"
      >
        *
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
