<script setup>
import CardList from "@/Components/CardList.vue";

const { language } = defineProps({
    language: {
        type: Object,
        required: true,
    }
})

const list = [
    {
        done: language.autonym == null || language.autonym_transcription == null || language.base_articles?.about == null,
        message: 'Нулевое что вы можете заполнить для вашего конланга это <a class="link link-info" :href="">информация</a>. То что будут видеть люди о вашем языке. <br>',
    },
    {
        done: true,
        message: 'True <a class="link link-info">Link</a>',
    },
]
</script>

<template>
    <div>
        <CardList>
            <p v-for="item in list" class="py-1 flex items-center">
                <b v-if="item.done" class="inline-flex mx-4 fill-success">
                    <svg xmlns="http://www.w3.org/2000/svg" height="1rem" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"/></svg>
                </b>
                <b v-else class="inline-flex mx-4 fill-error">
                    <svg xmlns="http://www.w3.org/2000/svg" height="1rem" viewBox="0 0 384 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg>
                </b>
                <span v-html="item.message"></span>
            </p>
        </CardList>
    </div>
</template>

<style scoped>

</style>
