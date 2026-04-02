<template>
  <AppLayout>
    <DashboardOverview
        :tasks="filteredTasks"
        :loading="loading"
        :error="error"
        :search="search"
        :status="statusFilter"
        :priority="priorityFilter"
        :sort="sortBy"
        @update:search="search = $event"
        @update:status="statusFilter = $event"
        @update:priority="priorityFilter = $event"
        @update:sort="sortBy = $event"
        @edit="handleEditTask"
        @delete="handleDeleteTask"
    />
  </AppLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import axios from '@/utils/axios.js'

import AppLayout from '@/components/templates/AppLayout/AppLayout.vue'
import DashboardOverview from '@/components/organisms/DashboardOverview/DashboardOverview.vue'

const router = useRouter()

const tasks = ref([])
const loading = ref(true)
const error = ref('')

const search = ref('')
const statusFilter = ref('')
const priorityFilter = ref('')
const sortBy = ref('')

const normalizeStatus = (value) => {
  const normalized = String(value ?? '').toLowerCase()

  const map = {
    '1': 'pending',
    '2': 'in progress',
    '3': 'completed',
    pending: 'pending',
    in_progress: 'in progress',
    'in progress': 'in progress',
    completed: 'completed',
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

const filteredTasks = computed(() => {
  let result = [...tasks.value]

  if (search.value.trim()) {
    const query = search.value.trim().toLowerCase()
    result = result.filter((task) =>
        String(task.title ?? '').toLowerCase().includes(query)
    )
  }

  if (statusFilter.value) {
    result = result.filter((task) => {
      return normalizeStatus(task.status || task.status_id) === statusFilter.value
    })
  }

  if (priorityFilter.value) {
    result = result.filter((task) => {
      return normalizePriority(task.priority || task.priority_id) === priorityFilter.value
    })
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

  if (sortBy.value === 'title_asc') {
    result.sort((a, b) => String(a.title).localeCompare(String(b.title)))
  }

  if (sortBy.value === 'title_desc') {
    result.sort((a, b) => String(b.title).localeCompare(String(a.title)))
  }

  return result
})

const loadTasks = async () => {
  loading.value = true
  error.value = ''

  try {
    const response = await axios.get('/tasks/dashboard')
    const payload = response.data.data ?? response.data

    if (!Array.isArray(payload)) {
      console.error('Unexpected payload:', payload)
      error.value = 'Unexpected response from server'
      tasks.value = []
      return
    }

    tasks.value = payload
  } catch (err) {
    console.error('Failed to load tasks:', err)
    error.value = err.response?.data?.message || 'Failed to load tasks'
    tasks.value = []
  } finally {
    loading.value = false
  }
}

const handleEditTask = (task) => {
  router.push(`/tasks/${task.id}/edit`)
}

const handleDeleteTask = async (task) => {
  const confirmed = window.confirm(`Delete task "${task.title}"?`)

  if (!confirmed) return

  try {
    await axios.delete(`/tasks/${task.id}`)
    tasks.value = tasks.value.filter((item) => item.id !== task.id)
  } catch (err) {
    console.error('Delete task error:', err)
    error.value = err.response?.data?.message || 'Failed to delete task'
  }
}

onMounted(loadTasks)
</script>