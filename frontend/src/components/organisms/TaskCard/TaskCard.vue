<template>
  <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md">
    <template v-if="!isEditing">
      <TaskCardHeader
          :title="taskTitle"
          :priority="taskPriority"
          :category="taskCategory"
      />

      <p class="mt-3 text-sm text-gray-600">
        {{ taskDescription || 'No description' }}
      </p>

      <div class="mt-4">
        <TaskMeta
            :status="taskStatus"
            :due-date="taskDueDate"
        />
      </div>

      <div class="mt-4 grid gap-3 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-end">
        <div>
          <BaseLabel :for-id="`quick-status-${task.id}`">
            Status
          </BaseLabel>

          <BaseSelect
              :id="`quick-status-${task.id}`"
              v-model="statusDraft"
              :options="statusOptions"
              placeholder="Select status"
              :disabled="saving"
          />
        </div>

        <BaseButton
            variant="secondary"
            size="sm"
            :disabled="saving || !statusDraft"
            @click="changeStatus"
        >
          Change status
        </BaseButton>
      </div>

      <p v-if="statusError" class="mt-2 text-sm text-red-600">
        {{ statusError }}
      </p>

      <div class="mt-5 border-t border-gray-100 pt-4">
        <TaskActions
            :editing="false"
            @edit="startEditing"
            @delete="$emit('delete', task)"
        />
      </div>
    </template>

    <template v-else>
      <div class="space-y-4">
        <FormField
            :id="`title-${task.id}`"
            v-model="form.title"
            label="Title"
            type="text"
            placeholder="Enter task title"
            :disabled="saving"
        />

        <div>
          <BaseLabel :for-id="`description-${task.id}`">
            Description
          </BaseLabel>

          <BaseTextarea
              :id="`description-${task.id}`"
              v-model="form.description"
              placeholder="Enter task description"
              :rows="4"
              :disabled="saving"
          />
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <FormField
              :id="`due-date-${task.id}`"
              v-model="form.due_date"
              label="Due date"
              type="datetime-local"
              :disabled="saving"
          />

          <SelectField
              :id="`priority-${task.id}`"
              v-model="form.priority"
              label="Priority"
              :options="priorityOptions"
              placeholder="Select priority"
              :disabled="saving"
          />
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <SelectField
              :id="`status-${task.id}`"
              v-model="form.status"
              label="Status"
              :options="statusOptions"
              placeholder="Select status"
              :disabled="saving"
          />

          <div />
        </div>

        <p v-if="localError" class="text-sm text-red-600">
          {{ localError }}
        </p>

        <div class="border-t border-gray-100 pt-4">
          <TaskActions
              :editing="true"
              @save="saveEdit"
              @cancel="cancelEditing"
          />
        </div>
      </div>
    </template>
  </article>
</template>

<script setup>
import { ref, reactive, watch, computed } from 'vue'

import TaskCardHeader from '@/components/molecules/TaskCardHeader/TaskCardHeader.vue'
import TaskMeta from '@/components/molecules/TaskMeta/TaskMeta.vue'
import TaskActions from '@/components/molecules/TaskActions/TaskActions.vue'
import FormField from '@/components/molecules/FormField/FormField.vue'
import SelectField from '@/components/molecules/SelectField/SelectField.vue'
import BaseLabel from '@/components/atoms/BaseLabel/BaseLabel.vue'
import BaseTextarea from '@/components/atoms/BaseTextArea/BaseTextArea.vue'
import BaseSelect from '@/components/atoms/BaseSelect/BaseSelect.vue'
import BaseButton from '@/components/atoms/BaseButton/BaseButton.vue'

