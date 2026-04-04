<template>
  <div class="flex flex-wrap gap-4 text-sm text-gray-500">
    <span>Status: {{ statusLabel }}</span>

    <span class="flex items-center gap-1">
      Due:
      <DateDisplay
          v-if="dueDate"
          :date="dueDate"
          format="short"
      />
      <span v-else>No due date</span>
    </span>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import DateDisplay from '@/components/atoms/DateDisplay/DateDisplay.vue'

const props = defineProps({
  status: {
    type: [String, Number],
    default: '',
  },
  dueDate: {
    type: String,
    default: '',
  },
})

const statusLabel = computed(() => {
  const value = String(props.status).toLowerCase()

  const map = {
    1: 'Created',
    2: 'In Progress',
    3: 'Done',
    created: 'Created',
    pending: 'Created',
    in_progress: 'In Progress',
    'in progress': 'In Progress',
    done: 'Done',
    completed: 'Done',
  }

  return map[value] || props.status || 'Unknown'
})
</script>