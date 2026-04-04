<template>
  <div>
    <BaseLabel :for-id="id">
      {{ label }}
    </BaseLabel>

    <BaseInput
        :id="id"
        :model-value="modelValue"
        :type="type"
        :placeholder="placeholder"
        :disabled="disabled"
        :required="required"
        :has-error="!!error"
        @update:modelValue="$emit('update:modelValue', $event)"
    />

    <p v-if="error" class="mt-1 text-sm text-red-600">
      {{ error }}
    </p>
  </div>
</template>

<script setup>
import BaseInput from '@/components/atoms/BaseInput/BaseInput.vue'
import BaseLabel from '@/components/atoms/BaseLabel/BaseLabel.vue'

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
  type: {
    type: String,
    default: 'text',
  },
  placeholder: {
    type: String,
    default: '',
  },
  error: {
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
})

defineEmits(['update:modelValue'])
</script>