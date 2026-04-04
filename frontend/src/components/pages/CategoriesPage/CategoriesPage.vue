<template>
  <AppLayout>
	<section class="space-y-6">
	  <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
		<div>
		  <Heading :level="1">Categories</Heading>
		  <Text color="muted" class="mt-1">
			Manage the categories in your account.
		  </Text>
		</div>

		<BaseButton v-if="!showCreateForm" @click="openCreateForm">
		  Add new category
		</BaseButton>
	  </div>

	  <div
		  v-if="successMessage"
		  class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700"
	  >
		<div class="flex items-start justify-between gap-3">
		  <p class="pr-2">{{ successMessage }}</p>

		  <button
			  type="button"
			  class="shrink-0 text-green-700 transition-colors hover:text-green-900"
			  aria-label="Dismiss success message"
			  @click="successMessage = ''"
		  >
			×
		  </button>
		</div>
	  </div>

	  <FeedbackMessage v-if="loading" type="info" message="Loading categories..." />

	  <FeedbackMessage v-else-if="error" type="error" :message="error" />

	  <template v-else>
		<div v-if="showCreateForm" class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
		  <Heading :level="2" size="xl">Add new category</Heading>

		  <form class="mt-4 space-y-4" @submit.prevent="createCategory">
			<div>
			  <label :for="createFieldId" class="mb-2 block text-sm font-medium text-gray-700">
				Category name
			  </label>

			  <BaseInput
				  :id="createFieldId"
				  v-model="newCategoryName"
				  placeholder="Enter category name"
				  :has-error="!!createError"
				  :disabled="creatingCategory"
				  required
			  />
			</div>

			<p v-if="createError" class="text-sm text-red-600">{{ createError }}</p>

			<div class="flex flex-wrap gap-3">
			  <BaseButton type="submit" :disabled="creatingCategory">
				Save category
			  </BaseButton>

			  <BaseButton type="button" variant="ghost" :disabled="creatingCategory" @click="cancelCreate">
				Cancel
			  </BaseButton>
			</div>
		  </form>
		</div>

		<FeedbackMessage
			v-else-if="categories.length === 0"
			type="info"
			message="No categories found. Add your first category to get started."
		/>

		<div v-else class="space-y-3">
		  <article
			  v-for="category in categories"
			  :key="category.id"
			  class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md"
		  >
			<template v-if="editingCategoryId !== category.id">
			  <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
				<div>
				  <p class="text-lg font-semibold text-gray-900">
					{{ category.name }}
				  </p>
				</div>

				<div class="flex flex-wrap gap-2">
				  <BaseButton type="button" variant="ghost" size="sm" @click="startEdit(category)">
					Edit
				  </BaseButton>

				  <BaseButton
					  type="button"
					  variant="danger"
					  size="sm"
					  :disabled="savingCategoryId === category.id"
					  @click="deleteCategory(category)"
				  >
					Delete
				  </BaseButton>
				</div>
			  </div>
			</template>

			<template v-else>
			  <form class="space-y-4" @submit.prevent="saveCategory(category)">
				<div>
				  <label :for="editFieldId(category.id)" class="mb-2 block text-sm font-medium text-gray-700">
					Category name
				  </label>

				  <BaseInput
					  :id="editFieldId(category.id)"
					  v-model="editCategoryName"
					  placeholder="Enter category name"
					  :has-error="!!editError"
					  :disabled="savingCategoryId === category.id"
					  required
				  />
				</div>

				<p v-if="editError" class="text-sm text-red-600">{{ editError }}</p>

				<div class="flex flex-wrap gap-3">
				  <BaseButton type="submit" :disabled="savingCategoryId === category.id">
					Save changes
				  </BaseButton>

				  <BaseButton
					  type="button"
					  variant="ghost"
					  :disabled="savingCategoryId === category.id"
					  @click="cancelEdit"
				  >
					Cancel
				  </BaseButton>
				</div>
			  </form>
			</template>
		  </article>
		</div>
	  </template>
	</section>
  </AppLayout>
</template>

<script setup>
import { onMounted, ref } from 'vue'

import axios from '@/utils/axios.js'
import AppLayout from '@/components/templates/AppLayout/AppLayout.vue'
import Heading from '@/components/atoms/Heading/Heading.vue'
import Text from '@/components/atoms/Text/Text.vue'
import BaseButton from '@/components/atoms/BaseButton/BaseButton.vue'
import BaseInput from '@/components/atoms/BaseInput/BaseInput.vue'
import FeedbackMessage from '@/components/molecules/FeedbackMessage/FeedbackMessage.vue'

