<template>
  <div class="space-y-4 md:px-3 md:py-4 sm:space-y-6 sm:px-6 sm:py-8">
    <!-- Header -->
    <header
      class="flex flex-col gap-3 rounded-lg border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-950 sm:flex-row sm:items-center sm:justify-between sm:rounded-xl sm:p-5">
      <div class="flex items-center gap-3">
        <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100 sm:text-2xl">Categories</h1>
        <div v-if="isLoading"
          class="h-4 w-4 animate-spin rounded-full border-2 border-blue-300 border-t-blue-600 sm:h-5 sm:w-5">
        </div>
      </div>
      <button @click="openAddModal"
        class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-700 sm:w-auto">
        <i class="fa-solid fa-plus hover:scale-105"></i>
        Add Category
      </button>
    </header>

    <!-- Error Message -->
    <section v-if="errorMessage"
      class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-950 dark:text-red-200 sm:rounded-xl sm:p-4">
      {{ errorMessage }}
    </section>

    <!-- Table Card -->
    <div
      class="rounded-lg border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-950 sm:rounded-xl">
      <!-- Search -->
      <div class="relative w-full sm:w-64 m-2">
        <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500"></i>

        <input v-model="search" type="text" placeholder="Search categories"
          class="w-full rounded-lg border border-slate-300 pl-9 pr-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100" />
      </div>

      <!-- Desktop Table -->
      <div class="hidden overflow-x-auto sm:block">
        <table class="w-full text-left text-sm">
          <thead class="bg-violet-400 text-slate-600 dark:bg-slate-800 dark:text-slate-300">
            <tr>
              <th class="px-3 py-3">ID</th>
              <th class="md:px-4 px-1 py-3">Name</th>
              <th class="md:px-4 px-1 py-3">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="category in filteredCategories" :key="category.id"
              class="border-t border-slate-100 hover:bg-violet-100 dark:border-slate-700 dark:hover:bg-slate-800">
              <td class="px-4 py-3 font-semibold">{{ category.id }}</td>
              <td class="px-4 py-3 text-violet-400 font-semibold">{{ category.name }}</td>
              <td class="px-4 py-3 flex gap-2">
                <button @click="editCategory(category)"
                  class="inline-flex items-center gap-1 rounded-md bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700">
                  <i class="fa-solid fa-pen-to-square"></i>
                  Edit
                </button>
                <button @click="openDeleteModal(category)"
                  class="inline-flex items-center gap-1 rounded-md bg-rose-500 px-3 py-2 text-xs font-semibold text-white hover:bg-rose-600">
                  <i class="fa-solid fa-trash"></i>
                  Delete
                </button>
              </td>
            </tr>
            <tr v-if="filteredCategories.length === 0">
              <td class="px-4 py-6 text-center text-slate-500 dark:text-slate-400" colspan="3">No categories found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Mobile Table -->
      <div class="overflow-x-auto sm:hidden">
        <table class="w-full min-w-[600px] text-left text-xs md:text-sm">
          <thead class="bg-violet-400 text-slate-600 dark:bg-slate-800 dark:text-slate-300">
            <tr>
              <th class="md:px-4 px-2 py-3">ID</th>
              <th class="md:px-4 px-1 py-3">Name</th>
              <th class="md:px-4 px-1 py-3">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-700 dark:bg-slate-900">
            <tr v-for="category in filteredCategories" :key="category.id"
              class="hover:bg-violet-100 dark:hover:bg-slate-800 transition">
              <td class="md:px-4 py-3 font-semibold text-slate-700 dark:text-slate-200">{{ category.id }}</td>
              <td class="md:px-4 py-3 text-violet-400 font-semibold">{{ category.name }}</td>
              <td class="md:px-4 py-3 flex gap-2">
                <button @click="editCategory(category)"
                  class="inline-flex items-center gap-1 rounded-md bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700">
                  <i class="fa-solid fa-pen-to-square"></i>
                </button>
                <button @click="openDeleteModal(category)"
                  class="inline-flex items-center gap-1 rounded-md bg-rose-500 px-3 py-2 text-xs font-semibold text-white hover:bg-rose-600">
                  <i class="fa-solid fa-trash"></i>
                </button>
              </td>
            </tr>
            <tr v-if="filteredCategories.length === 0">
              <td class="md:px-4 py-6 text-center text-slate-500 dark:text-slate-400" colspan="3">No categories found.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="isModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm">
      <div class="w-full max-w-md rounded-2xl bg-white p-5 shadow-2xl
           dark:border dark:border-slate-700 dark:bg-slate-950">

        <!-- Header -->
        <div class="mb-4 flex items-center justify-between">
          <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">
            {{ modalTitle }}
          </h3>

          <button class="flex h-8 w-8 items-center justify-center rounded-full
               text-slate-500 hover:bg-slate-100 hover:text-slate-800
               dark:hover:bg-slate-800 dark:hover:text-white transition" @click="closeModal">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>

        <!-- Body -->
        <div class="space-y-4">
          <!-- Search-style input -->
          <div class="relative">
            <i class="fa-solid fa-tag absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>

            <input v-model="form.name" type="text" placeholder="Category name" class="w-full rounded-lg border border-slate-300 pl-9 pr-3 py-2 text-sm
                 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none
                 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100" />
          </div>
        </div>

        <!-- Footer -->
        <div class="mt-5 flex gap-2 justify-end">
          <button class="rounded-lg border border-slate-300 px-4 py-2 text-sm
               hover:bg-slate-100 dark:border-slate-600 dark:hover:bg-slate-800" @click="closeModal">
            Cancel
          </button>

          <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white
               hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800" @click="saveCategory">
            Save
          </button>
        </div>

      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="isDeleteModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
      @click.self="closeDeleteModal">
      <div
        class="w-full max-w-md rounded-2xl bg-white p-5 shadow-2xl dark:border dark:border-slate-700 dark:bg-slate-950">
        <div class="mb-4 flex items-center justify-between">
          <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">
            Delete Category
          </h3>
          <button
            class="flex h-8 w-8 items-center justify-center rounded-full text-slate-500 hover:bg-slate-100 hover:text-slate-800 dark:hover:bg-slate-800 dark:hover:text-white transition"
            @click="closeDeleteModal">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>

        <p class="text-sm text-slate-700 dark:text-slate-300">
          Are you sure you want to delete "{{ categoryToDelete?.name || 'this category' }}"?
          This action cannot be undone.
        </p>

        <div class="mt-5 flex flex-col gap-2 sm:flex-row sm:justify-end">
          <button
            class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold hover:bg-slate-100 dark:border-slate-600 dark:hover:bg-slate-800"
            @click="closeDeleteModal">
            Cancel
          </button>
          <button
            class="rounded-full bg-rose-500 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-600 dark:bg-rose-600 dark:hover:bg-rose-700"
            @click="confirmDeleteCategory">
            Delete
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import axios from 'axios'
import { ref, computed, onMounted, onUnmounted } from 'vue'

