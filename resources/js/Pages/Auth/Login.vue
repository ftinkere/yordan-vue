<script setup>
import Layout from "@/Layouts/Layout.vue";
import VCheckbox from "@/Components/VCheckbox.vue";
import VInput from "@/Components/VInput.vue";
import {useForm} from "@inertiajs/vue3";

const form = useForm({
    email: '',
    password: '',
    remember: true,
});

</script>

<template>
<Layout title="Логин">

    <form class="mx-auto flex flex-col items-start max-w-md" @submit.prevent="form.post(route('login'))">
        <VInput class="w-full max-w-md" label="Почта" name="email" v-model="form.email" :errors="form.errors.email" required/>
        <VInput class="w-full max-w-md" type="password" label="Пароль" name="password" v-model="form.password" :errors="form.errors.password" required/>

        <VCheckbox name="check" :errors="form.errors.remember" v-model="form.remember">Запомнить</VCheckbox>

        <button type="submit" class="mt-4 btn">Логин</button>
    </form>

</Layout>
</template>

<style scoped>

</style>
