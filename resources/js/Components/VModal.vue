<script setup>
import {onMounted, ref} from "vue";

const { id, buttonClass, modalBoxClass, headerClass } = defineProps({
    id: {
        type: String,
        default: null,
    },
    withoutX: {
        type: Boolean,
        default: false,
    },
    buttonClass: {
        type: [String, Object, Array],
        default: "btn btn-sm btn-ghost"
    },
    modalBoxClass: {
        type: [String, Object, Array],
        default: null
    },
    headerClass: {
        type: [String, Object, Array],
        default: "text-xl font-bold"
    },
    withoutButton: {
        type: Boolean,
        default: false,
    }
})

const modal = ref(null);

const emit = defineEmits(['close'])

onMounted(() => {
    modal.value.onclose = () => {
        emit('close');
    }
})

const close = function() {
    modal.value.close();
    emit('close');
}

const open = function() {
    modal.value.showModal();
}

defineExpose({
    close,
    open,
})
</script>

<template>
    <button
        v-if="!withoutButton"
        :class="buttonClass"
        @click="modal.showModal()"
    >
        <slot />
    </button>

    <teleport to="body">
        <dialog :id="id" ref="modal" class="modal">
            <div
                class="modal-box bg-neutral-900"
                v-bind="$attrs"
            >
                <div class="w-full flex flex-row justify-between">
                    <h3 :class="headerClass">
                        <slot name="header" />
                    </h3>
                    <form v-if="!withoutX" method="dialog">
                        <button class="absolute top-2 right-2 fill-white hover:fill-red-400 ms-auto btn btn-ghost btn-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" height="2em" viewBox="0 0 384 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg>
                        </button>
                    </form>
                </div>
                <slot name="content" />
            </div>
            <form method="dialog" class="modal-backdrop">
                <button />
            </form>
        </dialog>
    </teleport>
</template>

<style scoped>

</style>
