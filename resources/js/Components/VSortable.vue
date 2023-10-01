<script setup>
import { onMounted, ref } from "vue";
import Sortable from "sortablejs";

const { handle, filter, draggable, delay, delayOnTouchOnly, disabled } = defineProps({
    handle: {
        type: String,
        default: undefined,
    },
    filter: {
        type: String,
        default: undefined,
    },
    draggable: {
        type: String,
        default: undefined,
    },
    delay: {
        type: Number,
        default: 2000
    },
    delayOnTouchOnly: {
        type: Boolean,
        default: false
    },
    disabled: {
        type: Boolean,
        default: false,
    }
})

const items = ref(null)

const emits = defineEmits(['change'])

onMounted(() => {
    let sortable = Sortable.create(items.value, {
        delay,
        delayOnTouchOnly,
        disabled,
        handle,
        filter,
        draggable,

        onChange: event => emits('change', sortable, event)
    })
})
</script>

<template>
  <div ref="items">
    <slot />
  </div>
</template>

<style scoped>

</style>