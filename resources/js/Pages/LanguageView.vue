<script setup>
import LanguageLayout from "@/Layouts/LanguageLayout.vue";
import VMarkdownViewer from "@/Components/VMarkdownViewer.vue";
import VMarkdownEditor from "@/Components/VMarkdownEditor.vue";
import {computed, reactive, ref, toRef, watch} from "vue";
import TodoList from "@/Components/LanguageTodoList.vue";
import LanguageTodoAction from "@/Components/LanguageTodoAction.vue";
import VModal from "@/Components/VModal.vue";
import VInput from "@/Components/VInput.vue";
import {useForm} from "@inertiajs/vue3";
import TranscriptionConverter from "@/Components/TranscriptionConverter.vue";
import _ from "lodash";
import UserShort from "@/Components/UserShort.vue";
import VInputFile from "@/Components/VInputFile.vue";
import UserAvatar from "@/Components/UserAvatar.vue";
import LanguageFlag from "@/Components/LanguageFlag.vue";
import VSaveLoader from "@/Components/VSaveLoader.vue";

/* global route */

const props = defineProps({
    language: {
        type: Object,
        required: true,
    },
    can_edit: {
        type: Boolean,
        default: false,
    },
});

const { can_edit } = props;
const language = reactive(props.language);

const action = reactive({
    message: null,
    button: null,
    modal: null,
});

let actionModal = ref(null);

const autonymModal = ref(null);
const autonymTranscriptionModal = ref(null);
const typeModal = ref(null);
const aboutModal = ref(null);

const actionUpdate = function () {
    window.axios.get(route('languages.action', { code: language.id }))
        .then((response) => {
            if (response.data === null) {
                action.message = null;
                action.button = null;
                action.modal = null;
                actionModal.value = null;
                return;
            }
            action.message = response.data.message;
            action.button = response.data.button;
            action.modal = response.data.modal;

            if (action.modal === 'autonym') {
                actionModal.value = autonymModal.value;
            } else if (action.modal === 'autonym_transcription') {
                actionModal.value = autonymTranscriptionModal.value;
            } else if (action.modal === 'type') {
                actionModal.value = typeModal.value;
            } else if (action.modal === 'about') {
                actionModal.value = aboutModal.value;
            }
        });

}
actionUpdate();

const errors = reactive({});
const isDirty = ref(false);

const form = useForm({
    name: language.name,
    autonym: language.autonym,
    autonym_transcription: language.autonym_transcription,
    type: language.type,
    status: language.status,
});

const applyForm = function () {
    if (!form.name) {
        errors.name = "Имя не может быть пустым.";
        return;
    }
    errors.name = null;

    window.axios.post(route('languages.update', { code: language.id }), form.data())
        .then((response) => {
            language.name = response.data.name;
            language.autonym = response.data.autonym;
            language.autonym_transcription = response.data.autonym_transcription;
            language.type = response.data.type;
            language.status = response.data.status;

            actionUpdate();
            isDirty.value = false;
        }).catch((error) => {
            errors.name = error.response.data.errors?.name[0];
        });
};

watch(form, () => {
    isDirty.value = true;
});
watch(form, _.debounce(applyForm, 2000));

const about = ref(language.base_articles?.about || '');
const applyAbout = function () {
    window.axios.post(route('languages.about', { code: language.id }), { about: about.value })
        .then((response) => {
            about.value = response.data.about;

            actionUpdate();
            isDirty.value = false;
        });
}

watch(about, () => {
    isDirty.value = true;
});
watch(about, _.debounce(applyAbout, 2000));

let typeList = [
    'Априорный',
    'Апостериорный',
    'Художественный',
    'Артланг',
    'Инженерный',
    'Энджланг',
    'Вспомогательный',
    'Ауксланг',
    'Вымышленный',
    'Персональный',
    'Персланг',
    'Философский',
    'Логический',
];

const flagForm = useForm({
    flag: null,
});

const pushFlag = function () {
    window.axios.post(route('languages.flag', { code: language.id }), flagForm.data(), {
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'multipart/form-data'
        },
    }).then((response) => {
        language.flag = response.data.path;
    });
}

