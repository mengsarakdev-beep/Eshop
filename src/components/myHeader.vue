<template>
  <nav :class="[
    'fixed top-1 left-1/2 z-50 mb-80 w-11/12 max-w-7xl -translate-x-1/2 transform rounded-xl border px-1 py-2 shadow-lg backdrop-blur-md transition-colors md:px-6',
    isDarkMode
      ? 'border-slate-700 bg-slate-900/90 text-slate-100'
      : 'border-white/70 bg-white/90 text-slate-800',
  ]">
    <div class="relative flex items-center justify-between">
      <!-- Logo -->
      <div class="flex items-center gap-2">
        <svg class="h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 7l9-4 9 4-9 4-9-4" />
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 17l9 4 9-4" />
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9 4 9-4" />
        </svg>
        <span class="text-xl font-bold text-indigo-600">LH</span>
      </div>

      <!-- Desktop Menu -->
      <div class="hidden flex-1 items-center justify-center gap-6 md:flex">


        <div class="relative ml-6 w-72">
          <div :class="[
            'flex items-center rounded-full border px-4 py-2 shadow-sm focus-within:ring-2 focus-within:ring-indigo-500',
            isDarkMode ? 'border-slate-600 bg-slate-800' : 'border-gray-300 bg-white',
          ]">
            <svg :class="['h-5 w-5', isDarkMode ? 'text-slate-400' : 'text-gray-400']" fill="none" stroke="currentColor"
              stroke-width="2" viewBox="0 0 24 24">
              <circle cx="11" cy="11" r="8" />
              <line x1="21" y1="21" x2="16.65" y2="16.65" />
            </svg>

            <input v-model="searchTerm" type="text" placeholder="Search products..." :class="[
              'w-full pl-2 text-sm outline-none',
              isDarkMode ? 'bg-slate-800 text-slate-100 placeholder:text-slate-400' : 'bg-white text-slate-800',
            ]" @input="onSearchInput" />
          </div>
        </div>

        <ul :class="['flex space-x-6 font-semibold', isDarkMode ? 'text-slate-200' : 'text-gray-700']">
          <router-link :to="{ name: 'Home' }" :class="navLinkClass">Home</router-link>
          <router-link :to="{ name: 'Shop' }" :class="navLinkClass">Shop</router-link>
          <router-link :to="{ name: 'Contact' }" :class="navLinkClass">Contact</router-link>

          <template v-if="!auth.isLoggedIn">
            <router-link :to="{ name: 'Login' }" :class="navLinkClass">Login</router-link>
          </template>
          <template v-else>
            <button @click="handleLogout"
              class="block rounded px-2 py-2 text-red-600 transition hover:bg-red-50 dark:hover:bg-red-950/40">
              Logout
            </button>
          </template>
        </ul>
      </div>

      <!-- Right Icons -->
      <div class="flex items-center gap-2 md:gap-4">
        <button type="button" :title="isDarkMode ? 'Switch to light mode' : 'Switch to dark mode'" @click="toggleTheme"
          :class="[
            'rounded-lg p-2 transition',
            isDarkMode ? 'text-amber-300 hover:bg-slate-800' : 'text-gray-700 hover:bg-indigo-50',
          ]">
          <svg v-if="isDarkMode" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="4" />
            <path stroke-linecap="round"
              d="M12 2v2m0 16v2m10-10h-2M4 12H2m17.07 7.07-1.41-1.41M6.34 6.34 4.93 4.93m14.14 0-1.41 1.41M6.34 17.66l-1.41 1.41" />
          </svg>
          <svg v-else class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M21 12.79A9 9 0 1 1 11.21 3c-.02.27-.03.54-.03.81A9 9 0 0 0 20.19 12c.27 0 .54-.01.81-.03Z" />
          </svg>
        </button>

        <button class="rounded-lg p-2 md:hidden"
          :class="isDarkMode ? 'text-slate-200 hover:bg-slate-800' : 'text-gray-700 hover:bg-indigo-50'"
          @click="toggleMobileSearch">
          <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8" />
            <line x1="21" y1="21" x2="16.65" y2="16.65" />
          </svg>
        </button>
        <HeartCount class="hidden md:flex"/>

        <div class="relative">
          <router-link to="/cart" :class="[
            'inline-flex h-10 w-10 items-center justify-center rounded-sm border shadow-sm transition',
            isDarkMode
              ? 'border-slate-700 bg-slate-800/80 text-cyan-300 hover:bg-slate-700'
              : 'border-slate-200 bg-white text-blue-700 hover:bg-indigo-50 hover:text-indigo-600',
          ]">
            <svg class="h-6 w-" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 6h12" />
            </svg>
          </router-link>
          <span v-if="cartCount > 0"
            class="absolute -right-2 -top-2 rounded-full bg-red-500 px-1.5 text-xs font-semibold text-white shadow">
            {{ cartCount }}
          </span>
        </div>

        <button class="rounded-lg p-2 md:hidden" :class="[
          isDarkMode ? 'text-slate-200 hover:bg-slate-800' : 'text-gray-700 hover:bg-indigo-50',
          mobileMenu ? 'bg-indigo-100 text-indigo-600 dark:bg-slate-800 dark:text-white' : '',
        ]" @click="toggleMenu">
          <svg v-if="mobileMenu" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
          <svg v-else class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Mobile Search -->
    <div v-if="mobileSearch" :class="[
      'mt-2 w-full rounded-xl border px-4 py-2 shadow-md backdrop-blur-md md:hidden',
      isDarkMode ? 'border-slate-700 bg-slate-900/90' : 'bg-white/90',
    ]">
      <div :class="[
        'flex items-center rounded-full border px-3 py-1.5 focus-within:ring-2 focus-within:ring-indigo-500',
        isDarkMode ? 'border-slate-600 bg-slate-800' : 'border-gray-300 bg-white',
      ]">
        <svg :class="['h-5 w-5', isDarkMode ? 'text-slate-400' : 'text-gray-400']" fill="none" stroke="currentColor"
          stroke-width="2" viewBox="0 0 24 24">
          <circle cx="11" cy="11" r="8" />
          <line x1="21" y1="21" x2="16.65" y2="16.65" />
        </svg>

        <input v-model="searchTerm" type="text" placeholder="Search products..." :class="[
          'w-full pl-2 outline-none',
          isDarkMode ? 'bg-slate-800 text-slate-100 placeholder:text-slate-400' : 'text-gray-700',
        ]" @input="onSearchInput" />
      </div>
    </div>

    <!-- Mobile Menu -->
    <div v-if="mobileMenu" :class="[
      'mt-2 rounded-xl border-t py-2 shadow-md md:hidden',
      isDarkMode ? 'border-slate-700 bg-slate-900/95' : 'bg-white/90',
    ]">
      <div :class="[
        'mx-4 mb-2 flex items-center justify-between rounded-md border px-3 py-2',
        isDarkMode ? 'border-slate-700 bg-slate-800/80 text-slate-100' : 'border-slate-200 bg-slate-50 text-slate-700',
      ]">
        <span class="text-sm font-semibold">Wishlist
        </span>
        <HeartCount/>

      </div>

      <ul :class="['space-y-2 px-4 text-sm font-medium', isDarkMode ? 'text-slate-200' : 'text-gray-700']">
        <router-link :to="{ name: 'Home' }" :class="navLinkClass" @click="closeMobilePanels">Home</router-link>
        <router-link :to="{ name: 'Shop' }" :class="navLinkClass" @click="closeMobilePanels">Shop</router-link>
        <router-link :to="{ name: 'Contact' }" :class="navLinkClass" @click="closeMobilePanels">Contact</router-link>

        <template v-if="!auth.isLoggedIn">
          <router-link :to="{ name: 'Login' }" :class="navLinkClass" @click="closeMobilePanels">Login</router-link>
        </template>
        <template v-else>
          <button @click="handleLogout"
            class="block w-full rounded px-2 py-2 text-left text-red-600 transition hover:bg-red-50">
            Logout
          </button>
        </template>
      </ul>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useCart } from '../store/cartStore'
