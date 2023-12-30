<script setup>
import VInputFile from "@/Components/VInputFile.vue";
import VModalN from "@/Components/VModalN.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import route from "ziggy-js";
import { computed, inject, ref } from "vue";

const { image, pushUrl, deleteUrl } = defineProps({
    image: {
        type: String,
    },
    alt: {
        type: String,
        default: 'Изображение',
    },
    height: {
        type: String,
        default: '10em',
    },
    pushUrl: {
        type: String,
    },
    deleteUrl: {
        type: String,
    },
    label: {
        type: String,
        default: 'Изображение',
    }
})
const _token = usePage().props.csrf_token

const imageModal = ref(null)

const imageForm = useForm({
    image: null,
    _token,
})

const pushImage = function() {
    imageForm.post(pushUrl, {
        onSuccess: () => { imageModal.value?.close() }
    })
}

const deleteImage = function() {
    imageForm.delete(deleteUrl, {
        onSuccess: () => { imageModal.value?.close() }
    })
}

const imagePreview = computed(() => {
    if (imageForm.image) {
        return URL.createObjectURL(imageForm.image)
    } else if (image) {
        return image
    }
    return null
})
</script>

<template>
  <VModalN
    ref="imageModal"
    class="max-w-screen-sm"
  >
    <template #opener="{ onClick }">
      <img
        v-if="image"
        class="mb-4 cursor-pointer"
        :src="image"
        :alt="alt"
        :style="{ height }"
        @click="onClick"
      >
      <button
        v-else
        class="mb-4 btn btn-sm btn-primary"
        @click="onClick"
      >
        Загрузить изображение
      </button>
    </template>

    <template #content>
      <div class="flex flex-col items-center">
        <img
          v-if="imagePreview"
          :src="imagePreview"
          :alt="alt"
          :style="{
            'max-height': '60%',
          }"
        >

        <VInputFile
          v-model="imageForm.image"
          :label="label"
          class="w-full"
          input-class="file-input file-input-bordered rounded-e-none"
          :errors="imageForm.errors.image"
        >
          <template #button>
            <button
              class="btn btn-success rounded-s-none"
              type="submit"
              @click="pushImage"
            >
              Отправить
            </button>
          </template>
        </VInputFile>

        <button
          v-if="image"
          class="mt-4 btn btn-sm btn-error self-start"
          @click="deleteImage"
        >
          Удалить
        </button>
      </div>
    </template>
  </VModalN>
</template>

<style scoped>

</style>