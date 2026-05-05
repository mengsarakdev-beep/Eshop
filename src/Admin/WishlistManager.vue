<template>
  <div class="space-y-4 md:px-3 md:py-4 sm:space-y-6 sm:px-6 sm:py-8">
    <!-- Header -->
    <header
      class="flex flex-col gap-3 rounded-lg border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-950 sm:flex-row sm:items-center sm:justify-between sm:rounded-xl sm:p-5">
      <div class="flex items-center gap-3">
        <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100 sm:text-2xl">Wishlists</h1>
        <div v-if="isLoading"
          class="h-4 w-4 animate-spin rounded-full border-2 border-blue-300 border-t-blue-600 sm:h-5 sm:w-5">
        </div>
      </div>
    </header>

    <!-- Error Message -->
    <section v-if="errorMessage"
      class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-950 dark:text-red-200 sm:rounded-xl sm:p-4">
      {{ errorMessage }}
    </section>

    <!-- Wishlist Container -->
    <section
      class="rounded-md border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-950 sm:rounded-xl">
      <!-- Search & Pagination Controls -->
      <div
        class="flex flex-col gap-3 border-b border-slate-200 p-3 dark:border-slate-700 sm:flex-row sm:items-center sm:justify-between sm:p-4">

        <!-- Search Input with Icon -->
        <div class="relative w-full sm:w-64">
          <svg xmlns="http://www.w3.org/2000/svg"
            class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>

          <input v-model="search" type="text" placeholder="Search by user, product..."
            class="w-full rounded-lg border border-slate-300 pl-10 pr-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:placeholder-slate-400" />
        </div>

        <!-- Right Side -->
        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 sm:text-sm whitespace-nowrap">

          <!-- Count Icon -->
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" />
          </svg>

          <span>{{ filteredWishlists.length }} of {{ wishlists.length }}</span>

          <label class="hidden sm:inline text-slate-700 dark:text-slate-300" for="pageSize">
            Show
          </label>

          <select id="pageSize" v-model.number="pageSize"
            class="rounded-lg border border-slate-300 px-2 py-1 text-xs dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 sm:text-sm">
            <option v-for="n in [5, 10, 20, 50]" :key="n" :value="n">
              {{ n }}
            </option>
          </select>
        </div>
      </div>

      <!-- Wishlist Items Grid -->


      <div class="md:p-3 sm:p-4">

        <!-- Empty State -->
        <div v-if="filteredWishlists.length === 0"
          class="rounded-xl border border-slate-200 bg-slate-50 p-6 text-center text-slate-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400">
          <i class="fa-solid fa-heart text-3xl mb-2 text-slate-300 dark:text-slate-600"></i>
          <p>No wishlist items found.</p>
        </div>

        <!-- Table -->
        <div v-else class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
          <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">

            <!-- Header -->
            <thead class="bg-slate-50 dark:bg-slate-800">
              <tr>
                <th
                  class="md:px-4 px-2 py-3 text-left text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                  User
                </th>

                <th
                  class="md:px-4 px-2 py-3 text-left text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                  Product
                </th>

                <th
                  class="md:px-4 px-2 py-3 text-left text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                  ID
                </th>

                <th
                  class="md:px-4 px-2 py-3 text-left text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                  Price
                </th>

                <th
                  class="md:px-4 px-2 py-3 text-left text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                  Date
                </th>

                <th
                  class="md:px-4 px-2 py-3 text-center text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                  Action
                </th>
              </tr>
            </thead>

            <!-- Body -->
            <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-700 dark:bg-slate-900">
              <tr v-for="item in paginatedWishlists" :key="item.id"
                class="hover:bg-slate-50 dark:hover:bg-slate-800 transition">

                <!-- User -->
                <td class="md:px-4 px-2 py-4 text-sm font-medium text-slate-700 dark:text-slate-200">
                  {{ item.user_name || 'Unknown' }}
                </td>

                <!-- Product -->
                <td class="md:px-4 px-2 py-4">
                  <div class="flex items-center gap-3">

                    <img v-if="item.product_image" :src="item.product_image" :alt="item.product_name"
                      class="h-12 w-12 rounded-lg object-cover border border-slate-200 dark:border-slate-600" />

                    <div v-else
                      class="h-12 w-12 rounded-lg bg-slate-200 flex items-center justify-center text-slate-400 dark:bg-slate-700">
                      <i class="fa-solid fa-image"></i>
                    </div>

                    <span class="hidden md:flex text-sm font-semibold text-slate-800 dark:text-slate-100">
                      {{ item.product_name || 'Product' }}
                    </span>

                  </div>
                </td>

                <!-- Product ID -->
                <td class="md:px-4 px-2 py-4 text-sm text-slate-600 dark:text-slate-300">
                  {{ item.product_id }}
                </td>

                <!-- Price -->
                <td class="md:px-4 px-2 py-4 text-sm font-semibold text-blue-600 dark:text-blue-400">
                  <span v-if="item.product_price">
                    ${{ parseFloat(String(item.product_price)).toFixed(2) }}
                  </span>
                  <span v-else>-</span>
                </td>

                <!-- Date -->
                <td class="md:px-4 px-2 py-4 text-sm text-slate-600 dark:text-slate-300">
                  {{ formatDate(item.created_at) }}
                </td>

                <!-- Action -->
                <td class="md:px-4 px-2 py-4 text-center relative">

                  <!-- Desktop Remove -->
                  <button @click="openDeleteModal(item)"
                    class="hidden md:inline-flex rounded-lg bg-rose-500 px-3 py-2 text-xs font-semibold text-white hover:bg-rose-600 transition dark:bg-rose-600 dark:hover:bg-rose-700">
                    <i class="fa-solid fa-trash mr-1"></i>
                    Remove
                  </button>

                  <!-- Mobile 3 Dot -->
                  <div class="md:hidden relative inline-block">

                    <button @click="toggleMenu(item.id)"
                      class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700">
                      <i class="fa-solid fa-ellipsis-vertical"></i>
                    </button>

                    <!-- Dropdown -->
                    <div v-if="activeMenu === item.id"
                      class="absolute right-0 mt-2 w-36 rounded-lg border border-slate-200 bg-white shadow-lg z-50 dark:border-slate-700 dark:bg-slate-800">
                      <button @click="openDeleteModal(item); activeMenu = null"
                        class="flex w-full items-center gap-2 px-4 py-3 text-sm text-rose-600 hover:bg-slate-50 dark:hover:bg-slate-700">
                        <i class="fa-solid fa-trash"></i>
                        Remove
                      </button>
                    </div>

                  </div>

                </td>

              </tr>
            </tbody>

          </table>
        </div>

      </div>




      <!-- Pagination -->
      <div v-if="pageCount > 1"
        class="flex flex-wrap items-center justify-between gap-2 border-t border-slate-200 p-3 dark:border-slate-700 sm:p-4">
        <button @click="prevPage" :disabled="currentPage === 1"
          class="rounded-lg border border-slate-300 px-2 py-1.5 text-xs hover:bg-slate-100 disabled:opacity-50 dark:border-slate-600 dark:hover:bg-slate-800 sm:px-3 sm:py-2 sm:text-sm">
          <i class="fa-solid fa-chevron-left mr-1"></i> Previous
        </button>
        <div class="flex gap-1 flex-wrap">
          <button v-for="page in pageButtons" :key="page" @click="currentPage = page"
            :class="['px-2 py-1 text-xs rounded-lg border transition', currentPage === page ? 'bg-blue-600 text-white border-blue-600' : 'border-slate-300 hover:bg-slate-100 dark:border-slate-600 dark:hover:bg-slate-800']"
            class="sm:px-3 sm:py-2 sm:text-sm">
            {{ page }}
          </button>
        </div>
        <button @click="nextPage" :disabled="currentPage === pageCount"
          class="rounded-lg border border-slate-300 px-2 py-1.5 text-xs hover:bg-slate-100 disabled:opacity-50 dark:border-slate-600 dark:hover:bg-slate-800 sm:px-3 sm:py-2 sm:text-sm">
          Next <i class="fa-solid fa-chevron-right ml-1"></i>
        </button>
      </div>
    </section>

    <!-- Delete Modal -->
    <div v-if="isDeleteModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4 backdrop-blur-sm"
      @click.self="closeDeleteModal">
      <div
        class="w-full max-w-md overflow-hidden rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-700 dark:bg-slate-950">
        <div class="mb-6 flex items-center justify-between border-b border-slate-200 pb-4 dark:border-slate-700">
          <h3 class="text-xl font-bold text-slate-900 dark:text-slate-100">Remove from Wishlist</h3>
          <button class="text-slate-400 transition hover:text-slate-600 dark:text-slate-300 dark:hover:text-slate-100"
            @click="closeDeleteModal">✕</button>
        </div>

        <p class="text-slate-600 dark:text-slate-300">Are you sure you want to remove "{{ itemToDelete?.product_name ||
          'this item' }}" from the wishlist? This action cannot be undone.</p>

        <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-end">
          <button
            class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800"
            @click="closeDeleteModal">
            <i class="fa-solid fa-times mr-2"></i>
            Cancel
          </button>
          <button
            class="rounded-full bg-rose-500 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-600 dark:bg-rose-600 dark:hover:bg-rose-700"
            @click="confirmDeleteItem">
            <i class="fa-solid fa-trash mr-2"></i>
            Remove
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import axios from 'axios'
import { ref, computed, onMounted, watch } from 'vue'

