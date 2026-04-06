<template>
  <div class="app-layout">
    <Header v-if="showHeader" />
    <router-view />
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import Header from '@/components/organisms/Header/Header.vue'
import { useAuthStore } from '@/stores/auth.js'

const route = useRoute()
const authStore = useAuthStore()

onMounted(() => {
  authStore.initialize()
})

const showHeader = computed(() => {
  const requiresAuth = route.matched.some(record => record.meta.requiresAuth)
  return authStore.isAuthenticated && requiresAuth
})
</script>

<style>
body {
  margin: 0;
  font-family: Arial, sans-serif;
  background: #f5f7fb;
}

* {
  box-sizing: border-box;
}

.app-layout {
  min-height: 100vh;
}
</style>