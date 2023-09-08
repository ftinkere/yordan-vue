<script setup>
import Layout from "@/Layouts/Layout.vue";
import {computed} from "vue";
import CardLinkList from "@/Components/CardLinkList.vue";

/* global route */

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

const lastsWithHref = computed(function() {
    return lasts.map(function (el) {
       return {...el, href: route('languages.view', { code: el.id })};
    });
});
const ownedWithHref = computed(function() {
    return owned.map(function (el) {
       return {...el, href: route('languages.view', { code: el.id })};
    });
});
</script>

<template>
    <Layout title="Главная">
        <article class="mx-auto max-w-screen-md">
            <p>Добро пожаловать на Ёрдан!</p>
            <p>На данном сайте вы можете создавать свои конланги. И делиться ими с другими людьми.</p>
        </article>

        <div class="mt-4 mx-auto grid grid-cols-1"
             :class="{ 'md:grid-cols-2': $page.props.auth.user, 'max-w-md': !$page.props.auth.user }"
        >
            <div v-if="$page.props.auth.user" class="flex flex-col gap-2 max-w-screen-xs">
                <CardLinkList v-if="ownedWithHref.length > 0" :list="ownedWithHref" />
                <button class="btn btn-sm w-full mb-4 md:mb-0">Добавить язык</button>
            </div>

            <CardLinkList v-if="lastsWithHref.length > 0" class="max-w-screen-xs" :list="lastsWithHref" />
        </div>
    </Layout>
</template>

<style scoped>

</style>
