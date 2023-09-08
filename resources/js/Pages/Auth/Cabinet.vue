<script setup>
import Layout from "@/Layouts/Layout.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import {computed, reactive, ref, watch} from "vue";
import VInput from "@/Components/VInput.vue";
import VModal from "@/Components/VModal.vue";
import VInputFile from "@/Components/VInputFile.vue";
import UserAvatar from "@/Components/UserAvatar.vue";

/* global route */

let user = reactive(usePage().props.auth.user);

const form = useForm({
    name: user.name,
    email: user.email,
});

const applyForm = function() {
    window.axios(route('api.user.update'), {
        method: 'POST',
        headers: { 'Accept': 'application/json' },
        data: form.data(),
    }).then(function (response) {
        user.name = response.data.name;
        user.email = response.data.email;
        user.email_verified_at = response.data.email_verified_at;
    })
}

const passwordForm = useForm({
    password: null,
    password_confirmation: null,
});

const passwordModal = ref(null);

const errors = reactive({
    password: null,
    password_confirmation: null,
});

const passwordValidate = function () {
    if (passwordForm.password.length < 8) {
        errors.password = "Пароль должен быть минимум 8 символов.";
    } else {
        errors.password = null;
    }

    if (passwordForm.password !== passwordForm.password_confirmation) {
        errors.password_confirmation = "Пароли не совпадают";
    } else {
        errors.password_confirmation = null;
    }
}

const applyPasswordForm = function() {
    if (errors.password !== null || errors.password_confirmation !== null) {
        return;
    }

    window.axios(route('api.user.change_password'), {
        method: 'POST',
        headers: { 'Accept': 'application/json' },
        data: { password: passwordForm.password },
    }).then(function (response) {
        passwordModal.close();
    }).catch(function (reason) {
        errors.password = reason.response?.data?.errors?.password[0];
    });
}

watch(passwordForm, passwordValidate);

const avatarForm = useForm({
    avatar: null,
});

const avatar_preview = computed(function () {
    if (avatarForm.avatar) {
        return URL.createObjectURL(avatarForm.avatar);
    }
    return null;
});
const pushAvatar = function() {
    window.axios(route('api.user.push_avatar'), {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'multipart/form-data'
        },
        data: avatarForm.data(),
    }).then(function (response) {
        user.avatar = response.data.path;
        avatarForm.reset();

    });
}

</script>

<template>
    <Layout>
        <div class="mb-2 mx-auto max-w-md flex flex-row">
            <VModal button-class="mx-auto" modal-box-class="flex flex-col gap-2 items-center">
                <UserAvatar class="w-[5rem]" :avatar="user.avatar" alt="Аватар" />

                <template #header>
                    Сменить аватар
                </template>

                <template #content>
                    <img v-if="avatar_preview" class="rounded-full w-20" alt="Аватар" :src="avatar_preview" >

                    <VInputFile v-model="avatarForm.avatar" label="Аватар" input-class="file-input file-input-bordered" />
                    <button
                        class="btn btn-success"
                        type="submit"
                        @click="pushAvatar"
                    >
                        Отправить
                    </button>
                </template>
            </VModal>
        </div>

        <div class="mx-auto card max-w-md">
            <h3 class="card-title flex flex-row text-2xl font-bold">
                {{ user.name }}
                <VModal button-class="btn btn-ghost btn-sm fill-white items-center text-lg cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"/></svg>

                    <template #header>
                        Изменить имя
                    </template>

                    <template #content>
                        <VInput v-model="form.name"
                                label="Имя"
                                :errors="$page.props.errors.name"
                                class="grow"
                                input-class="rounded-e-none"
                                @keyup.enter="applyForm"
                        >
                            <template #button>
                                <button class="btn btn-success rounded-s-none" @click="applyForm">Изменить</button>
                            </template>
                        </VInput>
                    </template>
                </VModal>
            </h3>

            <div class="card-body p-2 flex flex-col gap-2">
                <div class="flex flex-row gap-2 items-center">
                    {{ user.email }}
                    <svg v-if="user.email_verified_at !== null" class="fill-green-600" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"/></svg>
                    <div v-else class="flex flex-row gap-2 items-center">
                        <svg class="fill-red-600" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 384 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M176 0c-26.5 0-48 21.5-48 48v80H48c-26.5 0-48 21.5-48 48v32c0 26.5 21.5 48 48 48h80V464c0 26.5 21.5 48 48 48h32c26.5 0 48-21.5 48-48V256h80c26.5 0 48-21.5 48-48V176c0-26.5-21.5-48-48-48H256V48c0-26.5-21.5-48-48-48H176z"/></svg>
                        <button
                            class="link link-info"
                            onclick="axios(route('api.user.resend_confirmation'), { method: 'POST' })"
                        >
                            Подтвердить
                        </button>
                    </div>

                    <VModal class="ms-4" button-class="btn btn-ghost btn-sm fill-white items-center text-lg cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"/></svg>

                        <template #header>
                            Изменить почту
                        </template>

                        <template #content>
                            <VInput v-model="form.email"
                                    label="Почта"
                                    :errors="$page.props.errors.email"
                                    class="grow"
                                    input-class="rounded-e-none"
                                    @keyup.enter="applyForm"
                            >
                                <template #button>
                                    <button class="btn btn-success rounded-s-none" @click="applyForm">Изменить</button>
                                </template>
                            </VInput>
                        </template>
                    </VModal>
                </div>

                <div class="flex flex-row gap-2">
                    Зарегистрирован: <span>{{ (new Date(user.created_at)).toLocaleString() }}</span>
                </div>

                <div>
                    <VModal ref="passwordModal" class="ms-4" button-class="items-center cursor-pointer">
                        <span class="link link-info hover:link-accent">Сменить пароль</span>

                        <template #header>
                            Сменить пароль
                        </template>

                        <template #content>
                            <div class="flex flex-col">
                                <VInput v-model="passwordForm.password"
                                        label="Пароль"
                                        type="password"
                                        :errors="errors.password"
                                />
                                <VInput v-model="passwordForm.password_confirmation"
                                        label="Подтвердите пароль"
                                        type="password"
                                        :errors="errors.password_confirmation"
                                />
                                <button class="mt-4 ms-auto btn btn-success" @click="applyPasswordForm">
                                    Изменить
                                </button>
                            </div>
                        </template>
                    </VModal>
                </div>
            </div>
        </div>
    </Layout>
</template>

<style scoped>

</style>
