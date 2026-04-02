<template>
  <div :class="classes" class="rounded-2xl border px-4 py-3 text-sm font-medium">
    {{ message }}
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  type: {
    type: String,
    default: 'info',
    validator: (value) => ['info', 'error', 'success'].includes(value),
  },
  message: {
    type: String,
    required: true,
  },
})

const classes = computed(() => {
  const variants = {
    info: 'bg-blue-50 text-blue-700 border-blue-200',
    error: 'bg-red-50 text-red-700 border-red-200',
    success: 'bg-green-50 text-green-700 border-green-200',
  }

  return variants[props.type]
})
</script>