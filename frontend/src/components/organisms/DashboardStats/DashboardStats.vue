<template>
  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <StatCard title="Total Tasks" :value="stats.totalTasks ?? 0" />
    <StatCard title="Done" :value="stats.done ?? 0" />
    <StatCard title="Pending" :value="stats.pending ?? 0" />
    <StatCard title="Overdue" :value="stats.overdue ?? 0" />
  </div>
</template>

<script setup>
import StatCard from '@/components/molecules/StatCard/StatCard.vue'

defineProps({
  stats: {
    type: Object,
    default: () => ({
      totalTasks: 0,
      done: 0,
      pending: 0,
      overdue: 0,
    }),
  },
})
</script>