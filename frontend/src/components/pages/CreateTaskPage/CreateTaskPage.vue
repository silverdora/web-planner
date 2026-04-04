<template>
  <AppLayout>
    <section class="mx-auto max-w-3xl">
      <div class="mb-6">
        <Heading :level="1">Create Task</Heading>
        <Text color="muted" class="mt-1">
          Add a new task to your planner
        </Text>
      </div>

      <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
        <form @submit.prevent="handleCreateTask" class="space-y-5">
          <FormField
              id="title"
              v-model="title"
              label="Title"
              type="text"
              placeholder="Enter task title"
              :disabled="loading"
          />

          <div>
            <BaseLabel for-id="description">Description</BaseLabel>
            <BaseTextarea
                id="description"
                v-model="description"
                placeholder="Enter task description"
                :disabled="loading"
                :rows="5"
            />
          </div>

          <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <FormField
                id="due_date"
                v-model="dueDate"
                label="Due date"
                type="datetime-local"
                :disabled="loading"
            />

            <SelectField
                id="priority"
                v-model="priorityId"
                label="Priority"
                :options="priorityOptions"
                placeholder="Select priority"
                :disabled="loading"
            />
          </div>

          <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <SelectField
                id="status"
                v-model="statusId"
                label="Status"
                :options="statusOptions"
                placeholder="Select status"
                :disabled="loading"
            />

            <SelectField
                id="category"
                v-model="categoryId"
                label="Category"
                :options="categoryOptions"
                placeholder="Select category"
                :disabled="loading || categoriesLoading"
            />
          </div>

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

          <div class="flex flex-wrap gap-3">
            <BaseButton
                type="submit"
                variant="primary"
                size="lg"
                :disabled="loading || categoriesLoading"
            >
              {{ loading ? 'Creating...' : 'Create Task' }}
            </BaseButton>

            <BaseButton
                type="button"
                variant="ghost"
                size="lg"
                :disabled="loading"
                @click="goBack"
            >
              Cancel
            </BaseButton>
          </div>
        </form>
      </div>
    </section>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from '@/utils/axios.js'

import AppLayout from '@/components/templates/AppLayout/AppLayout.vue'
import Heading from '@/components/atoms/Heading/Heading.vue'
import Text from '@/components/atoms/Text/Text.vue'
import BaseButton from '@/components/atoms/BaseButton/BaseButton.vue'
import BaseLabel from '@/components/atoms/BaseLabel/BaseLabel.vue'
import BaseTextarea from '@/components/atoms/BaseTextarea/BaseTextarea.vue'
import FormField from '@/components/molecules/FormField/FormField.vue'
import SelectField from '@/components/molecules/SelectField/SelectField.vue'
import FeedbackMessage from '@/components/molecules/FeedbackMessage/FeedbackMessage.vue'

const router = useRouter()

const title = ref('')
const description = ref('')
const dueDate = ref('')
const priorityId = ref('')
const statusId = ref('')
const categoryId = ref('')

const loading = ref(false)
const categoriesLoading = ref(false)
const error = ref('')
const successMessage = ref('')
const categories = ref([])

const priorityOptions = [
  { value: '1', label: 'Low' },
  { value: '2', label: 'Medium' },
  { value: '3', label: 'High' },
]

const statusOptions = [
  { value: '1', label: 'Pending' },
  { value: '2', label: 'In Progress' },
  { value: '3', label: 'Completed' },
]

const categoryOptions = computed(() =>
    categories.value.map((category) => ({
      value: String(category.id),
      label: category.name,
    }))
)

const loadCategories = async () => {
  categoriesLoading.value = true

  try {
    const response = await axios.get('/categories')
    const payload = response.data.data ?? response.data

    if (!Array.isArray(payload)) {
      throw new Error('Unexpected categories response')
    }

    categories.value = payload
  } catch (err) {
    console.error('Load categories error:', err)
    error.value = err.response?.data?.message || err.message || 'Failed to load categories.'
  } finally {
    categoriesLoading.value = false
  }
}

const handleCreateTask = async () => {
  error.value = ''
  successMessage.value = ''

  if (!title.value.trim()) {
    error.value = 'Title is required.'
    return
  }

  loading.value = true

  try {
    await axios.post('/tasks', {
      title: title.value.trim(),
      description: description.value.trim(),
      due_date: dueDate.value || null,
      priority_id: priorityId.value || null,
      status_id: statusId.value || null,
      category_id: categoryId.value || null,
    })

    successMessage.value = 'Task created successfully.'

    setTimeout(() => {
      router.push('/dashboard')
    }, 700)
  } catch (err) {
    console.error('Create task error:', err)
    error.value = err.response?.data?.message || 'Failed to create task.'
  } finally {
    loading.value = false
  }
}

const goBack = () => {
  router.push('/dashboard')
}

onMounted(loadCategories)
</script>