<template>
  <div class="grid gap-4 md:grid-cols-2">
    <TaskCard
        v-for="task in tasks"
        :key="task.id"
        :task="task"
        :categories="categories"
        :saving="savingTaskId === task.id"
        @delete="$emit('delete', $event)"
        @save-edit="$emit('save-edit', $event)"
        @change-status="$emit('change-status', $event)"
    />
  </div>
</template>

<script setup>
import TaskCard from '@/components/organisms/TaskCard/TaskCard.vue'

defineProps({
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
</script>