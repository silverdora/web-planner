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
              :error="titleError"
              required
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
                :error="dueDateError"
                required
                :disabled="loading"
            />

            <SelectField
                id="priority"
                v-model="priority"
                label="Priority"
                :options="priorityOptions"
                placeholder="Select priority"
                :error="priorityError"
                required
                :disabled="loading"
            />
          </div>

          <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
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
import BaseTextarea from '@/components/atoms/BaseTextArea/BaseTextArea.vue'
import FormField from '@/components/molecules/FormField/FormField.vue'
import SelectField from '@/components/molecules/SelectField/SelectField.vue'
import FeedbackMessage from '@/components/molecules/FeedbackMessage/FeedbackMessage.vue'

const router = useRouter()

const title = ref('')
const description = ref('')
const dueDate = ref('')
const priority = ref('')
const categoryId = ref('')

const loading = ref(false)
const categoriesLoading = ref(false)
const error = ref('')
const titleError = ref('')
const dueDateError = ref('')
const priorityError = ref('')
const successMessage = ref('')
const categories = ref([])

const priorityOptions = [
  { value: 'low', label: 'Low' },
  { value: 'medium', label: 'Medium' },
  { value: 'high', label: 'High' },
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
      error.value = 'Unexpected categories response'
      categories.value = []
      return
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
  titleError.value = ''
  dueDateError.value = ''
  priorityError.value = ''

  if (!title.value.trim()) {
    titleError.value = 'Title is required.'
  }

  if (!dueDate.value) {
    dueDateError.value = 'Due date is required.'
  }

  if (!priority.value) {
    priorityError.value = 'Priority is required.'
  }

  if (titleError.value || dueDateError.value || priorityError.value) {
    error.value = 'Please fill in the required fields.'
    return
  }

  loading.value = true

  try {
    const response = await axios.post('/tasks', {
      title: title.value.trim(),
      description: description.value.trim(),
      due_date: dueDate.value || null,
      priority: priority.value || null,
      category_id: categoryId.value || null,
    })

    const createdTask = response.data.data ?? response.data
    console.log('Task created successfully:', createdTask)

    successMessage.value = `Task "${title.value.trim()}" was created successfully.`

    setTimeout(() => {
      router.push({
        name: 'dashboard',
        query: { created: '1' },
      })
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