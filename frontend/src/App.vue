<template>
  <div class="app-layout">
    <Header v-if="showHeader" />
    <router-view />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import Header from '@/components/organisms/Header/Header.vue'
import { getAuthToken } from '@/utils/axios.js'

const route = useRoute()

const showHeader = computed(() => {
  const isLoggedIn = !!getAuthToken()
  const requiresAuth = route.matched.some(record => record.meta.requiresAuth)

  return isLoggedIn && requiresAuth
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