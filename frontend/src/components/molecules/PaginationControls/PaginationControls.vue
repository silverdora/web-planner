<template>
  <div
      v-if="totalPages > 1"
      class="flex flex-col gap-3 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between"
  >
    <p class="text-sm text-gray-600">
      Page <span class="font-semibold text-gray-900">{{ currentPage }}</span>
      of <span class="font-semibold text-gray-900">{{ totalPages }}</span>
      <span v-if="total !== null" class="ml-2 text-gray-500">
        ({{ total }} tasks)
      </span>
    </p>

    <div class="flex flex-wrap gap-2">
      <BaseButton
          type="button"
          variant="ghost"
          size="sm"
          :disabled="currentPage <= 1 || disabled"
          @click="$emit('update:page', currentPage - 1)"
      >
        Previous
      </BaseButton>

      <BaseButton
          v-for="page in visiblePages"
          :key="page"
          type="button"
          :variant="page === currentPage ? 'primary' : 'ghost'"
          size="sm"
          :disabled="disabled"
          @click="$emit('update:page', page)"
      >
        {{ page }}
      </BaseButton>

      <BaseButton
          type="button"
          variant="ghost"
          size="sm"
          :disabled="currentPage >= totalPages || disabled"
          @click="$emit('update:page', currentPage + 1)"
      >
        Next
      </BaseButton>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import BaseButton from '@/components/atoms/BaseButton/BaseButton.vue'

const props = defineProps({
  currentPage: {
    type: Number,
    default: 1,
  },
  totalPages: {
    type: Number,
    default: 1,
  },
  total: {
    type: Number,
    default: null,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
})

defineEmits(['update:page'])

const visiblePages = computed(() => {
  const totalPages = props.totalPages
  const currentPage = props.currentPage

  if (totalPages <= 5) {
    return Array.from({ length: totalPages }, (_, index) => index + 1)
  }

  let start = Math.max(1, currentPage - 2)
  let end = Math.min(totalPages, currentPage + 2)

  if (start === 1) {
    end = 5
  }

  if (end === totalPages) {
    start = totalPages - 4
  }

  return Array.from({ length: end - start + 1 }, (_, index) => start + index)
})
</script>