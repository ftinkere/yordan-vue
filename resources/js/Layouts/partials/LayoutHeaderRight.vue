<script setup>
import { inject } from "vue";
import { Link } from "@inertiajs/vue3";

</script>

<template>
    <div class="shrink flex flex-row">
        <div v-if="this.$page.props.user === null" class="flex flex-row space-x-4">
            <div class="flex flex-row space-x-4">
                <a class="" :href="route('register')">Регистрация</a>
                <a class="" :href="route('login')">Логин</a>
            </div>
        </div>

<!--        TODO: Component VDropdown-->
        <div v-else class="dropdown dropdown-end">
            <label tabindex="0">
                <!--                        Аватар-->
                <svg class="fill-neutral-200 rounded-xl" width="3rem" height="3rem" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg"><path d="M628.736 528.896A416 416 0 0 1 928 928H96a415.872 415.872 0 0 1 299.264-399.104L512 704l116.736-175.104zM720 304a208 208 0 1 1-416 0 208 208 0 0 1 416 0z"/></svg>
            </label>
            <div class="text-xl flex flex-col gap-2 menu dropdown-content shadow rounded-box bg-neutral-800 border border-neutral-600">
                <Link class="px-2" href="{{ route('cabinet') }}">Кабинет</Link>
                <Link class="px-2" href="{{ route('logout') }}">Выйти</Link>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
