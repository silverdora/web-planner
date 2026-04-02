<template>
  <header class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <div class="flex items-center">
          <router-link
              to="/dashboard"
              class="text-2xl font-bold text-blue-600 hover:text-blue-700 transition-colors"
          >
            Web Planner
          </router-link>
        </div>

        <nav class="hidden md:flex items-center space-x-6">
          <router-link
              v-for="link in navigationLinks"
              :key="link.name"
              :to="link.href"
              class="text-gray-700 hover:text-blue-600 transition-colors font-medium"
          >
            {{ link.name }}
          </router-link>

          <button
              @click="handleLogout"
              class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md transition-colors font-medium"
          >
            Logout
          </button>
        </nav>

        <div class="md:hidden flex items-center gap-2">
          <button
              @click="mobileMenuOpen = !mobileMenuOpen"
              class="text-gray-700 hover:text-blue-600 transition-colors"
              aria-label="Toggle menu"
          >
            <svg
                class="h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
              <path
                  v-if="!mobileMenuOpen"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16"
              />
              <path
                  v-else
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>
        </div>
      </div>

      <div v-if="mobileMenuOpen" class="md:hidden pb-4">
        <nav class="flex flex-col space-y-2">
          <router-link
              v-for="link in navigationLinks"
              :key="link.name"
              :to="link.href"
              class="text-gray-700 hover:text-blue-600 transition-colors px-2 py-1"
              @click="mobileMenuOpen = false"
          >
            {{ link.name }}
          </router-link>

          <button
              @click="handleLogout"
              class="text-left bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-md transition-colors mt-2"
          >
            Logout
          </button>
        </nav>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { setAuthToken } from '@/utils/axios.js'

defineProps({
  navigationLinks: {
    type: Array,
    default: () => [
      { name: 'Dashboard', href: '/dashboard' },
      { name: 'Tasks', href: '/tasks' },
      { name: 'Create Task', href: '/tasks/create' },
    ],
  },
})

const mobileMenuOpen = ref(false)
const router = useRouter()

const handleLogout = () => {
  setAuthToken(null)
  localStorage.removeItem('user')
  mobileMenuOpen.value = false
  router.replace('/')
}
</script>
