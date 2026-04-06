<template>
  <AppLayout>
    <DashboardOverview
        :tasks="tasks"
        :loading="loading"
        :error="error"
        :success-message="successMessage"
        :search="search"
        :status="statusFilter"
        :priority="priorityFilter"
        :category="categoryFilter"
        :sort="sortBy"
        :categories="categories"
        :saving-task-id="savingTaskId"
        :current-page="currentPage"
        :total-pages="totalPages"
        :total="total"
        :stats="stats"
        @update:search="search = $event"
        @update:status="statusFilter = $event"
        @update:priority="priorityFilter = $event"
        @update:category="categoryFilter = $event"
        @update:sort="sortBy = $event"
        @update:page="currentPage = $event"
        @dismiss-success-message="successMessage = ''"
        @delete="handleDeleteTask"
        @save-edit="handleSaveEdit"
        @change-status="handleChangeStatus"
    />
  </AppLayout>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from '@/utils/axios.js'
import AppLayout from '@/components/templates/AppLayout/AppLayout.vue'
import DashboardOverview from '@/components/organisms/DashboardOverview/DashboardOverview.vue'

const tasks = ref([])
const categories = ref([])
const loading = ref(true)
const error = ref('')
const successMessage = ref('')
const savingTaskId = ref(null)

const currentPage = ref(1)
const pageSize = ref(10)
const total = ref(0)
const totalPages = ref(1)

const stats = ref({
  totalTasks: 0,
  done: 0,
  pending: 0,
  overdue: 0,
})

const route = useRoute()
const router = useRouter()

const search = ref('')
const statusFilter = ref('')
const priorityFilter = ref('')
const categoryFilter = ref('')
const sortBy = ref('')

const formatStatusLabel = (value) => {
  const normalized = String(value ?? '').trim().toLowerCase()

  const map = {
    created: 'Created',
    'in progress': 'In Progress',
    in_progress: 'In Progress',
    done: 'Done',
    completed: 'Done',
    cancelled: 'Cancelled',
  }

  return map[normalized] || String(value ?? '').trim()
}

const unwrapResponseData = (response) => response?.data?.data ?? response?.data ?? response

const getTaskParams = () => ({
  search: search.value || undefined,
  status: statusFilter.value || undefined,
  priority: priorityFilter.value || undefined,
  category_id: categoryFilter.value || undefined,
  sort: sortBy.value || undefined,
  page: currentPage.value,
  limit: pageSize.value,
})

const getStatsParams = () => ({
  search: search.value || undefined,
  status: statusFilter.value || undefined,
  priority: priorityFilter.value || undefined,
  category_id: categoryFilter.value || undefined,
})

const loadTasks = async () => {
  loading.value = true
  error.value = ''

  try {
    const response = await axios.get('/tasks/dashboard', {
      params: getTaskParams(),
    })

    const payload = response.data?.data ?? []
    const pagination = response.data?.pagination ?? {}

    tasks.value = Array.isArray(payload) ? payload : []
    total.value = pagination.total ?? 0
    totalPages.value = Math.max(1, pagination.totalPages ?? 1)

    // If current page no longer exists (for example after deleting the last task
    // on the last page), jump to the last valid page and let the page watcher reload.
    if (currentPage.value > totalPages.value) {
      currentPage.value = totalPages.value
      return
    }
  } catch (err) {
    console.error('Load tasks error:', err)
    error.value =
        err.response?.data?.error ||
        err.response?.data?.message ||
        err.message ||
        'Failed to load tasks'

    tasks.value = []
    total.value = 0
    totalPages.value = 1
  } finally {
    loading.value = false
  }
}

const loadStats = async () => {
  try {
    const response = await axios.get('/tasks/dashboard-stats')
    stats.value = response.data?.data ?? response.data ?? {
      totalTasks: 0,
      done: 0,
      pending: 0,
      overdue: 0,
    }
  } catch (err) {
    console.error('Load stats error:', err)
    stats.value = {
      totalTasks: 0,
      done: 0,
      pending: 0,
      overdue: 0,
    }
  }
}

const loadCategories = async () => {
  try {
    const response = await axios.get('/categories')
    const payload = unwrapResponseData(response)

    categories.value = Array.isArray(payload) ? payload : []
  } catch (err) {
    console.error('Load categories error:', err)
    error.value =
        err.response?.data?.error ||
        err.response?.data?.message ||
        err.message ||
        'Failed to load categories'
  }
}

const loadInitialData = async () => {
  await Promise.all([loadTasks(), loadStats(), loadCategories()])
}

const applyRouteSuccessMessage = async () => {
  const created = route.query.created

  if (created) {
    successMessage.value = 'Task created successfully.'

    const { created: _created, ...query } = route.query

    await router.replace({
      name: route.name || 'dashboard',
      query,
    })
  }
}

const handleDeleteTask = async (task) => {
  const confirmed = window.confirm(`Delete task "${task.title}"?`)
  if (!confirmed) return

  error.value = ''
  savingTaskId.value = task.id

  try {
    await axios.delete(`/tasks/${task.id}`)
    await Promise.all([loadTasks(), loadStats()])
    successMessage.value = 'Task deleted successfully.'
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
    await Promise.all([loadTasks(), loadStats()])
    successMessage.value = 'Task updated successfully.'
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

    successMessage.value = `Task status changed to ${formatStatusLabel(status)}.`
    await Promise.all([loadTasks(), loadStats()])

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

watch([search, statusFilter, priorityFilter, categoryFilter, sortBy], async () => {
  error.value = ''
  await loadStats()

  if (currentPage.value !== 1) {
    currentPage.value = 1
    return
  }

  await loadTasks()
})

watch(currentPage, async () => {
  await loadTasks()
  window.scrollTo({ top: 0, behavior: 'smooth' })
})

onMounted(async () => {
  await applyRouteSuccessMessage()
  await loadInitialData()
})
</script>