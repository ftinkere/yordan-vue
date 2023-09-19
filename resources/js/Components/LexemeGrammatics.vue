<script setup>
    import VCheckbox from "@/Components/VCheckbox.vue";
    import { ref } from "vue";
    import _ from "lodash";

    const { modelValue, variables, language } = defineProps({
        modelValue: {
            type: Array,
            default: () => [],
        },
        variables: {
            type: Array,
            default: null,
        },
        language: {
            type: Object,
            required: true,
        },
    })

    const model = ref(modelValue)
    const variablesModel = ref(variables)

    const uid = _.uniqueId();

    const grammaticsForm = ref(null);

    const change = function (event) {
        if (event.target.checked && !model.value.includes(event.target.value)) {
            model.value.push(event.target.value)
        } else {
            model.value = model.value.filter(item => item !== event.target.value)
        }
        emit("update:modelValue", model.value)
    }
    const changeVariable = function (event) {
        if (event.target.checked && !variablesModel.value.includes(event.target.value)) {
            variablesModel.value.push(event.target.value)
        } else {
            variablesModel.value = variablesModel.value.filter(item => item !== event.target.value)
        }
        emit("update:variables", variablesModel.value)
    }

    const emit = defineEmits(['update:modelValue', 'update:variables'])

    // watch(model, update)
    // watch(variablesModel, update)
</script>

<template>
  <form ref="grammaticsForm">
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 grid-rows-[masonry] gap-y-2">
      <div
        v-for="grammatic in language.grammatics"
        :key="grammatic.id"
        class="flex flex-col gap-1 h-fit"
      >
        <h4 class="font-bold text-xl">
          {{ grammatic.name }}
        </h4>
        <div
          v-for="value in grammatic.grammatic_values"
          :key="value.id"
        >
          <VCheckbox
            :model-value="model"
            :name="`grammatic_${uid}[]`"
            :value="`${value.id}`"
            class="w-fit"
            label-class="py-0"
            input-class="checkbox-sm"
            @change="change($event)"
          >
            {{ value.value }} ({{ value.short }})
          </VCheckbox>
          <VCheckbox
            v-if="variables !== null"
            v-show="model.includes(`${value.id}`)"
            :model-value="variablesModel"
            :name="`grammatic_variable_${uid}[]`"
            :value="`${value.id}`"
            class="ms-4 w-fit"
            label-class="py-0"
            input-class="checkbox-sm"
            @change="changeVariable($event)"
          >
            Изменяемое?
          </VCheckbox>
        </div>
      </div>
    </div>
  </form>
</template>

<style scoped>

</style>