const categories = ref([])
const loading = ref(true)
const error = ref('')
const successMessage = ref('')
const savingCategoryId = ref(null)

const showCreateForm = ref(false)
const creatingCategory = ref(false)
const newCategoryName = ref('')
const createError = ref('')
const createFieldId = 'new-category-name'

const editingCategoryId = ref(null)
const editCategoryName = ref('')
const editError = ref('')

const unwrapResponseData = (response) => response?.data?.data ?? response?.data ?? response

const normalizeCategory = (category) => ({
  id: category?.id ?? null,
  userId: category?.userId ?? category?.user_id ?? null,
  name: String(category?.name ?? '').trim(),
})

const sortCategories = (items) =>
	[...items].sort((a, b) => String(a.name ?? '').localeCompare(String(b.name ?? '')))

const setCategories = (items) => {
  categories.value = sortCategories(items.map(normalizeCategory))
}

const loadCategories = async () => {
  loading.value = true
  error.value = ''

  try {
	const response = await axios.get('/categories')
	const payload = unwrapResponseData(response)

	if (!Array.isArray(payload)) {
	  error.value = 'Unexpected categories response'
	  categories.value = []
	  return
	}

	setCategories(payload)
  } catch (err) {
	console.error('Categories load error:', err)
	error.value =
		err.response?.data?.error ||
		err.response?.data?.message ||
		err.message ||
		'Failed to load categories'
	categories.value = []
  } finally {
	loading.value = false
  }
}

const openCreateForm = () => {
  showCreateForm.value = true
  creatingCategory.value = false
  newCategoryName.value = ''
  createError.value = ''
  editError.value = ''
  editingCategoryId.value = null
}

const cancelCreate = () => {
  showCreateForm.value = false
  creatingCategory.value = false
  newCategoryName.value = ''
  createError.value = ''
}

const startEdit = (category) => {
  showCreateForm.value = false
  createError.value = ''
  editingCategoryId.value = category.id
  editCategoryName.value = String(category.name ?? '')
  editError.value = ''
}

const cancelEdit = () => {
  editingCategoryId.value = null
  editCategoryName.value = ''
  editError.value = ''
}

const createCategory = async () => {
  createError.value = ''

  const name = newCategoryName.value.trim()

  if (!name) {
	createError.value = 'Category name is required.'
	return
  }

  creatingCategory.value = true

  try {
	await axios.post('/categories', { name })
	await loadCategories()
	successMessage.value = 'Category created successfully.'
	cancelCreate()
  } catch (err) {
	console.error('Create category error:', err)
	createError.value =
		err.response?.data?.error ||
		err.response?.data?.message ||
		err.message ||
		'Failed to create category'
  } finally {
	creatingCategory.value = false
  }
}

const saveCategory = async (category) => {
  if (editingCategoryId.value !== category.id) return

  editError.value = ''

  const name = editCategoryName.value.trim()

  if (!name) {
	editError.value = 'Category name is required.'
	return
  }

  savingCategoryId.value = category.id

  try {
	await axios.put(`/categories/${category.id}`, { name })
	await loadCategories()
	successMessage.value = 'Category updated successfully.'
	cancelEdit()
  } catch (err) {
	console.error('Update category error:', err)
	editError.value =
		err.response?.data?.error ||
		err.response?.data?.message ||
		err.message ||
		'Failed to update category'
  } finally {
	savingCategoryId.value = null
  }
}

const deleteCategory = async (category) => {
  const confirmed = window.confirm(`Delete category "${category.name}"?`)
  if (!confirmed) return

  savingCategoryId.value = category.id
  error.value = ''

  try {
	await axios.delete(`/categories/${category.id}`)
	await loadCategories()

	if (editingCategoryId.value === category.id) {
	  cancelEdit()
	}

	successMessage.value = 'Category deleted successfully.'
  } catch (err) {
	console.error('Delete category error:', err)
	error.value =
		err.response?.data?.error ||
		err.response?.data?.message ||
		err.message ||
		'Failed to delete category'
  } finally {
	savingCategoryId.value = null
  }
}

const editFieldId = (id) => `category-name-${id}`

onMounted(loadCategories)
</script>

<style scoped>

</style>