const flagPreview = computed(function () {
    if (flagForm.flag) {
        return URL.createObjectURL(flagForm.flag);
    }
    return null;
});

const isEdit = ref(false);

</script>

<template>
    <LanguageLayout :language="language" :can_edit="can_edit">

        <div v-if="!isEdit || !can_edit">
            <div class="flex flex-row justify-between items-center">
                <h1 class="flex flex-row gap-4 items-center text-2xl font-bold">
                    <span class="flex flex-row gap-2">
                    <span>{{ language.name }}</span>
                    <span v-if="language.autonym"> - {{ language.autonym }}</span>
                    <span v-if="language.autonym_transcription"> /{{ language.autonym_transcription }}/</span>
                </span>
                </h1>

                <button class="btn btn-warning btn-sm" @click="isEdit = true">
                    <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"/></svg>
                </button>
            </div>

            <div class="my-4 mx-6 flex flex-row gap-2 items-baseline justify-between">
                <div class="flex flex-row gap-2">
                    <span v-if="language.type">{{ language.type }}</span>
                    <span v-if="language.status">{{ language.status }}</span>
                </div>
                <UserShort :user="language.user" />
            </div>

            <div class="my-4">
                <LanguageTodoAction
                    v-if="action.message"
                    :action="action"
                    :modal="actionModal"
                />
            </div>

            <div>
                <VMarkdownViewer
                    class="card border-y p-4 rounded-2xl text-white"
                    v-if="about"
                    :content="about"
                />

                <VMarkdownViewer
                    class="mt-4 card border-y border-warning p-4 rounded-2xl text-white"
                    v-if="language.dev_note?.note"
                    :content="language.dev_note?.note"
                />
            </div>
        </div>

        <div v-else class="flex flex-col gap-4">
            <div class="mb-4 flex flex-row justify-between items-center">
                <VModal modal-box-class="flex flex-col gap-2 items-center" button-class="h-fit w-fit">
                    <div class="pe-4 flex flex-row gap-2 items-center rounded-full hover:bg-neutral-700">
                        <LanguageFlag class="hover:fill-neutral-400" size="3rem" :language="language" />
                        <span>Изменить флаг</span>
                    </div>

                    <template #header>
                        Сменить флаг
                    </template>

                    <template #content>
                        <div class="mt-6 flex flex-col gap-4 items-end">
                            <LanguageFlag class="self-center" size="20rem" :language="language" :preview="flagPreview" />

                            <VInputFile
                                v-model="flagForm.flag"
                                class="w-full"
                                input-class="file-input file-input-bordered rounded-e-none"
                            >
                                <template #button>
                                    <button
                                        class="btn btn-success rounded-s-none"
                                        type="submit"
                                        @click="pushFlag"
                                    >
                                        Отправить
                                    </button>
                                </template>
                            </VInputFile>
                        </div>
                    </template>
                </VModal>

                <div class="flex flex-row gap-2">
                    <VSaveLoader class="fill-success" :is-save="!isDirty" />
                    <button class="btn btn-success btn-sm" @click="applyForm(); isEdit = false">
                        Сохранить
                    </button>
                </div>
            </div>

            <div class="mx-auto flex flex-col gap-2 w-full">
                <VInput
                    v-model="form.name"
                    label="Название"
                    class="w-full"
                    :errors="errors.name"
                    required
                />

                <div class="grid grid-cols-2">
                    <VInput v-model="form.autonym" label="Аутоним" class="w-full" />
                    <div class="flex flex-col">
                        <VInput v-model="form.autonym_transcription" label="Произношение аутонима" class="w-full" />
                        <TranscriptionConverter v-model="form.autonym_transcription" />
                    </div>
                </div>

                <div class="grid grid-cols-2">
                    <VInput v-model="form.type" label="Тип" class="w-full" :list="typeList" />
