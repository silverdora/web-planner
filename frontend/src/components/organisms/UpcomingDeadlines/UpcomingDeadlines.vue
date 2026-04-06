<template>
  <section class="space-y-4">
    <div>
      <Heading :level="2" size="xl">Upcoming deadlines</Heading>
      <Text color="muted" class="mt-1">
        Stay on top of overdue tasks and what is coming next.
      </Text>
    </div>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
      <article class="rounded-2xl border border-red-200 bg-red-50 p-5 shadow-sm">
        <h3 class="text-lg font-semibold text-red-800">Overdue</h3>
        <p class="mt-1 text-sm text-red-700">{{ overdue.length }} task(s)</p>

        <ul v-if="overdue.length" class="mt-4 space-y-3">
          <li v-for="task in overdue" :key="task.id" class="rounded-xl bg-white px-3 py-3">
            <p class="font-medium text-gray-900">{{ task.title }}</p>
            <p class="mt-1 text-sm text-gray-500">
              {{ formatDate(task.dueDate || task.due_date) }}
            </p>
          </li>
        </ul>

        <p v-else class="mt-4 text-sm text-red-700">No overdue tasks.</p>
      </article>

      <article class="rounded-2xl border border-blue-200 bg-blue-50 p-5 shadow-sm">
        <h3 class="text-lg font-semibold text-blue-800">Today</h3>
        <p class="mt-1 text-sm text-blue-700">{{ today.length }} task(s)</p>

        <ul v-if="today.length" class="mt-4 space-y-3">
          <li v-for="task in today" :key="task.id" class="rounded-xl bg-white px-3 py-3">
            <p class="font-medium text-gray-900">{{ task.title }}</p>
            <p class="mt-1 text-sm text-gray-500">
              {{ formatDate(task.dueDate || task.due_date) }}
            </p>
          </li>
        </ul>

        <p v-else class="mt-4 text-sm text-blue-700">No tasks due today.</p>
      </article>

      <article class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
        <h3 class="text-lg font-semibold text-emerald-800">Next 7 days</h3>
        <p class="mt-1 text-sm text-emerald-700">{{ week.length }} task(s)</p>

        <ul v-if="week.length" class="mt-4 space-y-3">
          <li v-for="task in week" :key="task.id" class="rounded-xl bg-white px-3 py-3">
            <p class="font-medium text-gray-900">{{ task.title }}</p>
            <p class="mt-1 text-sm text-gray-500">
              {{ formatDate(task.dueDate || task.due_date) }}
            </p>
          </li>
        </ul>

        <p v-else class="mt-4 text-sm text-emerald-700">No tasks due this week.</p>
      </article>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'
import Heading from '@/components/atoms/Heading/Heading.vue'
import Text from '@/components/atoms/Text/Text.vue'

const props = defineProps({
  upcoming: {
    type: Object,
    default: () => ({
      overdue: [],
      today: [],
      week: [],
    }),
  },
})

const overdue = computed(() => props.upcoming?.overdue ?? [])
const today = computed(() => props.upcoming?.today ?? [])
const week = computed(() => props.upcoming?.week ?? [])

const formatDate = (value) => {
  if (!value) return 'No due date'

  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return String(value)

  return date.toLocaleString()
}
</script>