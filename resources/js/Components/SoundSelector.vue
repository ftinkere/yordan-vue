<script setup>
    import SoundString from "@/Components/SoundString.vue";

    const { sounds, modelValue, language } = defineProps({
        modelValue: {
            type: Number,
            default: -1,
        },
        sounds: {
            type: [Object, Array],
            required: true,
        },
        language: {
            type: Object,
            required: true,
        }
    })

    const emits = defineEmits(['update:modelValue', 'change'])
</script>

<template>
  <div class="form-control">
    <label class="label"><span class="label-text">Аллофон?</span></label>
    <select
      :modelValue="modelValue"
      class="select select-bordered"
      @change="emits('update:modelValue', parseInt($event.target.value)); emits('change')"
    >
      <option :value="-1">
        Не аллофон
      </option>
      <option
        v-for="(sound,) in sounds"
        :key="sound.id"
        :selected="modelValue === sound.language_sounds?.filter(ls => ls.language_id === language.id)[0]?.id"
        :value="sound.language_sounds?.filter(ls => ls.language_id === language.id)[0].id"
      >
        <SoundString :sound="sound" />
      </option>
    </select>
  </div>
</template>

<style scoped>

</style>