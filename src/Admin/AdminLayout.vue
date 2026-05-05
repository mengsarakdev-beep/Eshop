<template>
  <div
  :class="[
    'flex min-h-screen flex-col sm:flex-row max-w-7xl mx-auto transition-colors duration-300 justify-center',
    isDarkMode ? 'bg-slate-950 text-slate-100' : 'bg-slate-100 text-slate-900'
  ]"
>

    <!-- Sidebar -->
    <aside :class="[
      'transition-all duration-300 ease-in-out flex flex-col',
      isOpen ? 'w-64' : 'w-16 sm:w-20',
      isDarkMode ? 'bg-slate-900 text-slate-100 shadow-slate-900/40' : 'bg-gradient-to-b from-blue-900 to-blue-800 text-white',
      'sm:relative fixed bottom-0 left-0 right-0 sm:h-auto h-auto'
    ]">
      <!-- Logo -->
      <div class="flex items-center justify-between p-3 sm:p-4">
        <span v-if="isOpen" class="text-lg font-bold sm:text-xl">
          Admin Panel
        </span>

        <button @click="isOpen = !isOpen" class="rounded bg-white/20 p-2 hover:bg-white/30">
          <i class="fa-solid fa-bars text-sm sm:text-lg"></i>
        </button>
      </div>

      <!-- Menu -->
      <nav class="flex-1 overflow-y-auto">
        <ul class="space-y-1 px-2 sm:space-y-2">
          <li v-for="item in menu" :key="item.name" @click="active = item.name" :class="[
            'flex cursor-pointer items-center gap-2 rounded-lg px-3 py-2 transition-all duration-200 ease-in-out sm:gap-3 sm:px-4 sm:py-3',
            active === item.name
              ? 'bg-white/20'
              : 'hover:bg-white/10'
          ]">
            <i
              :class="[item.icon, item.color, 'w-5 text-center transition-transform duration-200 ease-in-out hover:scale-110 sm:w-6']"></i>
            <span v-if="isOpen" class="text-sm sm:text-base">{{ item.name }}</span>
          </li>
        </ul>
      </nav>

      <!-- Logout -->
      <div class="border-t border-white/20 p-3 sm:p-4">
        <button @click="openLogoutModal"
          class="flex w-full items-center gap-2 rounded-lg px-3 py-2 transition-colors duration-200 hover:bg-white/10 sm:gap-3 sm:px-4 sm:py-3">
          <i
            class="fa-solid fa-sign-out-alt w-5 transition-transform duration-200 ease-in-out hover:scale-110 hover:text-red-400 sm:w-6"></i>
          <span v-if="isOpen" class="text-sm sm:text-base">Logout</span>
        </button>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="flex flex-1 flex-col  pb-24 sm:pb-0">

      <!-- Topbar -->
      <header :class="[
        'border-b px-3 py-3 flex flex-col gap-3 transition-colors duration-300 sm:flex-row sm:items-center sm:justify-between sm:px-6 sm:py-4',
        isDarkMode ? 'bg-slate-900 border-slate-700' : ' bg-blue-900 border-slate-200'
      ]">
        <h1 :class="[
          'text-lg font-semibold tracking-tight sm:text-2xl',
          isDarkMode ? 'text-slate-100' : 'text-white'
        ]">
          {{ active }}
        </h1>

        <div :class="[
          'flex flex-wrap items-center gap-2 rounded-lg border px-2 py-2 shadow-sm sm:gap-3 sm:px-3 sm:py-2',
          isDarkMode ? 'border-slate-700 bg-slate-950/80' : 'border-slate-200 bg-white'
        ]">
          <button @click="navigateToMessages"
            class="relative rounded-full p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 sm:p-2">
            <i :class="['fa-solid fa-bell text-sm sm:text-lg', isDarkMode ? 'text-slate-200' : 'text-slate-600']"></i>
            <span v-if="unreadContacts > 0"
              class="absolute -top-0.5 -right-0.5 inline-flex h-4 min-w-4 items-center justify-center rounded-full bg-rose-500 px-1 text-xs font-bold text-white sm:h-5 sm:min-w-5">
              {{ unreadContacts }}
            </span>
          </button>

          <button @click="toggleTheme"
            class="rounded-full p-1.5 transition hover:bg-slate-100 dark:hover:bg-slate-800 sm:p-2"
            :title="isDarkMode ? 'Switch to light mode' : 'Switch to dark mode'">
            <svg v-if="isDarkMode" class="h-4 w-4 text-amber-300 sm:h-5 sm:w-5" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="4" />
              <path stroke-linecap="round"
                d="M12 2v2m0 16v2m10-10h-2M4 12H2m17.07 7.07-1.41-1.41M6.34 6.34 4.93 4.93m14.14 0-1.41 1.41M6.34 17.66l-1.41 1.41" />
            </svg>
            <svg v-else class="h-4 w-4 text-slate-700 sm:h-5 sm:w-5" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M21 12.79A9 9 0 1 1 11.21 3c-.02.27-.03.54-.03.81A9 9 0 0 0 20.19 12c.27 0 .54-.01.81-.03Z" />
            </svg>
          </button>

          <img :src="auth.image || 'https://i.pravatar.cc/40?img=12'" alt="profile"
            class="h-8 w-8 cursor-pointer rounded-full border sm:h-10 sm:w-10"
            :class="isDarkMode ? 'border-slate-600' : 'border-slate-300'" @click="openProfileModal" />
        </div>
      </header>

      <div
  v-if="isProfileModalOpen"
  class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4"
