<template>
  <div class="flex flex-wrap gap-2">
    <template v-if="editing">
      <BaseButton
          variant="primary"
          size="sm"
          @click="$emit('save')"
      >
        Save
      </BaseButton>

      <BaseButton
          variant="ghost"
          size="sm"
          @click="$emit('cancel')"
      >
        Cancel
      </BaseButton>
    </template>

    <template v-else>
      <BaseButton
          variant="ghost"
          size="sm"
          @click="$emit('edit')"
      >
        Edit
      </BaseButton>

      <BaseButton
          variant="danger"
          size="sm"
          @click="$emit('delete')"
      >
        Delete
      </BaseButton>
    </template>
  </div>
</template>

<script setup>
import BaseButton from '@/components/atoms/BaseButton/BaseButton.vue'

defineProps({
  editing: {
    type: Boolean,
    default: false,
  },
})

defineEmits(['edit', 'delete', 'save', 'cancel'])
</script>