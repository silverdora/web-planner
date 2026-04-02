<template>
  <label :for="forId" class="mb-1 block text-sm font-medium text-gray-700">
    <slot>{{ text }}</slot>
  </label>
</template>

<script setup>
defineProps({
  forId: {
    type: String,
    default: '',
  },
  text: {
    type: String,
    default: '',
  },
})
</script>