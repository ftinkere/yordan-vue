<script setup>


const { user } = defineProps({
    user: {
        type: Object,
        required: true,
    },
})
</script>

<template>
  <div>
    <h3 class="card-title flex flex-row justify-between text-2xl font-bold">
      {{ user.name }}
    </h3>

    <div class="card-body p-2 flex flex-col gap-2">
      <div class="flex flex-row gap-2">
        Зарегистрирован: <span>{{ (new Date(user.created_at)).toLocaleString() }}</span>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