const CATEGORY_API = 'http://myapi2026.infinityfreeapp.com/api/category'

const isLoading = ref(false)
const errorMessage = ref('')
const search = ref('')
const isModalOpen = ref(false)
const isDeleteModalOpen = ref(false)
const editingCategory = ref<{ id: number; name: string } | null>(null)
const categoryToDelete = ref<{ id: number; name: string } | null>(null)
const form = ref({ name: '' })

const categories = ref<{ id: number; name: string }[]>([])

const filteredCategories = computed(() => {
  const term = search.value.toLowerCase().trim()
  if (!term) return categories.value
  return categories.value.filter(category => category.name.toLowerCase().includes(term))
})

const modalTitle = computed(() => (editingCategory.value ? 'Edit Category' : 'Add Category'))

async function fetchCategories() {
  isLoading.value = true
  errorMessage.value = ''
  try {
    const response = await axios.get(`${CATEGORY_API}/get_categories.php`)
    if (response.data?.categories && Array.isArray(response.data.categories)) {
      categories.value = response.data.categories
    } else if (Array.isArray(response.data)) {
      categories.value = response.data
    }
  } catch (error) {
    console.error('Error fetching categories:', error)
    errorMessage.value = 'Failed to load categories'
  } finally {
    isLoading.value = false
  }
}

function openAddModal() {
  editingCategory.value = null
  form.value.name = ''
  isModalOpen.value = true
}

function closeModal() {
  isModalOpen.value = false
}

function editCategory(category: { id: number; name: string }) {
  editingCategory.value = category
  form.value.name = category.name
  isModalOpen.value = true
}

async function saveCategory() {
  const name = form.value.name.trim()
  if (!name) return

  isLoading.value = true
  errorMessage.value = ''

  try {
    if (editingCategory.value) {
      // Update existing category
      await axios.post(`${CATEGORY_API}/update_category.php`, {
        id: editingCategory.value.id,
        name: name
      })
    } else {
      // Add new category
      await axios.post(`${CATEGORY_API}/add_category.php`, {
        name: name
      })
    }
    closeModal()
    await fetchCategories()
  } catch (error) {
    console.error('Error saving category:', error)
    errorMessage.value = 'Failed to save category'
  } finally {
    isLoading.value = false
  }
}

function openDeleteModal(category: { id: number; name: string }) {
  categoryToDelete.value = category
  isDeleteModalOpen.value = true
}

function closeDeleteModal() {
  isDeleteModalOpen.value = false
  categoryToDelete.value = null
}

async function executeDeleteCategory(category: { id: number; name: string }) {
  isLoading.value = true
  errorMessage.value = ''

  try {
    await axios.post(`${CATEGORY_API}/delete_category.php`, {
      id: category.id
    })
    await fetchCategories()
  } catch (error) {
    console.error('Error deleting category:', error)
    errorMessage.value = 'Failed to delete category'
  } finally {
    isLoading.value = false
  }
}

async function confirmDeleteCategory() {
  if (!categoryToDelete.value) return
  await executeDeleteCategory(categoryToDelete.value)
  closeDeleteModal()
}

async function deleteLastCategory() {
  if (categories.value.length === 0) {
    alert('No categories to delete.')
    return
  }
  const last = categories.value[categories.value.length - 1]
  if (!last) return
  await executeDeleteCategory(last)
}

onMounted(() => {
  fetchCategories()
  window.addEventListener('category-add', openAddModal)
  window.addEventListener('category-delete', deleteLastCategory)
})

onUnmounted(() => {
  window.removeEventListener('category-add', openAddModal)
  window.removeEventListener('category-delete', deleteLastCategory)
})
</script>

<style scoped></style>
