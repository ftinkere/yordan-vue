<script setup>
import { onMounted, onUpdated, ref } from "vue";
import XButton from "@/Components/buttons/XButton.vue";
import { watchTriggerable } from "@vueuse/core";

const { header, showX, withBackdrop } = defineProps({
    header: {
        type: String,
        default: null
    },
    showX: {
        type: Boolean,
        default: true,
    },
    withBackdrop: {
        type: Boolean,
        default: true,
    },
})

const modal = ref(null)

const emits = defineEmits([ 'close' ])

onMounted(() => {
    modal.value.onclose = () => {
        emits('close');
    }
})

function close() {
    modal.value?.close();
    emits('close');
}

function open() {
    modal.value?.showModal();
}

defineExpose({
    close,
    open,
})

</script>

<template>
  <slot
    name="opener"
    :on-click="open"
  />

  <teleport to="body">
    <dialog
      ref="modal"
      class="modal"
    >
      <div
        class="modal-box bg-neutral-900 max-w-screen-lg"
        v-bind="$attrs"
      >
        <div class="w-full flex flex-row flex-wrap justify-between">
          <h3
            class="me-10 flex flex-row flex-wrap items-center gap-4 text-xl font-bold"
            :class="{
              'mb-4': !!header,
              'mb-10': !header,
            }"
          >
            {{ header }}
            <slot name="postHeader"/>
          </h3>
          <form
            v-if="showX"
            method="dialog"
          >
            <XButton/>
          </form>
        </div>

        <slot name="content"/>
      </div>

      <form
        v-if="withBackdrop"
        method="dialog"
        class="modal-backdrop"
      >
        <button/>
      </form>
    </dialog>
  </teleport>
</template>

<style scoped>

</style>