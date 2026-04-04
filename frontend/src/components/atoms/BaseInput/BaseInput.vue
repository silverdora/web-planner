<template>
  <input
      :id="id"
      :value="modelValue"
      :type="type"
      :placeholder="placeholder"
      :disabled="disabled"
      :required="required"
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
    type: [String, Number],
    default: '',
  },
  type: {
    type: String,
    default: 'text',
  },
  placeholder: {
    type: String,
    default: '',
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  required: {
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
      'w-full rounded-xl border px-3 py-2.5 text-sm outline-none transition focus:ring-2'
  const normal =
      'border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500'
  const error =
      'border-red-300 bg-white text-gray-900 focus:border-red-500 focus:ring-red-500'

  return `${base} ${props.hasError ? error : normal} ${props.disabled ? 'opacity-50 cursor-not-allowed' : ''}`
})
</script>