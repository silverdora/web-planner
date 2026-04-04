<template>
  <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md">
    <template v-if="!isEditing">
      <TaskCardHeader
          :title="task.title"
          :priority="task.priority || task.priority"
      />

      <p class="mt-3 text-sm text-gray-600">
        {{ task.description || 'No description' }}
      </p>

      <div class="mt-4">
        <TaskMeta
            :status="task.status || task.status"
            :due-date="task.dueDate || task.due_date"
        />
      </div>

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
import { ref, reactive, watch } from 'vue'

import TaskCardHeader from '@/components/molecules/TaskCardHeader/TaskCardHeader.vue'
import TaskMeta from '@/components/molecules/TaskMeta/TaskMeta.vue'
import TaskActions from '@/components/molecules/TaskActions/TaskActions.vue'
import FormField from '@/components/molecules/FormField/FormField.vue'
import SelectField from '@/components/molecules/SelectField/SelectField.vue'
import BaseLabel from '@/components/atoms/BaseLabel/BaseLabel.vue'
import BaseTextarea from '@/components/atoms/BaseTextArea/BaseTextArea.vue'

const props = defineProps({
  task: {
    type: Object,
    required: true,
  },
  saving: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['delete', 'save-edit'])

const isEditing = ref(false)
const localError = ref('')

const form = reactive({
  title: '',
  description: '',
  due_date: '',
  priority: '',
  status: '',
})

const priorityOptions = [
  { value: '1', label: 'Low' },
  { value: '2', label: 'Medium' },
  { value: '3', label: 'High' },
]

const statusOptions = [
  { value: '1', label: 'Created' },
  { value: '2', label: 'In Progress' },
  { value: '3', label: 'Completed' },
]

const normalizePriority = (value) => {
  const normalized = String(value ?? '').toLowerCase()

  const map = {
    low: '1',
    medium: '2',
    high: '3',
    '1': '1',
    '2': '2',
    '3': '3',
  }

  return map[normalized] || ''
}

const normalizeStatus = (value) => {
  const normalized = String(value ?? '').toLowerCase()

  const map = {
    created: '1',
    in_progress: '2',
    'in progress': '2',
    completed: '3',
    '1': '1',
    '2': '2',
    '3': '3',
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

const resetForm = () => {
  form.title = props.task.title || ''
  form.description = props.task.description || ''
  form.due_date = formatForDateTimeLocal(props.task.dueDate || props.task.due_date)
  form.priority = normalizePriority(props.task.priority || props.task.priority)
  form.status = normalizeStatus(props.task.status || props.task.status)
  localError.value = ''
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