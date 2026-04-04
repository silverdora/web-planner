<template>
  <AppLayout>
    <DashboardOverview
        :tasks="filteredTasks"
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
        @update:search="search = $event"
        @update:status="statusFilter = $event"
        @update:priority="priorityFilter = $event"
        @update:category="categoryFilter = $event"
        @update:sort="sortBy = $event"
        @dismiss-success-message="successMessage = ''"
        @delete="handleDeleteTask"
        @save-edit="handleSaveEdit"
        @change-status="handleChangeStatus"
    />
  </AppLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
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

const route = useRoute()
const router = useRouter()

const search = ref('')
const statusFilter = ref('')
const priorityFilter = ref('')
const categoryFilter = ref('')
const sortBy = ref('')

const normalizeStatus = (value) => {
  const normalized = String(value ?? '').toLowerCase()

  const map = {
    '1': 'created',
    '2': 'in progress',
    '3': 'done',
    created: 'created',
    in_progress: 'in progress',
    'in progress': 'in progress',
    completed: 'done',
    done: 'done',
  }

  return map[normalized] || normalized
}

const normalizePriority = (value) => {
  const normalized = String(value ?? '').toLowerCase()

  const map = {
    '1': 'low',
    '2': 'medium',
    '3': 'high',
    low: 'low',
    medium: 'medium',
    high: 'high',
  }

  return map[normalized] || normalized
}

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

const getTaskCategoryId = (task) => {
  return String(task.category_id ?? task.categoryId ?? '')
}

const getTaskCategoryName = (task) => {
  const directName = String(task.category_name ?? task.categoryName ?? task.category?.name ?? '').trim()
  if (directName) return directName

  const categoryId = getTaskCategoryId(task)
  if (!categoryId) return ''

  const matchedCategory = categories.value.find(
      (category) => String(category.id) === categoryId
  )

  return String(matchedCategory?.name ?? '').trim()
}

const filteredTasks = computed(() => {
  let result = [...tasks.value]

  if (search.value.trim()) {
    const query = search.value.trim().toLowerCase()
    result = result.filter((task) =>
        String(task.title ?? '').toLowerCase().includes(query)
    )
  }

  if (statusFilter.value) {
    result = result.filter((task) =>
        normalizeStatus(task.status || task.status) === statusFilter.value
    )
  }

  if (priorityFilter.value) {
    result = result.filter((task) =>
        normalizePriority(task.priority || task.priority) === priorityFilter.value
    )
  }

  if (categoryFilter.value) {
    result = result.filter((task) =>
        getTaskCategoryId(task) === String(categoryFilter.value)
    )
  }

  if (sortBy.value === 'due_asc') {
    result.sort((a, b) => {
      const aDate = a.dueDate || a.due_date
      const bDate = b.dueDate || b.due_date
      return new Date(aDate || 0) - new Date(bDate || 0)
    })
  }

  if (sortBy.value === 'due_desc') {
    result.sort((a, b) => {
      const aDate = a.dueDate || a.due_date
      const bDate = b.dueDate || b.due_date
      return new Date(bDate || 0) - new Date(aDate || 0)
    })
  }

  if (sortBy.value === 'category_asc') {
    result.sort((a, b) => {
      const categoryCompare = getTaskCategoryName(a).localeCompare(getTaskCategoryName(b))
      if (categoryCompare !== 0) return categoryCompare
      return String(a.title ?? '').localeCompare(String(b.title ?? ''))
    })
  }

  if (sortBy.value === 'category_desc') {
    result.sort((a, b) => {
      const categoryCompare = getTaskCategoryName(b).localeCompare(getTaskCategoryName(a))
      if (categoryCompare !== 0) return categoryCompare
      return String(a.title ?? '').localeCompare(String(b.title ?? ''))
    })
  }

  if (sortBy.value === 'title_asc') {
    result.sort((a, b) => String(a.title).localeCompare(String(b.title)))
  }

  if (sortBy.value === 'title_desc') {
    result.sort((a, b) => String(b.title).localeCompare(String(a.title)))
  }

  return result
})

const loadTasks = async () => {
  const response = await axios.get('/tasks/dashboard')
  const payload = response.data.data ?? response.data

  if (!Array.isArray(payload)) {
    throw new Error('Unexpected tasks response')
  }

  tasks.value = payload
}

const loadCategories = async () => {
  const response = await axios.get('/categories')
  const payload = response.data.data ?? response.data

  if (!Array.isArray(payload)) {
    throw new Error('Unexpected categories response')
  }

  categories.value = payload
}

const loadDashboardData = async () => {
  loading.value = true
  error.value = ''

  try {
    await Promise.all([loadTasks(), loadCategories()])
  } catch (err) {
    console.error('Dashboard load error:', err)
    error.value =
        err.response?.data?.error ||
        err.response?.data?.message ||
        err.message ||
        'Failed to load dashboard data'
    tasks.value = []
    categories.value = []
  } finally {
    loading.value = false
  }
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

  try {
    await axios.delete(`/tasks/${task.id}`)
    tasks.value = tasks.value.filter((item) => item.id !== task.id)
  } catch (err) {
    console.error('Delete task error:', err)
    error.value =
        err.response?.data?.error ||
        err.response?.data?.message ||
        err.message || 'Failed to delete task'
  }
}

const handleSaveEdit = async ({ id, payload, onSuccess, onError }) => {
  savingTaskId.value = id
  error.value = ''

  try {
    const response = await axios.put(`/tasks/${id}`, payload)
    const updatedTask = response.data.data ?? response.data

    const index = tasks.value.findIndex((task) => task.id === id)

    if (index !== -1) {
      tasks.value[index] = {
        ...tasks.value[index],
        ...payload,
        ...(typeof updatedTask === 'object' && updatedTask !== null ? updatedTask : {}),
      }
    }

    if (onSuccess) onSuccess()
  } catch (err) {
    console.error('Update task error:', err)
    const message = err.response?.data?.message || 'Failed to update task'
    if (onError) onError(message)
  } finally {
    savingTaskId.value = null
  }
}

const handleChangeStatus = async ({ id, status, onSuccess, onError }) => {
  savingTaskId.value = id
  error.value = ''

  try {
    const response = await axios.put('/tasks/change-status', {
      taskId: id,
      status,
    })

    const updatedTask = response.data.data ?? response.data
    const index = tasks.value.findIndex((task) => task.id === id)

    if (index !== -1) {
      tasks.value[index] = {
        ...tasks.value[index],
        status,
        ...(typeof updatedTask === 'object' && updatedTask !== null ? updatedTask : {}),
      }
    }

    successMessage.value = `Task status changed to ${formatStatusLabel(status)}.`

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
  await applyRouteSuccessMessage()
  await loadDashboardData()
})
</script>