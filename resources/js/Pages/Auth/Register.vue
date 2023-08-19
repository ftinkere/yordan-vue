<script setup>
import Layout from "@/Layouts/Layout.vue";
import VCheckbox from "@/Components/VCheckbox.vue";
import VInput from "@/Components/VInput.vue";
import {useForm} from "@inertiajs/vue3";

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    check: false,
});


</script>

<template>
<Layout title="Регистрация">

    <form class="mx-auto flex flex-col items-start max-w-md" @submit.prevent="form.post(route('register'))">

<!--        <x-input-elem name="name" type="text" label="Имя" required />-->
<!--        <x-input-elem name="email" type="email" label="Почта" required />-->
<!--        <x-input-elem name="password" type="password" label="Пароль" required />-->
<!--        <x-input-elem name="repeat" type="password" label="Повторите пароль" required />-->

        <VInput class="w-full max-w-md" label="Имя" name="name" v-model="form.name" :errors="form.errors.name" required/>
        <VInput class="w-full max-w-md" label="Почта" name="email" v-model="form.email" :errors="form.errors.email" required/>
        <VInput class="w-full max-w-md" type="password" label="Пароль" name="password" v-model="form.password" :errors="form.errors.password" required/>
        <VInput class="w-full max-w-md" type="password" label="Подтвердите пароль" name="password_confirmation" v-model="form.password_confirmation" :errors="form.errors.password_confirmation" required/>

        <VCheckbox name="check" :errors="form.errors.check" v-model="form.check" required >Я согласен с условиями</VCheckbox>

        <button type="submit" class="mt-4 btn">Регистрация</button>
    </form>

</Layout>
</template>

<style scoped>

</style>
