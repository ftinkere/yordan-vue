<script setup>
import _ from "lodash";
import { inject, ref } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import route from "ziggy-js";
import VModalN from "@/Components/VModalN.vue";
import LexemeLink from "@/Components/LexemeLink.vue";
import VFlashSuccess from "@/Components/VFlashSuccess.vue";
import VInputLexeme from "@/Components/VInputLexeme.vue";
import VInput from "@/Components/VInput.vue";
import VCheckbox from "@/Components/VCheckbox.vue";
import VModal from "@/Components/VModal.vue";


const { lexeme } = defineProps({
    lexeme: {
        type: Object,
        default: null,
    }
})
const _token = usePage().props.csrf_token
const language = inject('language')

const emits = defineEmits([ 'close', 'success' ])

const linksModal = ref(null)
const flashSuccess = ref(null)

const isLinkReverse = ref(false)
const linkForm = useForm({
    type: '',
    to_lexeme_id: null,
    comment: '',
    _token,
})
const reverseLinkForm = useForm({
    type: null,
    comment: null,
    _token,
})

const addLink = function() {
    if (lexeme) {
        linkForm.transform(data => {
            if (isLinkReverse.value) {
                data.reverse = reverseLinkForm.data()
            }
            return data
        }).post(route('languages.vocabulary.lexemes.links.store', {
            code: language.id,
            vocabula: lexeme.vocabula.id,
            lexeme: lexeme.id
        }), {
            preserveScroll: true,
            onSuccess: () => {
                flashSuccess.value?.flash()
                emits('success')
            },
        })
    }
}

const deleteLink = function (link) {
    if (lexeme) {
        linkForm.delete(route('languages.vocabulary.lexemes.links.delete', {
            code: language.id,
            vocabula: lexeme.vocabula.id,
            lexeme: lexeme.id,
            link: link.id,
            _token,
        }), {
            preserveScroll: true,
            onSuccess: () => {
                flashSuccess.value?.flash()
                emits('success')
            }
        })
    }
}

function close() {
    linksModal.value?.close()
    emits('close')
}

function open() {
    linksModal.value?.open()
}

defineExpose({ close, open })
</script>

<template>
  <VModalN
    ref="linksModal"
    header="Связи"
    class="max-w-screen-md w-full"
  >
    <template #content>
      <div class="flex flex-col">
        <div
          v-for="(arr, type) in _.groupBy(lexeme?.links, 'type')"
          :key="type"
          class="inline"
        >
          <span class="w-fit h-fit float-left">{{ type }}:&thinsp;&thinsp;&thinsp;&thinsp;</span>
          <div class="flex flex-col">
            <div
              v-for="link in arr"
              :key="link.id"
              class="flex flex-row flex-wrap gap-4"
            >
              <LexemeLink :link="link" />

              <button
                class="btn btn-xs btn-error border-neutral-800"
                @click="deleteLink(link)"
              >
                Удалить связь
              </button>
            </div>
          </div>
        </div>

        <VModal
          header="Добавить связь"
          button-class="mt-4 btn btn-sm btn-primary w-fit"
        >
          Добавить связь

          <template #postHeader>
            <button
              class="btn btn-sm btn-success"
              @click="addLink"
            >
              Добавить
            </button>

            <VFlashSuccess ref="flashSuccess" />
          </template>

          <template #content>
            <div class="flex flex-col">
              <VInputLexeme
                v-model="linkForm.to_lexeme_id"
                :language="language"
              />
              <VInput
                v-model="linkForm.type"
                label="Тип связи"
                :errors="linkForm.errors.type"
                required
              />
              <VInput
                v-model="linkForm.comment"
                label="Комментарий"
                :errors="linkForm.errors.comment"
              />
              <VCheckbox
                v-model="isLinkReverse"
                class="w-fit"
              >
                Добавить обратную связь?
              </VCheckbox>

              <div v-if="isLinkReverse">
                <VInput
                  v-model="reverseLinkForm.type"
                  label="Тип обратной связи"
                  required
                />
                <VInput
                  v-model="reverseLinkForm.comment"
                  label="Комментарий обратной связи"
                />
              </div>

              <button
                class="mt-4 btn btn-sm btn-success w-fit"
                @click="addLink"
              >
                Добавить
              </button>
            </div>
          </template>
        </VModal>
      </div>
    </template>
  </VModalN>
</template>

<style scoped>

</style>