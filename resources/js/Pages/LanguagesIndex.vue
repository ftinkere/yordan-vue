<script setup>
import Layout from "@/Layouts/Layout.vue";
import VModal from "@/Components/VModal.vue";
import {Link, useForm} from "@inertiajs/vue3";
import VInput from "@/Components/VInput.vue";

/* global route */

const { languages } = defineProps({
    languages: {
        type: Object,
        required: true,
    },
});

const addForm = useForm({
   name: null,
});
function submit() {
    addForm.post(route('languages.store'));
}
</script>

<template>
    <Layout title="Все языки">
        <div v-if="$page.props.auth.user" class="mb-4">
            <VModal button-class="btn btn-primary">
                Добавить язык

                <template #header>
                    Добавить язык
                </template>

                <template #content>
                    <div class="flex flex-row items-end">
                        <input hidden name="_token" :value="$page.props.csrf_token">
                        <VInput v-model="addForm.name" class="grow" label="Название" />
                        <button class="btn btn-success" @click="submit">Добавить</button>
                    </div>
                </template>
            </VModal>
        </div>

        <table class="table table-zebra">
            <thead>
                <tr>
                    <th scope="col">Код</th>
                    <th scope="col">Название</th>
                    <th scope="col">Создатель</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="language in languages.data" :key="language.id" class="hover">
                    <th scope="row">{{ language.id }}</th>
                    <td><Link class="hover:text-primary" :href="route('languages.view', {code: language.id})">{{ language.name }}</Link></td>
                    <td>{{ language.user ? language.user.name : 'null' }}</td>
                </tr>
            </tbody>
        </table>
    </Layout>
</template>

<style scoped>

</style>
