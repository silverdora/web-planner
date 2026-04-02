<template>
  <AuthLayout>
    <section class="rounded-3xl border border-gray-200 bg-white p-8 shadow-sm">
      <Heading :level="1" size="xl" class="text-center">
        Register
      </Heading>

      <Text color="muted" class="mt-2 text-center">
        Create an account to start managing your planner
      </Text>

      <form @submit.prevent="handleRegister" class="mt-6 space-y-4">
        <FormField
            id="name"
            v-model="name"
            label="Name"
            type="text"
            placeholder="Enter your name"
            :disabled="loading"
        />

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
            placeholder="Create a password"
            :disabled="loading"
        />

        <FormField
            id="confirmPassword"
            v-model="confirmPassword"
            label="Confirm password"
            type="password"
            placeholder="Repeat your password"
            :disabled="loading"
        />

        <FeedbackMessage
            v-if="error"
            type="error"
            :message="error"
        />

        <FeedbackMessage
            v-if="successMessage"
            type="success"
            :message="successMessage"
        />

        <BaseButton
            type="submit"
            variant="secondary"
            size="lg"
            :disabled="loading"
            :full-width="true"
        >
          {{ loading ? 'Registering...' : 'Register' }}
        </BaseButton>
      </form>

      <div class="mt-6 flex flex-col items-center gap-2">
        <router-link
            to="/login"
            class="text-sm font-medium text-blue-600 underline hover:text-blue-800"
        >
          Already have an account? Log in
        </router-link>

        <router-link
            to="/"
            class="text-sm font-medium text-gray-600 underline hover:text-gray-800"
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
import axios from '@/utils/axios.js'

import AuthLayout from '@/components/templates/AuthLayout/AuthLayout.vue'
import Heading from '@/components/atoms/Heading/Heading.vue'
import Text from '@/components/atoms/Text/Text.vue'
import BaseButton from '@/components/atoms/BaseButton/BaseButton.vue'
import FormField from '@/components/molecules/FormField/FormField.vue'
import FeedbackMessage from '@/components/molecules/FeedbackMessage/FeedbackMessage.vue'

const router = useRouter()

const name = ref('')
const email = ref('')
const password = ref('')
const confirmPassword = ref('')
const loading = ref(false)
const error = ref('')
const successMessage = ref('')

const handleRegister = async () => {
  error.value = ''
  successMessage.value = ''

  if (!name.value.trim()) {
    error.value = 'Name is required.'
    return
  }

  if (!email.value.trim()) {
    error.value = 'Email is required.'
    return
  }

  if (!password.value.trim()) {
    error.value = 'Password is required.'
    return
  }

  if (password.value !== confirmPassword.value) {
    error.value = 'Passwords do not match.'
    return
  }

  loading.value = true

  try {
    await axios.post('/auth/register', {
      name: name.value,
      email: email.value,
      password: password.value,
    })

    successMessage.value = 'Registration successful. Redirecting to login...'

    setTimeout(() => {
      router.push('/login')
    }, 900)
  } catch (err) {
    console.error('Registration error:', err)
    error.value = err.response?.data?.message || 'Registration failed. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>