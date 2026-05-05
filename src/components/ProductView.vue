<template>
  <section
    class="rounded-[28px] bg-white p-4 shadow-sm ring-1 ring-slate-200 transition-colors dark:bg-slate-900 dark:ring-slate-700 md:p-6">
    <div class="mb-6 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
      <div>
        <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Fresh product preview</h2>
        <p class="text-sm text-slate-500 dark:text-slate-300">Select any card below and continue browsing in the `Shop`
          page.</p>
      </div>

      <button @click="goToShop()"
        class="w-fit rounded-full border border-orange-200 bg-orange-50 px-4 py-2 text-sm font-semibold text-orange-600 transition hover:bg-orange-100 dark:border-orange-500/40 dark:bg-orange-500/10 dark:text-orange-200 dark:hover:bg-orange-500/20">
        Browse Shop
      </button>
    </div>

    <div v-if="loading" class="grid grid-cols-1 gap-4 md:grid-cols-3">
      <div v-for="item in 3" :key="item" class="h-64 animate-pulse rounded-3xl bg-slate-100 dark:bg-slate-800" />
    </div>

    <div v-else class="grid grid-cols-1 gap-4 md:grid-cols-3">
      <button v-for="(product, index) in products.slice(0, 3)" :key="product.id" @click="goToShop(product.name)" :class="[
        'group overflow-hidden rounded-3xl text-left transition hover:-translate-y-1 hover:shadow-xl',
        index === 0
          ? 'bg-slate-900 text-white md:col-span-2'
          : 'border border-slate-200 bg-slate-50 text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100'
      ]">
        <div :class="index === 0 ? 'grid gap-0 md:grid-cols-2' : ''">
          <img :src="absoluteImageUrl(product.image)" :alt="product.name"
            class="h-52 w-full object-cover transition group-hover:scale-105" @error="onImageError" />

          <div class="p-5">
            <span :class="index === 0 ? 'bg-white/10 text-orange-200' : 'bg-orange-100 text-orange-700'"
              class="inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em]">
              {{ product.category_name || 'Featured' }}
            </span>

            <h3 class="mt-3 line-clamp-2 text-xl font-bold">{{ product.name }}</h3>
            <p :class="index === 0 ? 'text-slate-300' : 'text-slate-500 dark:text-slate-300'" class="mt-2 text-sm">
              Open this item in the shop page and continue exploring more products.
            </p>

            <div class="mt-5 flex items-center justify-between">
              <span :class="index === 0 ? 'text-orange-300' : 'text-orange-600 dark:text-orange-300'"
                class="text-lg font-bold">
                ${{ Number(product.price).toFixed(2) }}
              </span>
              <span :class="index === 0 ? 'text-white' : 'text-slate-800 dark:text-slate-100'"
                class="text-sm font-semibold">
                Open in Shop →
              </span>
            </div>
          </div>
        </div>
      </button>
    </div>
  </section>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import type { Product } from '../types'

const router = useRouter()
const products = ref<Product[]>([])
const loading = ref(true)
const fallbackImage = 'http://localhost/Eshop/Backend/uploads/default.png'

async function fetchFeaturedProducts() {
  try {
    const res = await fetch('http://localhost/Eshop/Backend/api/product/get_products.php')
    const data = await res.json()

    if (data.success && Array.isArray(data.products)) {
      products.value = data.products.slice(0, 6).map((product: Product) => ({
        ...product,
        image: product.image || fallbackImage,
      }))
    } else {
      products.value = []
    }
  } catch (error) {
    console.error('Failed to load featured products:', error)
    products.value = []
  } finally {
    loading.value = false
  }
}

function goToShop(search = '') {
  if (search) {
    router.push({ path: '/shop', query: { search } })
    return
  }

  router.push('/shop')
}

function absoluteImageUrl(path?: string) {
  if (!path) return fallbackImage
  return path.startsWith('http') ? path : `http://localhost/Eshop/Backend/uploads/${path}`
}

function onImageError(event: Event) {
  ; (event.target as HTMLImageElement).src = fallbackImage
}

onMounted(fetchFeaturedProducts)
</script>
