<script setup>
import Layout from "@/Layouts/Layout.vue";
import VModal from "@/Components/VModal.vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import VInput from "@/Components/VInput.vue";
import route from "ziggy-js";
import VPaginator from "@/Components/VPaginator.vue";

defineProps({
    languages: {
        type: Object,
        required: true,
    },
});

const addForm = useForm({
    name: null,
    _token: usePage().props.csrf_token,
});

function submit() {
    addForm.post(route('languages.store'));
}
</script>

<template>
  <Layout title="Все языки">
    <div
      v-if="$page.props.auth.user && $page.props.auth.user.email_verified_at"
      class="mb-4"
    >
      <VModal
        button-class="btn btn-primary"
        header="Добавить язык"
      >
        <span>Добавить язык</span>

        <template #content>
          <div class="flex flex-row items-end">
            <VInput
              v-model="addForm.name"
              label="Название"
              :errors="addForm.errors.name"
              class="grow"
              input-class="rounded-e-none"
              @keyup.enter="submit"
            >
              <template #button>
                <button
                  class="btn btn-success rounded-s-none"
                  @click="submit"
                >
                  Добавить
                </button>
              </template>
            </VInput>
          </div>
        </template>
      </VModal>
    </div>

    <table class="table table-zebra">
      <thead>
        <tr>
          <th scope="col">
            Код
          </th>
          <th scope="col">
            Название
          </th>
          <th scope="col">
            Создатель
          </th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="language in languages.data"
          :key="language.id"
          class="hover"
        >
          <th scope="row">
            {{ language.id }}
          </th>
          <td>
            <Link
              class="hover:text-info"
              :href="route('languages.view', language.id)"
            >
              {{ language.name }}
            </Link>
          </td>
          <td>
            <Link
              class="hover:text-info"
              :href="route('profile', language.user_id)"
            >
              {{ language.user ? language.user.name : 'null' }}
            </Link>
          </td>
        </tr>
      </tbody>
    </table>

    <VPaginator :paginator="languages" />
  </Layout>
</template>

<style scoped>

</style>