type WishlistItem = {
  id: number
  user_id: number
  user_name?: string
  product_id: number
  product_name?: string
  product_image?: string
  product_price?: number | string
  created_at?: string
}

const API_BASE = 'http://localhost/Eshop/Backend/api/wishlists'

const wishlists = ref<WishlistItem[]>([])
const isLoading = ref(false)
const errorMessage = ref('')
const search = ref('')
const pageSize = ref(10)
const currentPage = ref(1)

const isDeleteModalOpen = ref(false)
const itemToDelete = ref<WishlistItem | null>(null)

const filteredWishlists = computed(() => {
  if (!search.value) return wishlists.value
  const term = search.value.toLowerCase()
  return wishlists.value.filter((w) =>
    [w.id.toString(), w.user_id.toString(), w.product_id.toString(), w.product_name].some((field) => field?.toString().toLowerCase().includes(term))
  )
})

const pageCount = computed(() => Math.max(1, Math.ceil(filteredWishlists.value.length / pageSize.value)))
const startIndex = computed(() => (currentPage.value - 1) * pageSize.value)
const endIndex = computed(() => Math.min(startIndex.value + pageSize.value, filteredWishlists.value.length))
const paginatedWishlists = computed(() => filteredWishlists.value.slice(startIndex.value, endIndex.value))

