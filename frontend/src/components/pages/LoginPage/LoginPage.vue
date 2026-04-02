<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="max-w-md w-full bg-white shadow-md rounded-lg p-8">
      <h1 class="text-2xl font-bold mb-6 text-center">Login</h1>

      <form @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
            Email
          </label>
          <input
              id="email"
              v-model="email"
              type="email"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Enter your email"
          />
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
            Password
          </label>
          <input
              id="password"
              v-model="password"
              type="password"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Enter your password"
          />
        </div>

        <div v-if="error" class="text-red-600 text-sm">
          {{ error }}
        </div>

        <button
            type="submit"
            :disabled="loading"
            class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ loading ? 'Logging in...' : 'Login' }}
        </button>
      </form>

      <div class="mt-4 flex flex-col items-center gap-2">
        <router-link to="/register" class="text-green-600 hover:text-green-800 underline text-sm">
          Don't have an account? Register
        </router-link>

        <router-link to="/" class="text-blue-600 hover:text-blue-800 underline text-sm">
          Back to Home
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios, { setAuthToken } from '@/utils/axios.js'

const router = useRouter()
const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')

const handleLogin = async () => {
  loading.value = true
  error.value = ''

  try {
    const response = await axios.post('/auth/login', {
      email: email.value,
      password: password.value,
    })

    if (response.data.token) {
      setAuthToken(response.data.token)

      if (response.data.user) {
        localStorage.setItem('user', JSON.stringify(response.data.user))
      }

      router.push('/dashboard')
    } else {
      error.value = 'No token received from server'
    }
  } catch (err) {
    console.error('Login error:', err)
    if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else {
      error.value = 'Login failed. Please check your credentials.'
    }
  } finally {
    loading.value = false
  }
}
</script>