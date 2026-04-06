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

        <ul v-if="visibleOverdue.length" class="mt-4 space-y-3">
          <li
              v-for="task in visibleOverdue"
              :key="task.id"
              class="rounded-xl bg-white px-3 py-3"
          >
            <p class="font-medium text-gray-900">{{ task.title }}</p>

            <div class="mt-2 flex items-center justify-between gap-3">
              <p class="text-sm text-gray-500">
                {{ formatDate(task.dueDate || task.due_date) }}
              </p>

              <BaseButton
                  size="sm"
                  variant="ghost"
                  @click="$emit('go-to-task', task.id)"
              >
                Go to task
              </BaseButton>
            </div>
          </li>
        </ul>

        <p v-else class="mt-4 text-sm text-red-700">No overdue tasks.</p>

        <BaseButton
            v-if="overdue.length > 5"
            class="mt-4"
            size="sm"
            variant="ghost"
            @click="expanded.overdue = !expanded.overdue"
        >
          {{ expanded.overdue ? 'Show less' : 'View all' }}
        </BaseButton>
      </article>

      <article class="rounded-2xl border border-blue-200 bg-blue-50 p-5 shadow-sm">
        <h3 class="text-lg font-semibold text-blue-800">Today</h3>
        <p class="mt-1 text-sm text-blue-700">{{ today.length }} task(s)</p>

        <ul v-if="visibleToday.length" class="mt-4 space-y-3">
          <li
              v-for="task in visibleToday"
              :key="task.id"
              class="rounded-xl bg-white px-3 py-3"
          >
            <p class="font-medium text-gray-900">{{ task.title }}</p>

            <div class="mt-2 flex items-center justify-between gap-3">
              <p class="text-sm text-gray-500">
                {{ formatDate(task.dueDate || task.due_date) }}
              </p>

              <BaseButton
                  size="sm"
                  variant="ghost"
                  @click="$emit('go-to-task', task.id)"
              >
                Go to task
              </BaseButton>
            </div>
          </li>
        </ul>

        <p v-else class="mt-4 text-sm text-blue-700">No tasks due today.</p>

        <BaseButton
            v-if="today.length > 5"
            class="mt-4"
            size="sm"
            variant="ghost"
            @click="expanded.today = !expanded.today"
        >
          {{ expanded.today ? 'Show less' : 'View all' }}
        </BaseButton>
      </article>

      <article class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
        <h3 class="text-lg font-semibold text-emerald-800">Next 7 days</h3>
        <p class="mt-1 text-sm text-emerald-700">{{ week.length }} task(s)</p>

        <ul v-if="visibleWeek.length" class="mt-4 space-y-3">
          <li
              v-for="task in visibleWeek"
              :key="task.id"
              class="rounded-xl bg-white px-3 py-3"
          >
            <p class="font-medium text-gray-900">{{ task.title }}</p>

            <div class="mt-2 flex items-center justify-between gap-3">
              <p class="text-sm text-gray-500">
                {{ formatDate(task.dueDate || task.due_date) }}
              </p>

              <BaseButton
                  size="sm"
                  variant="ghost"
                  @click="$emit('go-to-task', task.id)"
              >
                Go to task
              </BaseButton>
            </div>
          </li>
        </ul>

        <p v-else class="mt-4 text-sm text-emerald-700">No tasks due this week.</p>

        <BaseButton
            v-if="week.length > 5"
            class="mt-4"
            size="sm"
            variant="ghost"
            @click="expanded.week = !expanded.week"
        >
          {{ expanded.week ? 'Show less' : 'View all' }}
        </BaseButton>
      </article>
    </div>
  </section>
</template>

<script setup>
import { computed, reactive } from 'vue'
import Heading from '@/components/atoms/Heading/Heading.vue'
import Text from '@/components/atoms/Text/Text.vue'
import BaseButton from '@/components/atoms/BaseButton/BaseButton.vue'

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

defineEmits(['go-to-task'])

const expanded = reactive({
  overdue: false,
  today: false,
  week: false,
})

const overdue = computed(() => props.upcoming?.overdue ?? [])
const today = computed(() => props.upcoming?.today ?? [])
const week = computed(() => props.upcoming?.week ?? [])

const visibleItems = (items, isExpanded) => (isExpanded ? items : items.slice(0, 5))

const visibleOverdue = computed(() => visibleItems(overdue.value, expanded.overdue))
const visibleToday = computed(() => visibleItems(today.value, expanded.today))
const visibleWeek = computed(() => visibleItems(week.value, expanded.week))

const formatDate = (value) => {
  if (!value) return 'No due date'

  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return String(value)

  return date.toLocaleString()
}
</script>