const pageButtons = computed(() => {
  const buttons = []
  for (let i = 1; i <= pageCount.value && i <= 5; i++) {
    buttons.push(i)
  }
  return buttons
})

async function fetchWishlists() {
  isLoading.value = true
  errorMessage.value = ''
  try {
    const response = await axios.get(`${API_BASE}/get_all_wishlists.php`, { withCredentials: true })
    let wishlistList: WishlistItem[] = []

    if (response.data?.wishlists && Array.isArray(response.data.wishlists)) {
      wishlistList = response.data.wishlists
    } else if (response.data?.data && Array.isArray(response.data.data)) {
      wishlistList = response.data.data
    } else if (Array.isArray(response.data)) {
      wishlistList = response.data
    }

    wishlists.value = wishlistList
  } catch (error) {
    console.error('Error fetching wishlists:', error)
    errorMessage.value = 'Failed to load wishlists'
  } finally {
    isLoading.value = false
  }
}

function openDeleteModal(item: WishlistItem) {
  itemToDelete.value = item
  isDeleteModalOpen.value = true
  activeMenu.value = null
}

function closeDeleteModal() {
  isDeleteModalOpen.value = false
  itemToDelete.value = null
}

async function confirmDeleteItem() {
  if (!itemToDelete.value) return

  isLoading.value = true
  errorMessage.value = ''

  try {
    await axios.post(`${API_BASE}/remove_from_wishlist.php`, {
      id: itemToDelete.value.id,
      user_id: itemToDelete.value.user_id,
      product_id: itemToDelete.value.product_id
    })
    closeDeleteModal()
    await fetchWishlists()
  } catch (error) {
    console.error('Error removing wishlist item:', error)
    errorMessage.value = 'Failed to remove item from wishlist'
  } finally {
    isLoading.value = false
  }
}

function prevPage() {
  if (currentPage.value > 1) currentPage.value -= 1
}

function nextPage() {
  if (currentPage.value < pageCount.value) currentPage.value += 1
}

function formatDate(date: string | undefined) {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit'
  })
}

watch([search, pageSize, filteredWishlists], () => {
  if (currentPage.value > pageCount.value) currentPage.value = pageCount.value
  if (currentPage.value < 1) currentPage.value = 1
})

onMounted(() => {
  fetchWishlists()
})

const activeMenu = ref<number | null>(null)

function toggleMenu(id: number) {
  activeMenu.value =
    activeMenu.value === id ? null : id
}


</script>

<style scoped></style>
