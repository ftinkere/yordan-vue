<script setup>
import Layout from "@/Layouts/Layout.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import {computed, ref} from "vue";
import VInput from "@/Components/VInput.vue";
import VModal from "@/Components/VModal.vue";
import VInputFile from "@/Components/VInputFile.vue";

/* global route */


let user = usePage().props.auth.user;

const isEdit = ref(false);

const form = useForm({
    name: user.name,
});
const applyForm = function() {
    window.axios(route('api.user.update'), {
        method: 'POST',
        headers: { 'Accept': 'application/json' },
        data: form.data(),
    }).then(function (response) {
        isEdit.value = false;
        user.name = response.data.name;
    }).catch(function (reason) {
        console.error(reason);
    });
}

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
        isEdit.value = false;
        user.avatar = response.data.path;
        avatarForm.reset();

    }).catch(function (reason) {
        console.error(reason);
    });
}

</script>

<template>
    <Layout>
        <div class="mb-2 mx-auto max-w-md flex flex-row">
            <VModal button-class="mx-auto" modal-box-class="flex flex-col gap-2 items-center">
                <img v-if="user.avatar" class="rounded-full w-20" alt="Аватар" :src="user.avatar">
                <svg v-else class="fill-neutral-200 rounded-xl" width="3rem" height="3rem" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg"><path d="M628.736 528.896A416 416 0 0 1 928 928H96a415.872 415.872 0 0 1 299.264-399.104L512 704l116.736-175.104zM720 304a208 208 0 1 1-416 0 208 208 0 0 1 416 0z"/></svg>

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

        <div v-if="!isEdit" class="mx-auto card max-w-md">
            <h3 class="card-title flex flex-row justify-between text-2xl font-bold">
                {{ user.name }}
                <button
                    class="btn btn-warning btn-sm fill-white items-center text-lg cursor-pointer"
                    @click="isEdit = !isEdit"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"/></svg>
                </button>
            </h3>

            <div class="card-body p-2 flex flex-col gap-2">
                <div class="flex flex-row gap-2 items-center">
                    {{ user.email }}
                    <svg v-if="user.email_verified_at" class="fill-green-600" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"/></svg>
                    <div v-else class="flex flex-row gap-2 items-center">
                        <svg class="fill-red-600" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 384 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M176 0c-26.5 0-48 21.5-48 48v80H48c-26.5 0-48 21.5-48 48v32c0 26.5 21.5 48 48 48h80V464c0 26.5 21.5 48 48 48h32c26.5 0 48-21.5 48-48V256h80c26.5 0 48-21.5 48-48V176c0-26.5-21.5-48-48-48H256V48c0-26.5-21.5-48-48-48H176z"/></svg>
                        <button
                            class="link link-info"
                            onclick="axios(route('api.user.resend_confirmation'), { method: 'POST' })"
                        >
                            Подтвердить
                        </button>
                    </div>
                </div>

                <div class="flex flex-row gap-2">
                    Зарегистрирован: <span>{{ (new Date(user.created_at)).toLocaleString() }}</span>
                </div>
            </div>
        </div>

        <div v-else class="mx-auto max-w-md card flex flex-col gap-4">
            <VInput
                v-model="form.name"
                class="max-w-md"
                label="Имя"
                name="name"
                :errors="form.errors.name"
                required
            />
            <div class="flex flex-row justify-between">
                <button class="btn btn-success w-fit" @click="applyForm">
                    Сохранить
                </button>

                <button class="btn btn-error w-fit" @click="isEdit = false">
                    Отменить
                </button>
            </div>
        </div>
    </Layout>
</template>

<style scoped>

</style>
