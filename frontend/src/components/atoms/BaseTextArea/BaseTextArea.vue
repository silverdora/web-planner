<template>
  <textarea
      :id="id"
      :value="modelValue"
      :placeholder="placeholder"
      :rows="rows"
      :disabled="disabled"
      :class="classes"
      @input="$emit('update:modelValue', $event.target.value)"
  />
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  id: {
    type: String,
    default: '',
  },
  modelValue: {
    type: String,
    default: '',
  },
  placeholder: {
    type: String,
    default: '',
  },
  rows: {
    type: Number,
    default: 4,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  hasError: {
    type: Boolean,
    default: false,
  },
})

defineEmits(['update:modelValue'])

const classes = computed(() => {
  const base =
      'w-full rounded-xl border px-3 py-2.5 text-sm outline-none transition focus:ring-2 resize-none'
  const normal =
      'border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500'
  const error =
      'border-red-300 bg-white text-gray-900 focus:border-red-500 focus:ring-red-500'

  return `${base} ${props.hasError ? error : normal} ${props.disabled ? 'opacity-50 cursor-not-allowed' : ''}`
})
</script>