<template>
  <div class="space-y-6">
    <section v-for="group in visibleGroups" :key="group.key" class="space-y-3">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-lg font-semibold text-gray-900">{{ group.title }}</h2>
          <p class="text-sm text-gray-500">{{ group.items.length }} task(s)</p>
        </div>
      </div>

      <div class="grid gap-4 md:grid-cols-2">
        <TaskCard
            v-for="task in group.items"
            :key="task.id"
            :task="task"
            :categories="categories"
            :saving="savingTaskId === task.id"
            :id="`task-${task.id}`"
            @delete="$emit('delete', task)"
            @save-edit="$emit('save-edit', $event)"
            @change-status="$emit('change-status', $event)"
        />
      </div>
    </section>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import TaskCard from '@/components/organisms/TaskCard/TaskCard.vue'

const props = defineProps({
  tasks: {
    type: Array,
    default: () => [],
  },
  categories: {
    type: Array,
    default: () => [],
  },
  savingTaskId: {
    type: [Number, String, null],
    default: null,
  },
})

defineEmits(['delete', 'save-edit', 'change-status'])

const getTaskDueDate = (task) => task?.dueDate || task?.due_date || ''

const startOfDay = (date) => {
  const copy = new Date(date)
  copy.setHours(0, 0, 0, 0)
  return copy
}

const endOfDay = (date) => {
  const copy = new Date(date)
  copy.setHours(23, 59, 59, 999)
  return copy
}

const visibleGroups = computed(() => {
  const now = new Date()
  const todayStart = startOfDay(now)
  const todayEnd = endOfDay(now)

  const tomorrowStart = new Date(todayStart)
  tomorrowStart.setDate(tomorrowStart.getDate() + 1)

  const tomorrowEnd = endOfDay(tomorrowStart)

  const weekEnd = endOfDay(new Date(todayStart))
  weekEnd.setDate(weekEnd.getDate() + 7)

  const groups = {
    overdue: [],
    today: [],
    tomorrow: [],
    thisWeek: [],
    later: [],
    noDueDate: [],
  }

  for (const task of props.tasks) {
    const rawDueDate = getTaskDueDate(task)

    if (!rawDueDate) {
      groups.noDueDate.push(task)
      continue
    }

    const dueDate = new Date(rawDueDate)

    if (Number.isNaN(dueDate.getTime())) {
      groups.noDueDate.push(task)
      continue
    }

    if (dueDate < todayStart) {
      groups.overdue.push(task)
    } else if (dueDate >= todayStart && dueDate <= todayEnd) {
      groups.today.push(task)
    } else if (dueDate >= tomorrowStart && dueDate <= tomorrowEnd) {
      groups.tomorrow.push(task)
    } else if (dueDate > tomorrowEnd && dueDate <= weekEnd) {
      groups.thisWeek.push(task)
    } else {
      groups.later.push(task)
    }
  }

  return [
    { key: 'overdue', title: 'Overdue', items: groups.overdue },
    { key: 'today', title: 'Today', items: groups.today },
    { key: 'tomorrow', title: 'Tomorrow', items: groups.tomorrow },
    { key: 'thisWeek', title: 'This week', items: groups.thisWeek },
    { key: 'later', title: 'Later', items: groups.later },
    { key: 'noDueDate', title: 'No due date', items: groups.noDueDate },
  ].filter(group => group.items.length > 0)
})
</script>