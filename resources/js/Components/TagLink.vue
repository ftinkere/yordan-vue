<script setup>
import { Link } from "@inertiajs/vue3";
import { inject } from "vue";
import route from "ziggy-js";

const { tag } = defineProps({
    tag: {
        type: Object,
        required: true,
    },
    notLink: {
        type: Boolean,
        default: false,
    }
});

const language = inject('language');
</script>

<template>
  <Link
    v-if="!notLink"
    class="link link-info hover:text-purple-600"
    :href="route('languages.articles', { code: language.id, tag: tag.tag })"
  >
    {{ tag.tag }}
  </Link>
  <span
    v-else
    class="link cursor-default"
  >
    {{ tag.tag }}
  </span>
</template>

<style scoped>

</style>
