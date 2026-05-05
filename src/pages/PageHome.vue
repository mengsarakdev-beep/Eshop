<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-950">
    <!-- Scrolling Banner Section -->
    <banner ></banner>
    <!-- New Products Scrolling Section -->
    <section class=" py-5 bg-gray-50 dark:bg-slate-950">
      <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-xl font-bold mb-4 text-center ">New Arrivals</h2>

<hr>
        <!-- Scrolling Container -->
        <div class="relative overflow-hidden dark:bg-slate-950 p-1.5">
          <div class="flex animate-scroll-right space-x-6 pb-1" ref="newProductsScroll">
            <!-- Duplicate products for infinite scroll -->
            <div v-for="(product, index) in [...newProducts, ...newProducts, ...newProducts]" :key="`new-${index}`"
              class="flex-shrink-0 w-44 bg-gray-50 dark:bg-gray-700 rounded-lg p-2 shadow-md hover:shadow-lg transition-shadow">
              <img :src="getImageUrl(product.image)" :alt="product.name"
                class="w-full h-32 object-contain rounded-lg mb-3" @error="handleImageError">
              <h3 class="font-semibold text-sm mb-2 text-center text-blue-500">{{ product.name }}</h3>
              <p class="text-gray-600 dark:text-gray-300 text-sm mb-2 line-clamp-2">{{ product.description }}</p>
              <div class="flex justify-between items-center">
                <span class="text-sm font-semibold text-purple-400">${{ Number(product.price).toFixed(2) }}</span>
                <button @click="goToProduct(product)"
                  class="bg-indigo-400 text-white px-2.5 py-1 dark:bg-gray-600 rounded-lg hover:bg-indigo-700 transition-colors">
                  View
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>



  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import type { Product } from '../types'
import Banner from '../components/banner.vue'

const router = useRouter()
const newProducts = ref<Product[]>([])
const featuredOrders = ref<any[]>([])

const API_PRODUCTS = 'http://localhost/Eshop/Backend/api/product/get_products.php'
const FALLBACK_IMAGE = 'http://localhost/Eshop/Backend/uploads/default.png'

// Fetch new products
async function fetchNewProducts() {
  try {
    const response = await fetch(API_PRODUCTS)
    const data = await response.json()

    if (data.success && Array.isArray(data.products)) {
      // Get latest 8 products as "new arrivals"
      newProducts.value = data.products.slice(0, 8).map((product: Product) => ({
        ...product,
        image: product.image || FALLBACK_IMAGE
      }))
    }
  } catch (error) {
    console.error('Failed to fetch products:', error)
  }
}

// Generate sample featured orders
function generateFeaturedOrders() {
  featuredOrders.value = [
    {
      id: '1001',
      customer: 'John Doe',
      total: '299.99',
      items: [
        { id: 1, name: 'Wireless Headphones', price: '149.99' },
        { id: 2, name: 'Phone Case', price: '29.99' },
        { id: 3, name: 'Screen Protector', price: '19.99' }
      ]
    },
    {
      id: '1002',
      customer: 'Jane Smith',
      total: '189.50',
      items: [
        { id: 4, name: 'Smart Watch', price: '129.50' },
        { id: 5, name: 'Watch Band', price: '39.99' },
        { id: 6, name: 'Charger Cable', price: '19.99' }
      ]
    },
    {
      id: '1003',
      customer: 'Mike Johnson',
      total: '449.99',
      items: [
        { id: 7, name: 'Laptop Stand', price: '79.99' },
        { id: 8, name: 'Wireless Mouse', price: '49.99' },
        { id: 9, name: 'Keyboard', price: '119.99' },
        { id: 10, name: 'Mouse Pad', price: '19.99' }
      ]
    },
    {
      id: '1004',
      customer: 'Sarah Wilson',
      total: '159.99',
      items: [
        { id: 11, name: 'Bluetooth Speaker', price: '89.99' },
        { id: 12, name: 'Power Bank', price: '39.99' },
        { id: 13, name: 'Ear Buds', price: '29.99' }
      ]
    },
    {
      id: '1005',
      customer: 'Tom Brown',
      total: '349.99',
      items: [
        { id: 14, name: 'Gaming Mouse', price: '79.99' },
        { id: 15, name: 'Mechanical Keyboard', price: '149.99' },
        { id: 16, name: 'RGB Mouse Pad', price: '29.99' },
        { id: 17, name: 'Cable Organizer', price: '19.99' }
      ]
    }
  ]
}

function getImageUrl(imagePath?: string): string {
  if (!imagePath) return FALLBACK_IMAGE
  return imagePath.startsWith('http') ? imagePath : `http://localhost/Eshop/Backend/uploads/${imagePath}`
}

function handleImageError(event: Event) {
  const img = event.target as HTMLImageElement
  img.src = FALLBACK_IMAGE
}

// function goToShop() {
//   router.push('/shop')
// }
// goToShop()
// function goToCart() {
//   router.push('/cart')
// }
// goToCart()
function goToProduct(product: Product) {
  router.push({ path: '/shop', query: { search: product.name } })
}

onMounted(() => {
  fetchNewProducts()
  generateFeaturedOrders()
})
</script>

<style scoped>
/* Horizontal scrolling animations */
@keyframes scroll-right {
  0% {
    transform: translateX(0);
  }

  100% {
    transform: translateX(-50%);
  }
}

@keyframes scroll-left {
  0% {
    transform: translateX(-50%);
  }

  100% {
    transform: translateX(0);
  }
}

@keyframes scroll-banner {
  0% {
    transform: translateX(100%);
  }

  100% {
    transform: translateX(-100%);
  }
}

.animate-scroll-right {
  animation: scroll-right 60s linear infinite;
}

.animate-scroll-left {
  animation: scroll-left 80s linear infinite;
}

.animate-scroll-banner {
  animation: scroll-banner 40s linear infinite;
}

/* Pause animation on hover */
.animate-scroll-right:hover,
.animate-scroll-left:hover,
.animate-scroll-banner:hover {
  animation-play-state: paused;
}

/* Line clamp utility */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
