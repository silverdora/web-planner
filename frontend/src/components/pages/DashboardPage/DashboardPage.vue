<template>
  <div class="dashboard">
    <div class="container">
      <h1>My Tasks</h1>

      <div v-if="loading" class="info">Loading tasks...</div>
      <div v-else-if="error" class="error">{{ error }}</div>
      <div v-else-if="tasks.length === 0" class="info">No tasks found.</div>

      <div v-else class="task-list">
        <div v-for="task in tasks" :key="task.id" class="task-card">
          <div class="task-header">
            <h3>{{ task.title }}</h3>
            <span class="badge priority">{{ task.priority }}</span>
          </div>

          <p class="description">{{ task.description || 'No description' }}</p>

          <div class="task-meta">
            <span>Status: {{ task.status }}</span>
            <span>Due: {{ formatDate(task.dueDate || task.due_date) }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '@/utils/axios.js'

const tasks = ref([])
const loading = ref(true)
const error = ref('')

const formatDate = (dateString) => {
  if (!dateString) return 'No due date'
  return new Date(dateString).toLocaleString()
}

const loadTasks = async () => {
  loading.value = true
  error.value = ''

  try {
    const response = await axios.get('/tasks/dashboard')

    console.log('FULL RESPONSE:', response)
    console.log('RESPONSE DATA:', response.data)

    const payload = response.data.data ?? response.data

    if (!Array.isArray(payload)) {
      console.error('Unexpected payload:', payload)
      error.value = 'Unexpected response from server'
      tasks.value = []
      return
    }

    tasks.value = payload
  } catch (err) {
    console.error(err)
    error.value = err.response?.data?.message || 'Failed to load tasks'
    tasks.value = []
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadTasks()
})
</script>

<style scoped>
.dashboard {
  min-height: 100vh;
  background: #f5f7fb;
  padding: 40px 20px;
  font-family: Arial, sans-serif;
}

.container {
  max-width: 900px;
  margin: 0 auto;
}

h1 {
  font-size: 36px;
  margin-bottom: 8px;
  color: #222;
}

.subtitle {
  color: #666;
  margin-bottom: 24px;
}

.info {
  background: white;
  padding: 20px;
  border-radius: 12px;
  color: #555;
}

.error {
  background: #fee2e2;
  color: #b91c1c;
  padding: 20px;
  border-radius: 12px;
}

.task-list {
  display: grid;
  gap: 16px;
}

.task-card {
  background: white;
  border-radius: 14px;
  padding: 20px;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
}

.task-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
}

.task-header h3 {
  margin: 0;
  color: #222;
}

.description {
  margin: 12px 0;
  color: #555;
}

.task-meta {
  display: flex;
  gap: 16px;
  flex-wrap: wrap;
  color: #777;
  font-size: 14px;
}

.badge {
  padding: 6px 10px;
  border-radius: 999px;
  font-size: 13px;
  background: #dbeafe;
  color: #1d4ed8;
}
</style>