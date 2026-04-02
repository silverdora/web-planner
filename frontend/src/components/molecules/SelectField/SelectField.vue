<template>
  <div>
    <BaseLabel :for-id="id">
      {{ label }}
    </BaseLabel>

    <BaseSelect
        :id="id"
        :model-value="modelValue"
        :options="options"
        :placeholder="placeholder"
        :disabled="disabled"
        :has-error="!!error"
        @update:modelValue="$emit('update:modelValue', $event)"
    />

    <p v-if="error" class="mt-1 text-sm text-red-600">
      {{ error }}
    </p>
  </div>
</template>

<script setup>
import BaseLabel from '@/components/atoms/BaseLabel/BaseLabel.vue'
import BaseSelect from '@/components/atoms/BaseSelect/BaseSelect.vue'

defineProps({
  id: {
    type: String,
    required: true,
  },
  label: {
    type: String,
    required: true,
  },
  modelValue: {
    type: [String, Number],
    default: '',
  },
  options: {
    type: Array,
    default: () => [],
  },
  placeholder: {
    type: String,
    default: 'Select an option',
  },
  error: {
    type: String,
    default: '',
  },
  disabled: {
    type: Boolean,
    default: false,
  },
})

defineEmits(['update:modelValue'])
</script>