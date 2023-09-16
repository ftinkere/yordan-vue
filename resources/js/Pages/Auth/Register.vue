<script setup>
import Layout from "@/Layouts/Layout.vue";
import VCheckbox from "@/Components/VCheckbox.vue";
import VInput from "@/Components/VInput.vue";
import { useForm } from "@inertiajs/vue3";

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
    <form
      class="mx-auto flex flex-col items-start max-w-md"
      @submit.prevent="form.post(route('register'))"
    >
      <VInput
        v-model="form.name"
        class="w-full max-w-md"
        label="Имя"
        name="name"
        :errors="form.errors.name"
        required
      />
      <VInput
        v-model="form.email"
        class="w-full max-w-md"
        label="Почта"
        name="email"
        :errors="form.errors.email"
        required
      />
      <VInput
        v-model="form.password"
        class="w-full max-w-md"
        type="password"
        label="Пароль"
        name="password"
        :errors="form.errors.password"
        required
      />
      <VInput
        v-model="form.password_confirmation"
        class="w-full max-w-md"
        type="password"
        label="Подтвердите пароль"
        name="password_confirmation"
        :errors="form.errors.password_confirmation"
        required
      />

      <VCheckbox
        v-model="form.check"
        name="check"
        :errors="form.errors.check"
        required
      >
        Я согласен с условиями
      </VCheckbox>

      <button
        type="submit"
        class="mt-4 btn"
      >
        Регистрация
      </button>
    </form>
  </Layout>
</template>

<style scoped>

</style>