<!--                    <VInput v-model="form.status" label="Статус" class="w-full" />-->
                    <div>
                        <label class="label"><span class="label-text">Статус</span></label>
                        <select v-model="form.status" class="w-full select select-bordered">
                            <option :selected="form.status === 'Новый'">Новый</option>
                            <option :selected="form.status === 'В процессе'">В процессе</option>
                            <option :selected="form.status === 'Завершён'">Завершён</option>
                        </select>
                    </div>
                </div>

                <VMarkdownEditor
                    v-model="about"
                    :language="language"
                    label="О языке"
                    class="mt-2 w-full"
                />
            </div>
        </div>

        <VModal
            ref="autonymModal"
            without-button
            @close="actionUpdate"
        >
            <template #header>
                Аутоним
            </template>

            <template #content>
                <article>
                    Аутоним — самоназвание вашего языка.
                    Вы можете использовать юникод, но страйтесь использовать кириллицу или латиницу для записи,
                    но это не обязательно.
                </article>
                <div>
                    <input hidden name="_token" :value="$page.props.csrf_token">
                    <VInput v-model="form.autonym"
                            :errors="$page.props.errors.autonym"
                            class="grow"
                            input-class="rounded-e-none"
                            @keyup.enter="applyForm(); autonymModal.close()"
                    >
                        <template #button>
                            <button
                                class="btn btn-success rounded-s-none"
                                @click="applyForm(); autonymModal.close()"
                            >
                                Изменить
                            </button>
                        </template>
                    </VInput>
                </div>
            </template>
        </VModal>

        <VModal
            ref="autonymTranscriptionModal"
            without-button
            @close="actionUpdate"
        >
            <template #header>
                Произношение аутонима
            </template>

            <template #content>
                <article>
                    Ваша орфография может быть неочевидна другим людям,
                    поэтому укажите, пожалуйста, как ваш аутоним произносится.
                    Для этого вы можете использовать X-SAMPA, который удобно набирать на клавиатуре.
                    Просто введите его в поле и нажмите кнопку конвертации.
                </article>
                <div class="flex flex-col">
                    <input hidden name="_token" :value="$page.props.csrf_token">
                    <VInput
                        v-model="form.autonym_transcription"
                        :errors="$page.props.errors.autonym_transcription"
                        class="grow"
                        input-class="rounded-e-none"
                        @keyup.enter="applyForm(); autonymTranscriptionModal.close()"
                    >
                        <template #button>
                            <button
                                class="btn btn-success rounded-s-none"
                                @click="applyForm(); autonymTranscriptionModal.close()"
                            >
                                Изменить
                            </button>
                        </template>
                    </VInput>
                    <TranscriptionConverter v-model="form.autonym_transcription" />
                </div>
            </template>
        </VModal>

        <VModal
            ref="typeModal"
            without-button
            @close="actionUpdate"
        >
            <template #header>
                Тип конланга
            </template>

            <template #content>
                <article>
                    В данном поле вы можете указать что угодно, это ваше видение конланга.
                    Но для вас есть небольшой список для помощи.
                </article>
                <div class="flex flex-col">
                    <input hidden name="_token" :value="$page.props.csrf_token">
                    <VInput
                        v-model="form.type"
                        :errors="$page.props.errors.type"
                        class="grow"
                        input-class="rounded-e-none"
                        :list="typeList"
                        @keyup.enter="applyForm(); typeModal.close()"
                    >
                        <template #button>
                            <button
                                class="btn btn-success rounded-s-none"
                                @click="applyForm(); typeModal.close()"
                            >
                                Изменить
                            </button>
                        </template>
                    </VInput>
                </div>
            </template>
        </VModal>

        <VModal
            ref="aboutModal"
            without-button
            class="w-full max-w-full md:max-w-screen-md"
            @close="actionUpdate"
        >
            <template #header>
                О языке
            </template>

            <template #content>
                <article>
                    Здесь вы можете сообщить о вашем языке что желаете.
                    Можете сообщить о носителях. Об истории. О строе. О своих мыслях.
                    Указать типологию вашего языка. С примерами на вашем языке.
                    И любое что может заинтересовать других людей.
                </article>
                <div class="mt-4 flex flex-col">
                    <input hidden name="_token" :value="$page.props.csrf_token">
                    <VMarkdownEditor v-model="about" :language="language" />
                    <button
                        class="mt-4 btn btn-success self-end w-fit"
                        @click="applyAbout()"
                    >
                        Сохранить
                    </button>
                </div>
            </template>
        </VModal>

    </LanguageLayout>
</template>

<style scoped>

</style>
