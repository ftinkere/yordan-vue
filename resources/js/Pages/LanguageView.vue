<script setup>
import LanguageLayout from "@/Layouts/LanguageLayout.vue";
import VMarkdownViewer from "@/Components/VMarkdownViewer.vue";
import VMarkdownEditor from "@/Components/VMarkdownEditor.vue";
import {reactive, ref, toRef, watch} from "vue";
import TodoList from "@/Components/LanguageTodoList.vue";
import LanguageTodoAction from "@/Components/LanguageTodoAction.vue";
import VModal from "@/Components/VModal.vue";
import VInput from "@/Components/VInput.vue";
import {useForm} from "@inertiajs/vue3";
import TranscriptionConverter from "@/Components/TranscriptionConverter.vue";
import _ from "lodash";
import UserShort from "@/Components/UserShort.vue";

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

const form = useForm({
    autonym: language.autonym,
    autonym_transcription: language.autonym_transcription,
    type: language.type,
});

const applyForm = function () {
    window.axios.post(route('languages.update', { code: language.id }), form.data())
        .then((response) => {
            language.autonym = response.data.autonym;
            language.autonym_transcription = response.data.autonym_transcription;
            language.type = response.data.type;
        });
}

const about = ref(language.base_articles?.about || '');
const applyAbout = function () {
    window.axios.post(route('languages.about', { code: language.id }), { about: about.value })
        .then((response) => {
            about.value = response.data.about;
        });
}

watch(about, _.debounce(applyAbout, 1000));

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

</script>

<template>
    <LanguageLayout :language="language" :can_edit="can_edit">
        <h1 class="text-2xl font-bold">
            <span>{{ language.name }}</span>
            <span v-if="language.autonym"> - {{ language.autonym }}</span>
            <span v-if="language.autonym_transcription"> /{{ language.autonym_transcription }}/</span>
        </h1>

        <div class="my-4 mx-6 flex flex-row gap-2 items-baseline justify-between">
            <div class="flex flex-row gap-2">
                <span v-if="language.type">{{ language.type }}</span>
                <span v-if="language.status?.status">{{ language.status.status }}</span>
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
            <VMarkdownViewer class="card border-y p-4 rounded-2xl text-white"
                             v-if="about"
                             :content="about" />

            <VMarkdownViewer class="mt-4 card border-y border-warning p-4 rounded-2xl text-white"
                             v-if="language.dev_note?.note"
                             :content="language.dev_note?.note" />
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
                    И любое что может заинтересовать других людей.
                </article>
                <div class="mt-4 flex flex-col">
                    <input hidden name="_token" :value="$page.props.csrf_token">
                    <VMarkdownEditor v-model="about" />
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
