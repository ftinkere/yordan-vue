<script setup>
import LanguageLayout from "@/Layouts/LanguageLayout.vue";

const { language, tables } = defineProps({
    language: {
        type: Object,
        required: true
    },
    tables: {
        type: Array,
        default: () => [],
    }
})

console.log(tables)
</script>

<template>
  <LanguageLayout :language="language">
    <!-- Tables  -->
    <div class="flex flex-row flex-wrap gap-8">
      <!--  Table  -->
      <div
        v-for="table in tables"
        :key="table.name"
        class="overflow-x-auto grow"
      >
        <table class="table table-xs table-hover w-fit">
          <thead>
            <tr>
              <td
                v-for="el in table.rows[0]"
                :key="el.data"
                :is="el.header ? 'th' : 'td'"
                :colspan="el.colspan ?? 1"
                class="text-center text-white whitespace-nowrap w-fit"
              >
                {{ el.data }}
              </td>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(row, i) in table.rows.splice(1)"
              :key="i"
            >
              <td
                v-for="el in row"
                :key="el.data"
                :is="el.header ? 'th' : 'td'"
                :colspan="el.colspan ?? 1"
                :class="el.header ? 'text-end whitespace-nowrap w-fit' : 'text-center border border-neutral-700 whitespace-nowrap w-fit'"
              >
                {{ el.data }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <!--  End table  -->
    </div>
    <!--  End tables  -->
  </LanguageLayout>
</template>

<style scoped>

</style>