<script setup>
import { Link } from "@inertiajs/vue3";
import CardList from "@/Components/CardList.vue";

defineProps({
    list: {
        type: Array,
        required: true,
    },
    property: {
        type: String,
        default: 'name',
    }
});
</script>

<template>
  <CardList>
    <Link
      v-for="item in list"
      :key="item.id"
      class="w-full text-center link hover:link-info text-lg"
      :href="item.href"
    >
      {{ item[property] }}
    </Link>
  </CardList>
</template>

<style scoped>

</style>
