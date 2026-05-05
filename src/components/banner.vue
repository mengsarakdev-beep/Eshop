<template>
  <section class="md:mt-2 max-w-7xl mx-auto px-4 md:pt-8 pt-9">
    <div class="relative h-[340px] overflow-hidden rounded-[5px] bg-slate-900 shadow-2xl md:h-[460px]">
      <div v-for="(slide, index) in slides" :key="index" class="absolute inset-0 transition-opacity duration-700"
        :class="current === index ? 'opacity-100' : 'pointer-events-none opacity-0'">
        <img :src="slide.image" class="h-full w-full object-cover"
          :class="current === index ? 'banner-zoom' : ''" />

          <div class="absolute inset-0 flex items-center">
            <div class="max-w-2xl px-6 text-white md:px-10">
              <p class="text-xs font-semibold uppercase tracking-[0.35em] text-orange-200 items-center text-center">Eshop Banner</p>
              <div class="absolute md:left-1/2 left-1/3 transform -translate-x-1/2 md:mt-24 mt-24 flex flex-wrap gap-3">
              <button
                class="rounded-full border border-white/30 md:px-5 px-3 py-2  text-sm font-semibold text-white transition hover:bg-white/10">
                <router-link :to="{ name: 'Shop' }">Shop Now</router-link>
              </button>
              <button @click="goToCart"
                class="rounded-full border border-white/30 md:px-5 px-3.5 py-2 text-sm font-semibold text-white transition hover:bg-white/10">
                View Cart
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const current = ref(0)
let timer: ReturnType<typeof setInterval> | null = null

const slides = [
  {
    // title: 'Animated fashion picks with smooth zoom effect',
    // description: 'A banner with image scale animation that feels more modern and alive for your Home page.',
    image: 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?auto=format&fit=crop&w=1400&q=80',
    search: 'fashion',
  },
  {
    // title: 'Discover new arrivals and trending styles',
    // description: 'Click the banner or product cards and continue browsing inside the Shop page.',
    image: 'https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=1400&q=80',
    search: 'new',
  },
  {
    // title: 'Shop smarter with fast access to your cart',
    // description: 'Use the animated banner to highlight collections, offers, and featured products.',
    image: 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&w=1400&q=80',
    search: 'sale',
  },
]

function startSlider() {
  timer = setInterval(() => {
    current.value = (current.value + 1) % slides.length
  }, 4000)
}

// function goToShop(search = '') {
//   router.push('/shop')
//   if (search) {
//     router.push({ path: '/shop', query: { search } })
//     return
//   }

// }

function goToCart() {
  router.push('/cart')
}

onMounted(() => {
  startSlider()
})

onBeforeUnmount(() => {
  if (timer) clearInterval(timer)
})
</script>

<style scoped>
.banner-zoom {
  animation: bannerZoom 5s ease-in-out forwards;
}

@keyframes bannerZoom {
  from {
    transform: scale(1);
  }

  to {
    transform: scale(1.12);
  }
}
</style>
