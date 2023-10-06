<script setup>
    import SoundString from "@/Components/SoundString.vue";

    const { sounds, modelValue, language } = defineProps({
        modelValue: {
            type: Number,
            default: -1,
        },
        sounds: {
            type: [Object, Array],
            default: null,
        },
        language: {
            type: Object,
            required: true,
        },
        label: {
            type: String,
            default: null,
        },
        noSelected: {
            type: String,
            default: null,
        },
        required: {
            type: Boolean,
            default: false,
        }
    })

    const emits = defineEmits(['update:modelValue', 'change'])
</script>

<template>
  <div class="form-control">
    <label class="label flex flex-row justify-start">
      <span class="label-text inline-block">
        {{ label }}
        <span
          v-if="required"
          class="inline-block fill-red-600 self-start"
        >
          <svg xmlns="http://www.w3.org/2000/svg" height="0.5em" viewBox="0 0 384 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M192 32c17.7 0 32 14.3 32 32V199.5l111.5-66.9c15.2-9.1 34.8-4.2 43.9 11s4.2 34.8-11 43.9L254.2 256l114.3 68.6c15.2 9.1 20.1 28.7 11 43.9s-28.7 20.1-43.9 11L224 312.5V448c0 17.7-14.3 32-32 32s-32-14.3-32-32V312.5L48.5 379.4c-15.2 9.1-34.8 4.2-43.9-11s-4.2-34.8 11-43.9L129.8 256 15.5 187.4c-15.2-9.1-20.1-28.7-11-43.9s28.7-20.1 43.9-11L160 199.5V64c0-17.7 14.3-32 32-32z"/></svg>
        </span>
      </span>
    </label>
    <select
      :modelValue="modelValue"
      class="select select-bordered"
      @change="emits('update:modelValue', parseInt($event.target.value)); emits('change')"
    >
      <option :value="-1">
        {{ noSelected }}
      </option>
      <option
        v-if="sounds"
        v-for="(sound,) in sounds"
        :key="sound.id"
        :value="sound.language_sounds?.filter(ls => ls.language_id === language.id)[0].id"
        :selected="modelValue === sound.language_sounds?.filter(ls => ls.language_id === language.id)[0].id"
      >
        <!--:value="sound.language_sounds?.filter(ls => ls.language_id === language.id)[0].id"-->
        <SoundString :sound="sound" />
      </option>
      <option
        v-else
        v-for="sound in language.language_sounds"
        :key="sound.id"
        :value="sound.id"
        :selected="modelValue === sound.id"
      >
        <SoundString :sound="sound.sound" />
      </option>
    </select>
  </div>
</template>

<style scoped>

</style>