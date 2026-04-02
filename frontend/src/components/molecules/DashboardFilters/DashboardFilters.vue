<template>
  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
      <FormField
          id="search"
          :model-value="search"
          label="Search"
          type="text"
          placeholder="Search by title..."
          @update:modelValue="$emit('update:search', $event)"
      />

      <SelectField
          id="statusFilter"
          :model-value="status"
          label="Status"
          :options="statusOptions"
          placeholder="All statuses"
          @update:modelValue="$emit('update:status', $event)"
      />

      <SelectField
          id="priorityFilter"
          :model-value="priority"
          label="Priority"
          :options="priorityOptions"
          placeholder="All priorities"
          @update:modelValue="$emit('update:priority', $event)"
      />

      <SelectField
          id="sortFilter"
          :model-value="sort"
          label="Sort by"
          :options="sortOptions"
          placeholder="Choose sorting"
          @update:modelValue="$emit('update:sort', $event)"
      />
    </div>
  </div>
</template>

<script setup>
import FormField from '@/components/molecules/FormField/FormField.vue'
import SelectField from '@/components/molecules/SelectField/SelectField.vue'

defineProps({
  search: {
    type: String,
    default: '',
  },
  status: {
    type: String,
    default: '',
  },
  priority: {
    type: String,
    default: '',
  },
  sort: {
    type: String,
    default: '',
  },
})

defineEmits([
  'update:search',
  'update:status',
  'update:priority',
  'update:sort',
])

const statusOptions = [
  { value: '', label: 'All statuses' },
  { value: 'created', label: 'Created' },
  { value: 'in progress', label: 'In Progress' },
  { value: 'done', label: 'Done' },
]

const priorityOptions = [
  { value: '', label: 'All priorities' },
  { value: 'low', label: 'Low' },
  { value: 'medium', label: 'Medium' },
  { value: 'high', label: 'High' },
]

const sortOptions = [
  { value: '', label: 'Default' },
  { value: 'due_asc', label: 'Due date ↑' },
  { value: 'due_desc', label: 'Due date ↓' },
  { value: 'title_asc', label: 'Title A-Z' },
  { value: 'title_desc', label: 'Title Z-A' },
]
</script>