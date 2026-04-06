<template>
  <section class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
      <div>
        <Heading :level="1">My Tasks</Heading>
        <Text color="muted" class="mt-1">
          Overview of your current tasks and deadlines
        </Text>
      </div>

      <div class="flex flex-col gap-2 sm:flex-row">
        <router-link
            to="/tasks/completed"
            class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50"
        >
          View completed
        </router-link>

        <router-link
            to="/tasks/create"
            class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-blue-700"
        >
          Add new task
        </router-link>
      </div>
    </div>

    <div
        v-if="successMessage"
        class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700"
    >
      <div class="flex items-start justify-between gap-3">
        <p class="pr-2">
          {{ successMessage }}
        </p>

        <button
            type="button"
            class="shrink-0 text-green-700 transition-colors hover:text-green-900"
            aria-label="Dismiss success message"
            @click="$emit('dismiss-success-message')"
        >
          ×
        </button>
      </div>
    </div>

    <DashboardStats :stats="stats" />

    <CategoryStats :items="categoryStats" />

    <UpcomingDeadlines
        :upcoming="upcoming"
        @go-to-task="$emit('go-to-task', $event)"
    />

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

    <template v-else>
      <GroupedTaskList
          :tasks="tasks"
          :categories="categories"
          :saving-task-id="savingTaskId"
          @delete="$emit('delete', $event)"
          @save-edit="$emit('save-edit', $event)"
          @change-status="$emit('change-status', $event)"
      />

      <PaginationControls
          :current-page="currentPage"
          :total-pages="totalPages"
          :total="total"
          :disabled="loading"
          @update:page="$emit('update:page', $event)"
      />
    </template>
  </section>
</template>

<script setup>
import Heading from '@/components/atoms/Heading/Heading.vue'
import Text from '@/components/atoms/Text/Text.vue'
import FeedbackMessage from '@/components/molecules/FeedbackMessage/FeedbackMessage.vue'
import PaginationControls from '@/components/molecules/PaginationControls/PaginationControls.vue'
import DashboardFilters from '@/components/organisms/DashboardFilters/DashboardFilters.vue'
import DashboardStats from '@/components/organisms/DashboardStats/DashboardStats.vue'
import UpcomingDeadlines from '@/components/organisms/UpcomingDeadlines/UpcomingDeadlines.vue'
import GroupedTaskList from '@/components/organisms/GroupedTaskList/GroupedTaskList.vue'
import CategoryStats from '@/components/organisms/CategoryStats/CategoryStats.vue'

defineProps({
  tasks: { type: Array, default: () => [] },
  loading: { type: Boolean, default: false },
  error: { type: String, default: '' },
  successMessage: { type: String, default: '' },
  search: { type: String, default: '' },
  status: { type: String, default: '' },
  priority: { type: String, default: '' },
  category: { type: String, default: '' },
  sort: { type: String, default: '' },
  categories: { type: Array, default: () => [] },
  savingTaskId: { type: [Number, String, null], default: null },
  currentPage: { type: Number, default: 1 },
  totalPages: { type: Number, default: 1 },
  total: { type: Number, default: 0 },
  stats: { type: Object, default: () => ({ totalTasks: 0, done: 0, pending: 0, overdue: 0 }) },
  categoryStats: {type: Array, default: () => [],},
  upcoming: { type: Object, default: () => ({overdue: [], today: [], week: [],}),},
})

defineEmits([
  'update:search',
  'update:status',
  'update:priority',
  'update:category',
  'update:sort',
  'update:page',
  'dismiss-success-message',
  'delete',
  'save-edit',
  'change-status',
  'go-to-task',
])
</script>