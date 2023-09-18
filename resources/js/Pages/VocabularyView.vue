<script setup>
    import LanguageLayout from "@/Layouts/LanguageLayout.vue";
    import LexemeShort from "@/Components/LexemeShort.vue";
    import LexemeArticle from "@/Components/LexemeArticle.vue";
    import EditButton from "@/Components/EditButton.vue";
    import { ref } from "vue";
    import VSaveLoader from "@/Components/VSaveLoader.vue";
    import { useForm } from "@inertiajs/vue3";

    const { language, vocabula, editMode } = defineProps({
        language: {
          type: Object,
          required: true,
        },
        vocabula: {
            type: Object,
            required: true,
        },
        editMode: {
            type: Boolean,
            default: false,
        },
    })

    const isEdit = ref(editMode)

    const vocabulaForm = useForm({

    })

    console.log(vocabula);
</script>

<template>
  <LanguageLayout :language="language">
    <div class="card flex flex-col">
      <div class="flex flex-row justify-between">
        <h4 class="card-title m-2 font-bold text-3xl">
          <span>{{ vocabula.vocabula }}</span>
          <span class="font-normal text-md">/{{ vocabula.transcription }}/</span>
        </h4>

        <div>
          <VSaveLoader :is-save="!vocabulaForm.isDirty" />
          <EditButton @click="isEdit = true" />
        </div>
      </div>

      <div class="ms-6">
        <ul class="divide-y divide-neutral-700">
          <li class="mb-6 flex flex-col">
            <LexemeShort
              v-for="lexeme in vocabula.lexemes"
              :key="lexeme.id"
              :lexeme="lexeme"
              :one="vocabula.lexemes.filter(lx => lx.group_number === lexeme.group_number).length === 1"
            />
          </li>

          <template
            v-for="lexeme in vocabula.lexemes"
            :key="lexeme.id"
          >
            <li v-if="lexeme.short.length > 0 || lexeme.grammatics.length > 0">
              <article class="card">
                <div class="p-1">
                  <LexemeShort
                    :lexeme="lexeme"
                    :one="vocabula.lexemes.filter(lx => lx.group_number === lexeme.group_number).length === 1"
                  />
                  <LexemeArticle
                    :lexeme="lexeme"
                    without-short
                  />
                </div>
              </article>
            </li>
          </template>
        </ul>
      </div>
    </div>
  </LanguageLayout>
</template>

<style scoped>

</style>