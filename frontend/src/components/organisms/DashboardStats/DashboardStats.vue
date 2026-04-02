<template>
  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <StatCard title="Total Tasks" :value="tasks.length" />
    <StatCard title="Completed" :value="completedCount" />
    <StatCard title="Pending" :value="pendingCount" />
    <StatCard title="Overdue" :value="overdueCount" />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import StatCard from '@/components/molecules/StatCard/StatCard.vue'

const props = defineProps({
  tasks: {
    type: Array,
    default: () => [],
  },
})

const normalizeStatus = (status) => String(status).toLowerCase()

const completedCount = computed(() =>
    props.tasks.filter((task) => {
      const value = normalizeStatus(task.status || task.status_id)
      return value === 'completed' || value === '3'
    }).length
)

const pendingCount = computed(() =>
    props.tasks.filter((task) => {
      const value = normalizeStatus(task.status || task.status_id)
      return value !== 'completed' && value !== '3'
    }).length
)

const overdueCount = computed(() => {
  const now = new Date()

  return props.tasks.filter((task) => {
    const dueDate = task.dueDate || task.due_date
    const status = normalizeStatus(task.status || task.status_id)

    if (!dueDate) return false
    if (status === 'completed' || status === '3') return false

    return new Date(dueDate) < now
  }).length
})
</script>