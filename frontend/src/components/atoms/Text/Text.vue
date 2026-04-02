<template>
  <component :is="as" :class="classes">
    <slot />
  </component>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  as: {
    type: String,
    default: 'p',
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value),
  },
  color: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'muted', 'danger', 'success'].includes(value),
  },
})

const classes = computed(() => {
  const sizes = {
    sm: 'text-sm',
    md: 'text-base',
    lg: 'text-lg',
  }

  const colors = {
    default: 'text-gray-700',
    muted: 'text-gray-500',
    danger: 'text-red-600',
    success: 'text-green-600',
  }

  return `${sizes[props.size]} ${colors[props.color]}`
})
</script>