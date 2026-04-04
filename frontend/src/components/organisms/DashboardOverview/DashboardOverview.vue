<template>
  <section class="space-y-6">
    <div>
      <Heading :level="1">My Tasks</Heading>
      <Text color="muted" class="mt-1">
        Overview of your current tasks and deadlines
      </Text>
    </div>

    <DashboardStats :tasks="tasks" />

    <DashboardFilters
        :search="search"
        :status="status"
        :priority="priority"
        :category="category"
        :sort="sort"
        :categories="categories"
        @update:search="$emit('update:search', $event)"
        @update:status="$emit('update:status', $event)"
        @update:priority="$emit('update:priority', $event)"
        @update:category="$emit('update:category', $event)"
        @update:sort="$emit('update:sort', $event)"
    />

    <FeedbackMessage
        v-if="loading"
        type="info"
        message="Loading tasks..."
    />

    <FeedbackMessage
        v-else-if="error"
        type="error"
        :message="error"
    />

    <FeedbackMessage
        v-else-if="tasks.length === 0"
        type="info"
        message="No tasks found."
    />

    <TaskList
        v-else
        :tasks="tasks"
        :saving-task-id="savingTaskId"
        @delete="$emit('delete', $event)"
        @save-edit="$emit('save-edit', $event)"
    />
  </section>
</template>

<script setup>
import Heading from '@/components/atoms/Heading/Heading.vue'
import Text from '@/components/atoms/Text/Text.vue'
import FeedbackMessage from '@/components/molecules/FeedbackMessage/FeedbackMessage.vue'
import DashboardFilters from '@/components/molecules/DashboardFilters/DashboardFilters.vue'
import DashboardStats from '@/components/organisms/DashboardStats/DashboardStats.vue'
import TaskList from '@/components/organisms/TaskList/TaskList.vue'

defineProps({
  tasks: { type: Array, default: () => [] },
  loading: { type: Boolean, default: false },
  error: { type: String, default: '' },
  search: { type: String, default: '' },
  status: { type: String, default: '' },
  priority: { type: String, default: '' },
  category: { type: String, default: '' },
  sort: { type: String, default: '' },
  categories: { type: Array, default: () => [] },
  savingTaskId: { type: [Number, String, null], default: null },
})

defineEmits([
  'update:search',
  'update:status',
  'update:priority',
  'update:category',
  'update:sort',
  'delete',
  'save-edit',
])
</script>