>
  <div class="max-h-[95vh] w-full max-w-lg overflow-y-auto">
    <div
      :class="[
        'rounded-2xl shadow-2xl ring-1',
        isDarkMode ? 'bg-slate-950 ring-slate-800' : 'bg-white ring-slate-200'
      ]"
    >
      <!-- Header -->
      <div
        :class="[
          'flex items-center justify-between border-b p-5 sm:p-6',
          isDarkMode ? 'border-slate-800' : 'border-slate-200'
        ]"
      >
        <h3 class="flex items-center gap-2 text-lg font-semibold"
            :class="isDarkMode ? 'text-slate-100' : 'text-slate-900'">

          <!-- user icon -->
          <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-width="2" d="M5.5 21a8.38 8.38 0 0113 0M12 13a4 4 0 100-8 4 4 0 000 8z"/>
          </svg>

          Edit Profile
        </h3>

        <button @click="closeProfileModal"
          class="text-xl"
          :class="isDarkMode ? 'text-slate-400 hover:text-slate-200' : 'text-slate-400 hover:text-slate-600'">
          ✕
        </button>
      </div>

      <!-- Body -->
      <div class="space-y-4 p-5 sm:p-6">

        <!-- Username -->
        <div>
          <label class="text-xs font-medium uppercase text-slate-500">Username</label>

          <div class="relative mt-1">
            <svg class="absolute left-3 top-2.5 h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-width="2" d="M16 11a4 4 0 11-8 0 4 4 0 018 0zM4 20a8 8 0 0116 0"/>
            </svg>

            <input
              v-model="profileForm.username"
              class="w-full rounded-lg border border-slate-300 pl-10 pr-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
            />
          </div>
        </div>

        <!-- Email -->
        <div>
          <label class="text-xs font-medium uppercase text-slate-500">Email</label>

          <div class="relative mt-1">
            <svg class="absolute left-3 top-2.5 h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-width="2" d="M3 8l9 6 9-6M4 6h16v12H4z"/>
            </svg>

            <input
              v-model="profileForm.email"
              type="email"
              class="w-full rounded-lg border border-slate-300 pl-10 pr-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
            />
          </div>
        </div>

        <!-- Phone -->
        <div>
          <label class="text-xs font-medium uppercase text-slate-500">Phone</label>

          <div class="relative mt-1">
            <svg class="absolute left-3 top-2.5 h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-width="2" d="M3 5h6l2 5-3 2a11 11 0 005 5l2-3 5 2v6a2 2 0 01-2 2C9.716 24 0 14.284 0 2a2 2 0 012-2h6z"/>
            </svg>

            <input
              v-model="profileForm.phone"
              class="w-full rounded-lg border border-slate-300 pl-10 pr-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
            />
          </div>
        </div>

        <!-- Image Upload -->
        <div>
          <label class="text-xs font-medium uppercase text-slate-500">Profile Image</label>

          <div class="relative mt-1">
            <svg class="absolute left-3 top-2.5 h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-width="2" d="M4 16l4-4a2 2 0 012 0l4 4m4-4l-4-4a2 2 0 00-2 0l-4 4m12 8H4"/>
            </svg>

            <input
              type="file"
              accept="image/*"
              @change="onImageSelected"
              class="w-full rounded-lg border border-slate-300 pl-10 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"
            />
          </div>
        </div>

        <!-- Preview -->
        <div
          v-if="profileForm.imagePreview"
          class="flex items-center gap-4 rounded-xl border p-3"
          :class="isDarkMode ? 'border-slate-800 bg-slate-900' : 'border-slate-200 bg-slate-50'"
        >
          <img :src="profileForm.imagePreview" class="h-12 w-12 rounded-full object-cover" />
          <span class="text-sm text-slate-500">Preview</span>
        </div>

        <!-- Error -->
        <div v-if="profileUpdateError"
          class="rounded-lg bg-rose-50 px-3 py-2 text-sm text-rose-700">
          {{ profileUpdateError }}
        </div>
      </div>

      <!-- Footer -->
      <div class="flex flex-col-reverse gap-3 border-t p-5 sm:flex-row sm:justify-end sm:p-6"
        :class="isDarkMode ? 'border-slate-800' : 'border-slate-200'">

        <!-- Cancel -->
        <button
          @click="closeProfileModal"
          class="flex items-center justify-center gap-2 rounded-lg border px-5 py-2 text-sm font-medium transition sm:w-auto"
          :class="isDarkMode
            ? 'border-slate-700 text-slate-200 hover:bg-slate-800'
            : 'border-slate-300 text-slate-700 hover:bg-slate-100'"
        >
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
          Cancel
        </button>

        <!-- Save -->
        <button
          @click="submitProfileUpdate"
          :disabled="isSavingProfile"
          class="flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 disabled:opacity-60"
        >
          <svg v-if="!isSavingProfile" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>

          <svg v-else class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
          </svg>

          {{ isSavingProfile ? 'Saving...' : 'Save Changes' }}
        </button>
      </div>
    </div>
  </div>
