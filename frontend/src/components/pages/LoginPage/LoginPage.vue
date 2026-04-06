<template>
  <AuthLayout>
    <section class="rounded-3xl border border-gray-200 bg-white p-8 shadow-sm">
      <Heading :level="1" size="xl" class="text-center">
        Login
      </Heading>

      <Text color="muted" class="mt-2 text-center">
        Sign in to manage your planner
      </Text>

      <form @submit.prevent="handleLogin" class="mt-6 space-y-4">
        <FormField
            id="email"
            v-model="email"
            label="Email"
            type="email"
            placeholder="Enter your email"
            :disabled="loading"
        />

        <FormField
            id="password"
            v-model="password"
            label="Password"
            type="password"
            placeholder="Enter your password"
            :disabled="loading"
        />

        <FeedbackMessage
            v-if="error"
            type="error"
            :message="error"
        />

        <BaseButton
            type="submit"
            variant="primary"
            size="lg"
            :disabled="loading"
            :full-width="true"
        >
          {{ loading ? 'Logging in...' : 'Login' }}
        </BaseButton>
      </form>

      <div class="mt-6 flex flex-col items-center gap-2">
        <router-link
            to="/register"
            class="text-sm font-medium text-green-600 underline hover:text-green-800"
        >
          Don't have an account? Register
        </router-link>

        <router-link
            to="/"
            class="text-sm font-medium text-blue-600 underline hover:text-blue-800"
        >
          Back to Home
        </router-link>
      </div>
    </section>
  </AuthLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth.js'

import AuthLayout from '@/components/templates/AuthLayout/AuthLayout.vue'
import Heading from '@/components/atoms/Heading/Heading.vue'
import Text from '@/components/atoms/Text/Text.vue'
import BaseButton from '@/components/atoms/BaseButton/BaseButton.vue'
import FormField from '@/components/molecules/FormField/FormField.vue'
import FeedbackMessage from '@/components/molecules/FeedbackMessage/FeedbackMessage.vue'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')

const handleLogin = async () => {
  loading.value = true
  error.value = ''

  try {
    await authStore.login({
      email: email.value,
      password: password.value,
    })

    router.push('/dashboard')
  } catch (err) {
    console.error('Login error:', err)

    error.value =
        err.response?.data?.error ||
        err.response?.data?.message ||
        err.message ||
        'Login failed. Please check your credentials.'
  } finally {
    loading.value = false
  }
}
</script>