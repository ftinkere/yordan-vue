<script setup>
import VModal from "@/Components/VModal.vue";
import { Link } from "@inertiajs/vue3";

const links = [
    {label: "Все языки", route: "route('languages.index')"},
    {label: "Справка", route: "route('info')"},
    {label: "О нас", route: "route('about')"},
];
</script>

<template>
    <div class="grow flex flex-row gap-4 items-baseline">

        <VModal
            without-x
            button-class="btn btn-ghost sm:hidden"
            modal-box-class="absolute left-0 top-[4rem] max-h-full h-[100dvh] w-2/3 flex flex-col gap-1"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-5 h-5 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>

            <template #content>
                <Link v-for="{ route, label } in links" :key="route" class="text-xl" :href="route">
                    {{ label }}
                </Link>
            </template>
        </VModal>

        <Link class="text-2xl" :href="route('main')">
            Ёрдан
        </Link>
        <div class="flex flex-row space-x-4 max-sm:hidden">
            <Link v-for="{ route, label } in links" :key="route" :href="route">
                {{ label }}
            </Link>
        </div>
    </div>
</template>

<style scoped>

</style>
