<template>
  <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
    <p class="text-sm font-medium text-gray-500">{{ title }}</p>
    <p class="mt-2 text-3xl font-bold text-gray-900">{{ value }}</p>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true,
  },
  value: {
    type: [String, Number],
    required: true,
  },
})
</script>