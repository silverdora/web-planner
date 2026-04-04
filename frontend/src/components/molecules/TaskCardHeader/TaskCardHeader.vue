<template>
  <div class="flex items-start justify-between gap-3">
    <h3 class="text-lg font-semibold text-gray-900 break-words">
      {{ title }}
    </h3>

    <div class="flex flex-wrap items-center justify-end gap-2">
      <Badge v-if="categoryLabel" variant="secondary" size="sm">
        {{ categoryLabel }}
      </Badge>

      <Badge :variant="priorityVariant" size="sm">
        {{ priorityLabel }}
      </Badge>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import Badge from '@/components/atoms/Badge/Badge.vue'

const props = defineProps({
  title: {
    type: String,
    required: true,
  },
  priority: {
    type: [String, Number],
    default: 'medium',
  },
  category: {
    type: [String, Number],
    default: '',
  },
})

const normalizedPriority = computed(() => String(props.priority).toLowerCase())

const priorityVariant = computed(() => {
  const map = {
    low: 'success',
    medium: 'warning',
    high: 'danger',
    1: 'success',
    2: 'warning',
    3: 'danger',
  }

  return map[normalizedPriority.value] || 'primary'
})

const priorityLabel = computed(() => {
  const map = {
    1: 'Low',
    2: 'Medium',
    3: 'High',
    low: 'Low',
    medium: 'Medium',
    high: 'High',
  }

  return map[normalizedPriority.value] || props.priority
})

const categoryLabel = computed(() => {
  const value = String(props.category ?? '').trim()
  return value || ''
})
</script>