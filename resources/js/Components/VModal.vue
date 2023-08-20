<script setup>
import { ref } from "vue";

const { id } = defineProps({
    buttonClass: {
        type: [String, Object, Array],
        default: "btn btn-sm btn-ghost"
    },
    modalBoxClass: {
        type: [String, Object, Array],
        default: ""
    },
})

const modal = ref(null);
</script>

<template>
    <button :class="buttonClass"
            @click="modal.showModal()"
    >
        <slot></slot>
    </button>

    <teleport to="body">
        <dialog class="modal" ref="modal">
            <div class="modal-box bg-neutral-900"
                 :class="modalBoxClass"
                 v-bind="$attrs"
            >
                <slot name="content"></slot>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button></button>
            </form>
        </dialog>
    </teleport>
</template>

<style scoped>

</style>