</div>

      <!-- Logout Confirmation Modal -->
      <div v-if="isLogoutModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">
        <div :class="[
          'w-full max-w-sm rounded-2xl p-8 shadow-2xl ring-1',
          isDarkMode ? 'bg-slate-950 ring-slate-700' : 'bg-white ring-slate-200'
        ]">
          <div class="mb-6 flex items-center justify-between" :class="isDarkMode ? 'text-slate-100' : 'text-slate-900'">
            <h3 :class="isDarkMode ? 'text-slate-100' : 'text-slate-900'" class="text-xl font-semibold">Confirm Logout
            </h3>
            <button :class="isDarkMode ? 'text-slate-400 hover:text-slate-200' : 'text-slate-400 hover:text-slate-600'"
              class="text-lg" @click="closeLogoutModal">✕</button>
          </div>

          <p class="mb-8 text-slate-600">Are you sure you want to logout? You'll need to login again to access the admin
            panel.</p>

          <div class="flex justify-end gap-3">
            <button
              class="rounded-lg border border-slate-300 px-5 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors"
              @click="closeLogoutModal">
              Cancel
            </button>
            <button
              class="rounded-lg bg-red-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 transition-colors"
              @click="confirmLogout">
              Logout
            </button>
          </div>
        </div>
      </div>

      <!-- Page Content -->
      <main class="flex-1">
        <div :class="[
          'rounded-sm transition-colors duration-300 w-full',
          isDarkMode ? 'bg-slate-900 shadow-slate-900/20' : 'bg-white'
        ]">
          <component :is="getComponent(active)" />
        </div>
      </main>

    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, watch, onMounted } from 'vue'

import Dashboard from './Dashboard.vue'
import ProductManager from './ProductManager.vue'
import OrderManager from './orderManager.vue'
import CategoryManager from './CategoryManager.vue'
import WishlistManager from './WishlistManager.vue'
import UserManager from './UserManager.vue'
import ContactManager from './ContactManager.vue'
import { useTheme } from '../store/themeStore'
import { useAuth } from '../store/authStore'

const { isDarkMode, toggleTheme, initializeTheme } = useTheme()
const auth = useAuth()

const isOpen = ref(true)
const active = ref('Overview')

// Menu with multi-color icons
const menu = [
  { name: 'Overview', icon: 'fa-solid fa-tachometer-alt', color: 'text-blue-400' },
  { name: 'Products', icon: 'fa-solid fa-cube', color: 'text-green-400' },
  { name: 'Categories', icon: 'fa-solid fa-layer-group', color: 'text-purple-400' },
  { name: 'Orders', icon: 'fa-solid fa-shopping-cart', color: 'text-yellow-400' },
  { name: 'Wishlist', icon: 'fa-solid fa-heart', color: 'text-pink-400' },
  { name: 'User', icon: 'fa-solid fa-user-friends', color: 'text-red-400' },
  { name: 'Contacts', icon: 'fa-solid fa-envelope', color: 'text-cyan-400' },
]

