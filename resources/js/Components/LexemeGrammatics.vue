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

    // const model = ref(modelValue)
    // const variablesModel = ref(variables)

    const uid = _.uniqueId();

    const grammaticsForm = ref(null);

    const getData = () => {
        if (grammaticsForm.value === null) {
            return [];
        }
        return (new FormData(grammaticsForm.value)).getAll(`grammatic_${uid}[]`)
    }
    const getDataVariables = () => {
        if (grammaticsForm.value === null) {
            return [];
        }
        return (new FormData(grammaticsForm.value)).getAll(`grammatic_variable_${uid}[]`)
    }

    const emit = defineEmits(['update:modelValue', 'update:modelVariables'])

    const update = function () {
        emit("update:modelValue", getData())
        emit("update:variables", getDataVariables())
    }

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

            :name="`grammatic_${uid}[]`"
            :value="`${value.id}`"
            class="w-fit"
            label-class="py-0"
            input-class="checkbox-sm"
            @change="update"
          >
            {{ value.value }} ({{ value.short }})
          </VCheckbox>
          <VCheckbox
            v-if="variables !== null"
            v-show="modelValue.includes(`${value.id}`)"

            :name="`grammatic_variable_${uid}[]`"
            :value="`${value.id}`"
            class="ms-4 w-fit"
            label-class="py-0"
            input-class="checkbox-sm"
            @change="update"
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