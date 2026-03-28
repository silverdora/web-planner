<template>
  <div class="dashboard-page">
    <div class="container">
      <h1>Web Planner</h1>
      <p class="subtitle">Organize your tasks, projects, and deadlines in one place.</p>

      <div class="buttons">
        <button @click="goToTasks">View Tasks</button>
        <button class="secondary" @click="goToCreateTask">Create Task</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

const goToTasks = () => {
  router.push('/tasks')
}

const goToCreateTask = () => {
  router.push('/tasks/create')
}
</script>

<style scoped>
.dashboard-page {
  min-height: calc(100vh - 64px);
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f5f7fb;
  padding: 20px;
}

.container {
  text-align: center;
  background: white;
  padding: 40px;
  border-radius: 16px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
  max-width: 600px;
  width: 100%;
}

h1 {
  font-size: 42px;
  margin-bottom: 12px;
  color: #222;
}

.subtitle {
  font-size: 18px;
  color: #666;
  margin-bottom: 28px;
}

.buttons {
  display: flex;
  gap: 12px;
  justify-content: center;
  flex-wrap: wrap;
}

button {
  padding: 12px 20px;
  border: none;
  border-radius: 10px;
  background: #3b82f6;
  color: white;
  font-size: 16px;
  cursor: pointer;
}

button:hover {
  opacity: 0.9;
}

.secondary {
  background: #10b981;
}
</style>