import { useAuth } from '../store/authStore'
import { useTheme } from '../store/themeStore'
import HeartCount from './HeartCount.vue'

const router = useRouter()
const route = useRoute()
const auth = useAuth()
const { isDarkMode, toggleTheme, initializeTheme } = useTheme()

const mobileMenu = ref(false)
const mobileSearch = ref(false)

const closeMobilePanels = () => {
  mobileMenu.value = false
  mobileSearch.value = false
}

const toggleMenu = () => {
  mobileMenu.value = !mobileMenu.value
  if (mobileMenu.value) {
    mobileSearch.value = false
  }
}

const toggleMobileSearch = () => {
  mobileSearch.value = !mobileSearch.value
  if (mobileSearch.value) {
    mobileMenu.value = false
  }
}

const { cart } = useCart()
const cartCount = computed(() => cart.reduce((total, item) => total + item.quantity, 0))

const navLinkClass = computed(() =>
  isDarkMode.value
    ? 'block rounded px-2 py-2 transition hover:bg-slate-800'
    : 'block rounded px-2 py-2 transition hover:bg-indigo-50',
)

watch(
  () => route.fullPath,
  () => {
    closeMobilePanels()
  },
)

onMounted(() => {
  initializeTheme()
})

const handleLogout = async () => {
  closeMobilePanels()

  try {
    const res = await fetch('http://localhost/Eshop/Backend/api/auth/logout.php', {
      method: 'POST',
      credentials: 'include',
    })
    const data = await res.json()

    if (data.success) {
      auth.logout()
      localStorage.removeItem('token')
      router.replace('/login')
    } else {
      alert('Logout failed on server')
    }
  } catch (err) {
    console.error('Logout error:', err)
    alert('Server error during logout')
  }
}

const searchTerm = ref('')

const emit = defineEmits<{
  (e: 'search-changed', value: string): void
}>()

const onSearchInput = () => {
  emit('search-changed', searchTerm.value)
}
</script>