const props = defineProps({
  task: {
    type: Object,
    required: true,
  },
  categories: {
    type: Array,
    default: () => [],
  },
  saving: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['delete', 'save-edit', 'change-status'])

const isEditing = ref(false)
const localError = ref('')
const statusError = ref('')
const statusDraft = ref('')

const form = reactive({
  title: '',
  description: '',
  due_date: '',
  priority: '',
  status: '',
})

const priorityOptions = [
  { value: 'low', label: 'Low' },
  { value: 'medium', label: 'Medium' },
  { value: 'high', label: 'High' },
]

const statusOptions = [
  { value: 'created', label: 'Created' },
  { value: 'in progress', label: 'In Progress' },
  { value: 'done', label: 'Done' },
]

const normalizePriority = (value) => {
  const normalized = String(value ?? '').toLowerCase()

  const map = {
    low: 'low',
    medium: 'medium',
    high: 'high',
    '1': 'low',
    '2': 'medium',
    '3': 'high',
  }

  return map[normalized] || ''
}

const normalizeStatus = (value) => {
  const normalized = String(value ?? '').toLowerCase()

  const map = {
    created: 'created',
    in_progress: 'in progress',
    'in progress': 'in progress',
    completed: 'done',
    done: 'done',
    '1': 'created',
    '2': 'in progress',
    '3': 'done',
  }

  return map[normalized] || ''
}

const formatForDateTimeLocal = (value) => {
  if (!value) return ''

  const date = new Date(value)

  if (Number.isNaN(date.getTime())) return ''

  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  const hours = String(date.getHours()).padStart(2, '0')
  const minutes = String(date.getMinutes()).padStart(2, '0')

  return `${year}-${month}-${day}T${hours}:${minutes}`
}

const getTaskValue = (keys, fallback = '') => {
  for (const key of keys) {
    const value = props.task?.[key]

    if (value !== undefined && value !== null) {
      return value
    }
  }

  return fallback
}

const resolveCategoryNameById = (categoryId) => {
  if (categoryId === undefined || categoryId === null || categoryId === '') {
    return ''
  }

  const matched = props.categories.find(
      (category) => String(category?.id ?? '') === String(categoryId)
  )

  return String(matched?.name ?? '').trim()
}

const taskTitle = computed(() => String(getTaskValue(['title', 'task_title'], '')))
const taskDescription = computed(() => String(getTaskValue(['description', 'task_description'], '')))
const taskDueDate = computed(() => getTaskValue(['dueDate', 'due_date'], ''))
const taskPriority = computed(() => getTaskValue(['priority'], ''))
const taskStatus = computed(() => getTaskValue(['status'], ''))
const taskCategory = computed(() => {
  const categoryName = getTaskValue(['category_name', 'categoryName'], '')
  const directLabel = String(categoryName ?? '').trim()
  if (directLabel) return directLabel

  const nestedLabel = String(props.task?.category?.name ?? '').trim()
  if (nestedLabel) return nestedLabel

  return resolveCategoryNameById(getTaskValue(['category_id', 'categoryId'], ''))
})

const resetForm = () => {
  form.title = taskTitle.value
  form.description = taskDescription.value
  form.due_date = formatForDateTimeLocal(taskDueDate.value)
  form.priority = normalizePriority(taskPriority.value)
  form.status = normalizeStatus(taskStatus.value)
  statusDraft.value = normalizeStatus(taskStatus.value)
  localError.value = ''
  statusError.value = ''
}

const startEditing = () => {
  resetForm()
  isEditing.value = true
}

const cancelEditing = () => {
  resetForm()
  isEditing.value = false
}

const saveEdit = () => {
  localError.value = ''

  if (!form.title.trim()) {
    localError.value = 'Title is required.'
    return
  }

  emit('save-edit', {
    id: props.task.id,
    payload: {
      title: form.title.trim(),
      description: form.description.trim(),
      due_date: form.due_date || null,
      priority: form.priority || null,
      status: form.status || null,
    },
    onSuccess: () => {
      isEditing.value = false
    },
    onError: (message) => {
      localError.value = message || 'Failed to save task.'
    },
  })
}

const changeStatus = () => {
  statusError.value = ''

  if (!statusDraft.value) {
    statusError.value = 'Please choose a status.'
    return
  }

  emit('change-status', {
    id: props.task.id,
    status: statusDraft.value,
    onSuccess: () => {
      statusError.value = ''
    },
    onError: (message) => {
      statusError.value = message || 'Failed to change status.'
    },
  })
}

watch(
    () => props.task,
    () => {
      if (!isEditing.value) {
        resetForm()
      }
    },
    { deep: true, immediate: true }
)
</script>