<template>
  <section class="space-y-4">
    <div>
      <Heading :level="2" size="xl">Statistics by category</Heading>
      <Text color="muted" class="mt-1">
        Overview of task progress across your categories.
      </Text>
    </div>

    <FeedbackMessage
        v-if="items.length === 0"
        type="info"
        message="No category statistics available yet."
    />

    <div v-else class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
      <article
          v-for="item in items"
          :key="item.categoryId ?? item.categoryName"
          class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm"
      >
        <div class="flex items-start justify-between gap-3">
          <div>
            <h3 class="text-lg font-semibold text-gray-900">
              {{ item.categoryName }}
            </h3>
            <p class="mt-1 text-sm text-gray-500">
              {{ item.total }} total task(s)
            </p>
          </div>

          <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800">
            {{ completionRate(item) }}%
          </span>
        </div>

        <div class="mt-4 space-y-2">
          <div class="flex items-center justify-between text-sm">
            <span class="text-gray-600">Done</span>
            <span class="font-medium text-gray-900">{{ item.done }}</span>
          </div>

          <div class="flex items-center justify-between text-sm">
            <span class="text-gray-600">Pending</span>
            <span class="font-medium text-gray-900">{{ item.pending }}</span>
          </div>
        </div>

        <div class="mt-4 h-2 overflow-hidden rounded-full bg-gray-100">
          <div
              class="h-full rounded-full bg-blue-600"
              :style="{ width: `${completionRate(item)}%` }"
          />
        </div>
      </article>
    </div>
  </section>
</template>

<script setup>
import Heading from '@/components/atoms/Heading/Heading.vue'
import Text from '@/components/atoms/Text/Text.vue'
import FeedbackMessage from '@/components/molecules/FeedbackMessage/FeedbackMessage.vue'

const props = defineProps({
  items: {
    type: Array,
    default: () => [],
  },
})

const completionRate = (item) => {
  const total = Number(item?.total ?? 0)
  const done = Number(item?.done ?? 0)

  if (total <= 0) return 0

  return Math.round((done / total) * 100)
}
</script>