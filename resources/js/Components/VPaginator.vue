<script setup>
  import { Link } from '@inertiajs/vue3'

    const { paginator } = defineProps({
        paginator: {
            type: Object,
            required: true
        },
    })

</script>

<template>
  <nav
    v-if="paginator.links.length > 3"
    role="navigation"
    aria-label="Пагинация"
    class="flex flex-col gap-4 sm:items-start"
  >
    <div>
      <p
        class="flex flex-row flex-wrap items-baseline gap-1 text-sm text-neutral-400 leading-5"
      >
        <span>Showing</span>

        <template v-if="paginator.from">
          <span class="font-bold">
            {{ paginator.from }}
          </span>
          <span>to</span>
          <span class="font-bold">{{ paginator.to }}</span>
        </template>
        <span v-else>
          {{ paginator.data.length }}
        </span>
        <span>of</span>
        <span class="font-bold">{{ paginator.total }}</span>
        <span>results</span>
      </p>
    </div>

    <div class="flex-grow flex flex-row flex-wrap">
      <!-- Previous Page Link -->
      <span
        v-if="paginator.current_page === 1"
        aria-disabled="true"
        aria-label="Назад"
      >
        <span
          class="inline-flex items-center px-2 py-2 text-sm font-medium text-neutral-400 bg-neutral-800 border border-neutral-700 cursor-default rounded-l-md leading-5"
          aria-hidden="true"
        >
          <svg
            class="w-5 h-5"
            fill="currentColor"
            viewBox="0 0 20 20"
          >
            <path
              fill-rule="evenodd"
              d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
              clip-rule="evenodd"
            />
          </svg>
        </span>
      </span>
      <Link
        v-else
        :href="paginator.prev_page_url"
        rel="prev"
        class="inline-flex items-center px-2 py-2 text-sm font-medium text-neutral-300 hover:text-neutral-400 bg-neutral-900 border border-neutral-700 focus:ring rounded-l-md leading-5 cursor-pointer transition ease-in-out duration-150"
        aria-label="Назад"
      >
        <svg
          class="w-5 h-5"
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path
            fill-rule="evenodd"
            d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
            clip-rule="evenodd"
          />
        </svg>
      </Link>

      <!--  Pagination Elements  -->
      <template
        v-for="(element, i) in paginator.links"
        :key="element.label"
      >
        <template v-if="i === 0 || i === paginator.links.length - 1" />
        <span
          v-else-if="element.url === null"
          aria-disabled="true"
        >
          <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-neutral-500 bg-neutral-900 border border-neutral-700 cursor-default leading-5">
            {{ element.label }}
          </span>
        </span>
        <span
          v-else-if="element.active"
          class="inline-flex items-center px-4 py-2 text-sm font-medium text-neutral-500 dark:text-neutral-400 bg-neutral-800 border border-neutral-700 leading-5"
          aria-label="Страница {{ element.label }}"
        >
          {{ element.label }}
        </span>
        <Link
          v-else
          :href="element.url"
          class="inline-flex items-center px-4 py-2 text-sm font-medium text-neutral-300 hover:text-neutral-400 bg-neutral-900 border border-neutral-700 focus:ring leading-5 cursor-pointer transition ease-in-out duration-150"
          aria-label="Страница {{ element.label }}"
        >
          {{ element.label }}
        </Link>
      </template>

      <!--  Next Page Link  -->
      <Link
        v-if="paginator.next_page_url !== null"
        :href="paginator.next_page_url"
        rel="next"
        class="inline-flex items-center px-2 py-2 text-sm font-medium text-neutral-500 bg-neutral-900 border border-neutral-700 cursor-pointer rounded-r-md leading-5"
        aria-label="Вперёд"
      >
        <svg
          class="w-5 h-5"
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path
            fill-rule="evenodd"
            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
            clip-rule="evenodd"
          />
        </svg>
      </Link>
      <span
        v-else
        aria-disabled="true"
        aria-label="Вперёд"
      >
        <span
          class="inline-flex items-center px-2 py-2 text-sm font-medium text-neutral-400 bg-neutral-800 border border-neutral-700 focus:ring rounded-r-md leading-5 cursor-default transition ease-in-out duration-150"
          aria-hidden="true"
        >
          <svg
            class="w-5 h-5"
            fill="currentColor"
            viewBox="0 0 20 20"
          >
            <path
              fill-rule="evenodd"
              d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
              clip-rule="evenodd"
            />
          </svg>
        </span>
      </span>
    </div>
  </nav>
</template>

<style scoped>

</style>