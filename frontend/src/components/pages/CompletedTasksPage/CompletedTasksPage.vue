<template>
  <AppLayout>
    <section class="space-y-6">
      <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
          <Heading :level="1">Completed tasks</Heading>
          <Text color="muted" class="mt-1">
            View all tasks that have already been completed.
          </Text>
        </div>

        <router-link
            to="/dashboard"
            class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-blue-700"
        >
          Back to dashboard
        </router-link>
      </div>

      <FeedbackMessage
          v-if="loading"
          type="info"
          message="Loading completed tasks..."
      />

      <FeedbackMessage
          v-else-if="error"
          type="error"
          :message="error"
      />

      <FeedbackMessage
          v-else-if="completedTasks.length === 0"
          type="info"
          message="No completed tasks yet."
      />

      <TaskList
          v-else
          :tasks="completedTasks"
          :categories="categories"
          :saving-task-id="savingTaskId"
          @delete="handleDeleteTask"
          @save-edit="handleSaveEdit"
          @change-status="handleChangeStatus"
      />
    </section>
  </AppLayout>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import axios from '@/utils/axios.js'

import AppLayout from '@/components/templates/AppLayout/AppLayout.vue'
import Heading from '@/components/atoms/Heading/Heading.vue'
import Text from '@/components/atoms/Text/Text.vue'
import FeedbackMessage from '@/components/molecules/FeedbackMessage/FeedbackMessage.vue'
import TaskList from '@/components/organisms/TaskList/TaskList.vue'

const tasks = ref([])
const categories = ref([])
const loading = ref(true)
const error = ref('')
const savingTaskId = ref(null)

const normalizeStatus = (value) => {
  const normalized = String(value ?? '').trim().toLowerCase()

  const map = {
    done: 'done',
    completed: 'done',
    '3': 'done',
    created: 'created',
    'in progress': 'in progress',
    in_progress: 'in progress',
    cancelled: 'cancelled',
  }

  return map[normalized] || normalized
}

const completedTasks = computed(() =>
    tasks.value.filter((task) => normalizeStatus(task.status) === 'done')
)

const unwrapResponseData = (response) => response?.data?.data ?? response?.data ?? response

const loadTasks = async () => {
  loading.value = true
  error.value = ''

  try {
    const response = await axios.get('/tasks/dashboard', {
      params: {
        status: 'done',
        page: 1,
        limit: 100,
      },
    })

    const payload = response.data?.data ?? []
    tasks.value = Array.isArray(payload) ? payload : []
  } catch (err) {
    console.error('Load completed tasks error:', err)
    error.value =
        err.response?.data?.error ||
        err.response?.data?.message ||
        err.message ||
        'Failed to load completed tasks'

    tasks.value = []
  } finally {
    loading.value = false
  }
}

const loadCategories = async () => {
  try {
    const response = await axios.get('/categories')
    const payload = unwrapResponseData(response)
    categories.value = Array.isArray(payload) ? payload : []
  } catch (err) {
    console.error('Load categories error:', err)
    categories.value = []
  }
}

const handleDeleteTask = async (task) => {
  const confirmed = window.confirm(`Delete task "${task.title}"?`)
  if (!confirmed) return

  savingTaskId.value = task.id
  error.value = ''

  try {
    await axios.delete(`/tasks/${task.id}`)
    await loadTasks()
  } catch (err) {
    console.error('Delete task error:', err)
    error.value =
        err.response?.data?.error ||
        err.response?.data?.message ||
        err.message ||
        'Failed to delete task'
  } finally {
    savingTaskId.value = null
  }
}

const handleSaveEdit = async ({ id, payload, onSuccess, onError }) => {
  savingTaskId.value = id
  error.value = ''

  try {
    await axios.put(`/tasks/${id}`, payload)
    await loadTasks()
    if (onSuccess) onSuccess()
  } catch (err) {
    console.error('Update task error:', err)
    const message =
        err.response?.data?.error ||
        err.response?.data?.message ||
        err.message ||
        'Failed to update task'

    if (onError) onError(message)
  } finally {
    savingTaskId.value = null
  }
}

const handleChangeStatus = async ({ id, status, onSuccess, onError }) => {
  savingTaskId.value = id
  error.value = ''

  try {
    await axios.put('/tasks/change-status', {
      taskId: id,
      status,
    })

    await loadTasks()
    if (onSuccess) onSuccess()
  } catch (err) {
    console.error('Change task status error:', err)
    const message =
        err.response?.data?.error ||
        err.response?.data?.message ||
        err.message ||
        'Failed to change task status'

    if (onError) onError(message)
  } finally {
    savingTaskId.value = null
  }
}

onMounted(async () => {
  await Promise.all([loadTasks(), loadCategories()])
})
</script>