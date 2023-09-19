<script setup>
    import VInput from "@/Components/VInput.vue";
    import { useForm } from "@inertiajs/vue3";
    import { computed, nextTick, ref, watch } from "vue";
    import route from "ziggy-js";
    import _ from "lodash";

    const { language } = defineProps({
        language: {
            type: Object,
            required: true,
        },
        modelValue: {
            type: [ Number, String ],
            default: null,
        },
    })
    const emits = defineEmits(['update:modelValue'])

    const select = ref(null)

    const results = ref([])

    const searchForm = useForm({
        q: ''
    })

    const updateResults = function () {
        fetch(route('languages.vocabulary.lexemes.search', { code: language.id, q: searchForm.q }), {
            method: 'GET',
        }).then((response) => response.json())
            .then((data) => {
                results.value = data
                nextTick(() => select.value.dispatchEvent(new Event('change')))
            })
    }

    watch(computed(() => searchForm.q), _.debounce(updateResults, 1000))

    const firstId = computed(() => {
        if (results.value.length > 0) {
            return results.value[0].id
        }
        return 0
    })
</script>

<template>
  <div>
    <VInput
      v-model="searchForm.q"
      type="search"
      label="Поиск лексемы"
      placeholder="Введите вокабулу для поиска"
    />

    <select
      ref="select"
      class="select select-bordered w-full"
      @change="emits('update:modelValue', $event.target.value)"
    >
      <option
        v-if="results.length === 0"
        :value="0"
        selected
      >
        Выберете лексему
      </option>
      <option
        v-for="result in results"
        :key="result.id"
        :value="result.id"
        :selected="result.id === firstId"
      >
        {{ result.view }}
      </option>
    </select>
  </div>
</template>

<style scoped>

</style>