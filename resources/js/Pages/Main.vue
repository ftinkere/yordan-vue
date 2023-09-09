<script setup>
import Layout from "@/Layouts/Layout.vue";
import {computed} from "vue";
import CardLinkList from "@/Components/CardLinkList.vue";
import VInput from "@/Components/VInput.vue";
import VModal from "@/Components/VModal.vue";
import {useForm} from "@inertiajs/vue3";

/* global route */

const addForm = useForm({
    name: null,
});
function submit() {
    addForm.post(route('languages.store'));
}

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
                <VModal button-class="btn btn-sm w-full mb-4 md:mb-0">
                    <span>Добавить язык</span>

                    <template #header>
                        Добавить язык
                    </template>

                    <template #content>
                        <div class="flex flex-row items-end">
                            <input hidden name="_token" :value="$page.props.csrf_token">
                            <VInput v-model="addForm.name"
                                    label="Название"
                                    :errors="$page.props.errors.name"
                                    class="grow"
                                    input-class="rounded-e-none"
                                    @keyup.enter="submit"
                            >
                                <template #button>
                                    <button class="btn btn-success rounded-s-none" @click="submit">Добавить</button>
                                </template>
                            </VInput>
                        </div>
                    </template>
                </VModal>
            </div>

            <CardLinkList v-if="lastsWithHref.length > 0" class="max-w-screen-xs" :list="lastsWithHref" />
        </div>
    </Layout>
</template>

<style scoped>

</style>
