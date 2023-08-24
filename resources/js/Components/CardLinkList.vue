<script setup>
import { Link } from "@inertiajs/vue3";

const { list, property } = defineProps({
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
    <div v-if="$page.props.auth.user" class="h-fit card rounded-2xl border-y flex flex-col divide-y divide-neutral-600">
        <Link v-for="item in list" :key="item.id" class="w-full text-center link hover:link-info text-lg" :href="item.href">
            {{ item[property] }}
        </Link>
    </div>
</template>

<style scoped>

</style>
