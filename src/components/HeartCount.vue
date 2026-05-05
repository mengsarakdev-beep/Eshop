<template>
  <div>
    <div
      class="flex cursor-pointer select-none items-center space-x-1"
      :class="{ 'cursor-not-allowed opacity-50': !store.loaded }"
      @click="openPopup"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-6 w-6 transition-colors duration-200"
        :class="count > 0 ? 'text-blue-700 dark:text-blue-400' : 'text-gray-400 dark:text-slate-400'"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
      >
        <path
          d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5
             2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09
             C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5
             c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"
        />
      </svg>

      <span class="font-medium">{{ store.loaded ? count : '...' }}</span>
    </div>

    <transition name="fade">
      <div
        v-if="showPopup"
        class="fixed inset-0 z-50 mt-16 flex items-start justify-center bg-slate-900/35 px-2.5 pt-20 backdrop-blur-sm"
        @click.self="showPopup = false"
      >
        <div class="flex w-full max-w-md flex-col gap-4 rounded-lg border border-slate-200 bg-white p-4 shadow-lg dark:border-slate-700 dark:bg-slate-900">
          <div class="flex items-center justify-between">
            <h2 class="flex items-center gap-1 text-lg font-bold text-slate-900 dark:text-slate-100">My Wishlists</h2>
            <button @click="showPopup = false" class="text-xl text-gray-500 transition hover:text-gray-800 dark:text-slate-400 dark:hover:text-slate-100">
              <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div v-if="loadingItems" class="py-4 text-center text-gray-500 dark:text-slate-300">
            Loading wishlist...
          </div>

          <div v-else-if="wishlistProducts.length === 0" class="py-4 text-center text-gray-500 dark:text-slate-300">
            Your wishlist is empty
          </div>

          <div v-else class="grid max-h-72 grid-cols-2 gap-3 overflow-y-auto md:grid-cols-3">
            <div
              v-for="item in wishlistProducts"
              :key="item.id"
              class="flex flex-col rounded border border-slate-200 p-2 transition-shadow hover:shadow-md dark:border-slate-700 dark:bg-slate-800/60"
            >
              <img
                :src="item.image || fallbackImage"
                class="h-24 w-full cursor-pointer rounded object-cover transition-transform hover:scale-105"
                :alt="item.name"
              />
              <div class="mt-2 flex flex-col gap-1">
                <p class="mx-auto truncate text-sm font-semibold text-slate-800 dark:text-slate-100">{{ item.name }}</p>
                <p class="mx-auto text-xs font-bold text-indigo-600 dark:text-indigo-300">${{ item.price }}</p>
              </div>
              <div class="mt-2 flex gap-2">
                <button
                  class="flex-1 rounded py-1 text-sm font-semibold text-white transition-colors"
                  :class="isOutOfStock(item)
                    ? 'cursor-not-allowed bg-slate-400 dark:bg-slate-600'
                    : 'bg-indigo-600 hover:bg-indigo-700'"
                  :disabled="isOutOfStock(item)"
                  @click.stop="addToCart(item)"
                >
                  {{ isOutOfStock(item) ? 'Out of Stock' : 'Add to Cart' }}
                </button>
                <button
                  class="text-sm text-red-500 transition-colors hover:text-red-600"
                  aria-label="Remove from wishlist"
                  @click.stop="remove(item.id)"
                >
                  <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M19 7L5 7M6 7v12a2 2 0 002 2h8a2 2 0 002-2V7M10 11v6M14 11v6M9 7V5a2 2 0 012-2h2a2 2 0 012 2v2"
                    />
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <button
            class="mt-2 w-full rounded-lg bg-red-600 py-2 font-semibold text-white transition-colors hover:bg-red-700 disabled:cursor-not-allowed disabled:bg-slate-400"
            :disabled="wishlistProducts.length === 0 || clearing"
            @click="clearAll"
          >
            {{ clearing ? 'Clearing...' : 'Clear All' }}
          </button>
        </div>
      </div>
    </transition>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'

export default defineComponent({
  name: 'HeartCount',
})
</script>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useWishlistStore } from '../store/wishlistStore'
import { useCart } from '../store/cartStore'

interface Product {
  id: number
  name: string
  price: number
  image?: string
  stock?: number
}

const store = useWishlistStore()
const cartStore = useCart()

const showPopup = ref(false)
const loadingItems = ref(false)
const clearing = ref(false)
const wishlistProducts = ref<Product[]>([])

const fallbackImage = 'http://localhost/Eshop/Backend/uploads/default.png'
const count = computed(() => store.wishlistCount)

const refreshWishlist = async () => {
  loadingItems.value = true

  const items = await store.fetchWishlistItems()
  wishlistProducts.value = items.map((item) => ({
    ...item,
    price: Number(item.price) || 0,
  }))

  loadingItems.value = false
}

onMounted(async () => {
  if (!store.loaded) {
    await store.loadWishlist()
  }

  await refreshWishlist()
})

const openPopup = async () => {
  if (!store.loaded) {
    await store.loadWishlist()
  }

  showPopup.value = true
  await refreshWishlist()
}

const isOutOfStock = (item: Product) => {
  const stock = item.stock
  return stock !== undefined && stock !== null && Number(stock) <= 0
}

const remove = async (id: number) => {
  const removed = await store.removeFromWishlist(id)

  if (removed) {
    wishlistProducts.value = wishlistProducts.value.filter((item) => item.id !== id)
  }
}

const addToCart = async (item: Product) => {
  if (isOutOfStock(item)) return

  const added = cartStore.addToCart(item)
  if (!added) {
    alert('This item is out of stock or already at the maximum available stock.')
    return
  }

  const removed = await store.removeFromWishlist(item.id)
  if (removed) {
    wishlistProducts.value = wishlistProducts.value.filter((product) => product.id !== item.id)
  }
}

const clearAll = async () => {
  if (!wishlistProducts.value.length) return

  clearing.value = true
  const cleared = await store.clearWishlist()

  if (cleared) {
    wishlistProducts.value = []
  }

  clearing.value = false
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease-in-out;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-thumb {
  background-color: rgba(107, 114, 128, 0.5);
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background-color: rgba(107, 114, 128, 0.7);
}
</style>
