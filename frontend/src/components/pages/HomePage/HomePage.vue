<template>
  <AppLayout>
    <section class="flex min-h-[70vh] items-center justify-center">
      <div class="w-full max-w-3xl rounded-3xl border border-gray-200 bg-white p-8 text-center shadow-sm sm:p-12">
        <Heading :level="1" class="text-gray-900">
          Web Planner
        </Heading>

        <Text size="lg" color="muted" class="mt-3">
          Organize your tasks, projects, and deadlines in one place.
        </Text>

        <div class="mt-8 flex flex-wrap justify-center gap-3">
          <template v-if="!isLoggedIn">
            <BaseButton variant="primary" size="lg" @click="goToLogin">
              Log in
            </BaseButton>

            <BaseButton variant="secondary" size="lg" @click="goToRegister">
              Register
            </BaseButton>
          </template>

          <template v-else>
            <BaseButton variant="primary" size="lg" @click="goToDashboard">
              Go to Dashboard
            </BaseButton>

            <BaseButton variant="secondary" size="lg" @click="goToCreateTask">
              Create Task
            </BaseButton>
          </template>
        </div>
      </div>
    </section>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { getAuthToken } from '@/utils/axios.js'

import AppLayout from '@/components/templates/AppLayout/AppLayout.vue'
import Heading from '@/components/atoms/Heading/Heading.vue'
import Text from '@/components/atoms/Text/Text.vue'
import BaseButton from '@/components/atoms/BaseButton/BaseButton.vue'

const router = useRouter()

const isLoggedIn = computed(() => !!getAuthToken())

const goToLogin = () => {
  router.push('/login')
}

const goToRegister = () => {
  router.push('/register')
}

const goToDashboard = () => {
  router.push('/dashboard')
}

const goToCreateTask = () => {
  router.push('/tasks/create')
}
</script>