const profileApiUrl = 'http://localhost/Eshop/Backend/api/auth/update_profile.php'

const isProfileModalOpen = ref(false)
const isSavingProfile = ref(false)
const profileUpdateError = ref('')
const isLogoutModalOpen = ref(false)

const profileForm = reactive({
  username: auth.username || '',
  email: auth.email || '',
  phone: auth.phone || '',
  imageFile: null as File | null,
  imagePreview: auth.image || ''
})

const unreadContacts = ref(0)

async function fetchUnreadContacts() {
  try {
    const response = await fetch('http://localhost/Eshop/Backend/api/contact/get_contacts.php', {
      method: 'GET',
      credentials: 'include'
    })
    const data = await response.json()
    if (data.success) {
      unreadContacts.value = data.unread || 0
    }
  } catch (error) {
    console.error('Unable to fetch contact unread count', error)
    unreadContacts.value = 0
  }
}

function navigateToMessages() {
  active.value = 'Contacts'
}


watch(
  () => [auth.username, auth.email, auth.phone, auth.image],
  () => {
    profileForm.username = auth.username
    profileForm.email = auth.email
    profileForm.phone = auth.phone
    profileForm.imagePreview = auth.image
  }
)

function openProfileModal() {
  profileUpdateError.value = ''
  profileForm.username = auth.username
  profileForm.email = auth.email
  profileForm.phone = auth.phone
  profileForm.imagePreview = auth.image
  profileForm.imageFile = null
  isProfileModalOpen.value = true
}

function closeProfileModal() {
  isProfileModalOpen.value = false
}

function onImageSelected(event: Event) {
  const input = event.target as HTMLInputElement
  if (!input.files || input.files.length === 0) {
    profileForm.imageFile = null
    return
  }

  const file = input.files[0]
  if (!file) {
    profileForm.imageFile = null
    return
  }

  profileForm.imageFile = file
  const reader = new FileReader()
  reader.onload = () => {
    profileForm.imagePreview = reader.result as string
  }
  reader.readAsDataURL(file)
}

async function submitProfileUpdate() {
  profileUpdateError.value = ''

  const username = profileForm.username.trim()
  const email = profileForm.email.trim()
  const phone = profileForm.phone.trim()

  if (!username || !email || !phone) {
    profileUpdateError.value = 'Username, email, and phone are required.'
    return
  }

  isSavingProfile.value = true

  try {
    const formData = new FormData()
    formData.append('username', username)
    formData.append('email', email)
    formData.append('phone', phone)
    if (profileForm.imageFile) {
      formData.append('image', profileForm.imageFile)
    }

    const response = await fetch(profileApiUrl, {
      method: 'POST',
      body: formData,
      credentials: 'include'
    })

    const data = await response.json()

    if (!data.success) {
      profileUpdateError.value = data.message || 'Failed to update profile.'
      return
    }

    auth.updateProfile({
      username: data.user?.username || username,
      email: data.user?.email || email,
      phone: data.user?.phone || phone,
      image: data.user?.image || auth.image
    })

    profileForm.imagePreview = data.user?.image || profileForm.imagePreview

    isProfileModalOpen.value = false
    alert('Profile updated successfully.')
  } catch (error) {
    console.error('Profile update error:', error)
    profileUpdateError.value = 'Error connecting to server.'
  } finally {
    isSavingProfile.value = false
  }
}

const components = {
  Overview: Dashboard,
  Products: ProductManager,
  Categories: CategoryManager,
  Orders: OrderManager,
  Wishlist: WishlistManager,
  User: UserManager,
  Contacts: ContactManager,
}

function getComponent(view: string) {
  return components[view as keyof typeof components] || Dashboard
}

function openLogoutModal() {
  isLogoutModalOpen.value = true
}

function closeLogoutModal() {
  isLogoutModalOpen.value = false
}

function confirmLogout() {
  isLogoutModalOpen.value = false
  window.location.href = '/login'
}

onMounted(() => {
  initializeTheme()
  fetchUnreadContacts()
})
</script>

<style scoped>
/* Hover scale animations handled via Tailwind classes in the template */
</style>
