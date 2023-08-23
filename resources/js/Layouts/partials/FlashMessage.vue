<script setup>

import { ref } from "vue";
import { router } from "@inertiajs/vue3";

const { message } = defineProps({
    message: {
        type: Object,
        default: null,
    }
});

const isShow = ref(true);

router.on('success', () => {
    isShow.value = true;
})
</script>

<template>
    <div
        v-if="message && isShow"
        class="container-fluid rounded-b-lg"
        :class="{ 'bg-green-900': message.type === 'success', 'bg-red-900': message.type === 'error' }"
    >
        <div class="container px-6 flex flex-row justify-between items-center">
            <span>{{ message.message }}</span>
            <button
                class="float-right btn btn-ghost btn-sm btn-circle fill-white"
                @click="isShow = false"
            >
                <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 384 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z" /></svg>
            </button>
        </div>
    </div>
</template>

<style scoped>

</style>
