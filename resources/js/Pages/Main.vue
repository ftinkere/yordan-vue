<script setup>
import Layout from "@/Layouts/Layout.vue";
import { Link } from "@inertiajs/vue3";

const { lasts, owned } = defineProps({
    lasts: {
        type: Array,
        default: () => [],
    },
    owned: {
        type: Array,
        default: () => [],
    },
})
</script>

<template>
    <Layout title="Главная">
        <article class="mx-auto max-w-screen-md">
            <p>Добро пожаловать на Ёрдан!</p>
            <p>На данном сайте вы можете создавать свои конланги. И делиться ими с другими людьми.</p>
        </article>

        <div class="mx-auto max-w-screen-md grid grid-cols-1" :class="{ 'md:grid-cols-2': $page.props.auth.user, 'max-w-md': !$page.props.auth.user }">
            <div class="flex flex-col gap-2">
                <div v-if="$page.props.auth.user" class="h-fit card rounded-2xl border-y flex flex-col divide-y divide-neutral-600">
                    <Link v-for="language in owned" :key="language.id" class="w-full text-center link hover:link-info text-lg" href="">
                        {{ language.name }}
                    </Link>
                </div>

                <button class="btn btn-sm w-full mb-4 md:mb-0">Добавить язык</button>
            </div>

            <div class="card rounded-2xl border-y flex flex-col divide-y divide-neutral-600">
                <Link v-for="language in lasts" :key="language.id" class="w-full text-center link hover:link-info text-lg" href="">
                    {{ language.name }}
                </Link>
            </div>
        </div>
    </Layout>
</template>

<style scoped>

</style>
