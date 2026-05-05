<template>
  <div
    class="mx-auto mt-6 flex max-w-7xl touch-pan-x gap-2 overflow-x-auto px-3 py-5 transition-colors">
    <button v-for="cat in categories" :key="cat.id" @click="selectCategory(cat)" :class="[
      'whitespace-nowrap rounded-md border px-4 py-1 font-semibold transition-colors flex-shrink-0',
      selectedCategoryId === cat.id
        ? 'border-indigo-600 bg-indigo-600 text-white shadow-sm dark:border-indigo-500 dark:bg-indigo-500'
        : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-100 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800'
    ]">
      {{ cat.name }}
    </button>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue"

interface Category {
  id: number
  name: string
}

const emit = defineEmits<{
  (e: 'change-category', categoryId: number): void
}>()

const categories = ref<Category[]>([])
const selectedCategoryId = ref(0)

const API_CATEGORY = "http://localhost/Eshop/Backend/api/category/get_categories.php"

const selectCategory = (cat: Category) => {
  selectedCategoryId.value = cat.id

  // ✅ send category_id to parent
  emit('change-category', cat.id)
}

// Fetch categories
async function fetchCategories() {
  try {
    const res = await fetch(API_CATEGORY)
    const data = await res.json()

    if (data.success && Array.isArray(data.categories)) {
      categories.value = [{ id: 0, name: "All" }, ...data.categories]
    } else {
      categories.value = [{ id: 0, name: "All" }]
    }
  } catch (err) {
    categories.value = [{ id: 0, name: "All" }]
  }
}

onMounted(fetchCategories)
</script>

<style scoped>
.touch-pan-x {
  -webkit-overflow-scrolling: touch;
}
</style>
