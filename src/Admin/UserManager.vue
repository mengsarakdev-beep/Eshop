<template>
  <div class="space-y-6 md:px-3 md:py-4 sm:px-6 sm:py-8">
    <header
      class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between rounded-xl bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-950">
      <div class="flex items-center gap-3">
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-slate-100">Users</h1>
        <div v-if="isLoading" class="h-5 w-5 animate-spin rounded-full border-2 border-blue-300 border-t-blue-600">
        </div>
      </div>
      <button
        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-50"
        @click="openAddModal" :disabled="isLoading">
        <i class="fa-solid fa-plus"></i>
        Add User
      </button>
    </header>

    <section v-if="errorMessage"
      class="rounded-md bg-red-50 border border-red-200 p-4 text-sm text-red-700 dark:bg-red-950 dark:border-red-800 dark:text-red-200">
      {{ errorMessage }}
    </section>

    <section class="rounded-md bg-white md:p-5 shadow-sm dark:bg-slate-950 dark:border-slate-700 dark:border">

<div
  class="md:mb-4 flex flex-col gap-4 rounded-xl bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-950 sm:flex-row sm:items-center sm:justify-between"
>

  <!-- Search Section -->
  <div class="flex flex-col gap-3 sm:flex-row sm:items-center">

    <!-- Search Input with Icon -->
    <div class="relative w-full sm:w-72">
      <i
        class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"
      ></i>

      <input
        v-model="search"
        type="text"
        placeholder="Search users..."
        class="w-full rounded-lg border border-slate-300 pl-10 pr-3 py-2 outline-none focus:border-blue-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100"
      />
    </div>

    <!-- Count -->
    <span class="text-sm text-slate-500 dark:text-slate-400">
      {{ filteredUsers.length }} of {{ users.length }} users
    </span>

  </div>

  <!-- Page Size Section -->
  <div class="flex items-center gap-2">

    <label
      class="text-sm font-medium text-slate-700 dark:text-slate-300"
      for="pageSize"
    >
      <i class="fa-solid fa-list mr-1"></i>
      Show
    </label>

    <select
      id="pageSize"
      v-model.number="pageSize"
      class="rounded-lg border border-slate-300 px-3 py-2 outline-none focus:border-blue-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100"
    >
      <option
        v-for="n in [5, 10, 20, 50]"
        :key="n"
        :value="n"
      >
        {{ n }}
      </option>
    </select>

    <span class="text-sm text-slate-500 dark:text-slate-400">
      entries
    </span>

  </div>

</div>




<div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">

  <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">

    <!-- Header -->
    <thead class="bg-slate-50 dark:bg-slate-800">
      <tr>
        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
          ID
        </th>

        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
          Name
        </th>

        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
          Email
        </th>

        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
          Phone
        </th>

        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
          Role
        </th>

        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
          Joined
        </th>

        <th class="px-4 py-3 text-center text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
          Action
        </th>
      </tr>
    </thead>

    <!-- Body -->
    <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-700 dark:bg-slate-900">

      <tr
        v-for="user in paginatedUsers"
        :key="user.id"
        class="hover:bg-slate-50 dark:hover:bg-slate-800 transition"
      >

        <!-- ID -->
        <td class="md:px-4 md:py-4 text-sm font-semibold text-slate-700 dark:text-slate-200">
          {{ user.id }}
        </td>

        <!-- Name -->
        <td class="md:px-4 md:py-4 text-sm font-medium text-slate-700 dark:text-slate-200">
          {{ user.name || user.username || 'N/A' }}
        </td>

        <!-- Email -->
        <td class="md:px-4 md:py-4 text-sm text-slate-600 dark:text-slate-300">
          {{ user.email || 'N/A' }}
        </td>

        <!-- Phone -->
        <td class="md:px-4 md:py-4 text-sm text-slate-600 dark:text-slate-300">
          {{ user.phone || 'N/A' }}
        </td>

        <!-- Role -->
        <td class="md:px-4 md:py-4">
          <span
            class="rounded-full px-3 py-1 text-xs font-semibold uppercase"
            :class="
              user.role === 'admin'
                ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'
                : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'
            "
          >
            {{ user.role || 'user' }}
          </span>
        </td>

        <!-- Joined -->
        <td class="md:px-4 md:py-4 text-sm text-slate-600 dark:text-slate-300">
          {{ formatDate(user.created_at) }}
        </td>

        <!-- Action -->

<!-- Action -->
<td class="md:px-4 md:py-4 text-center relative">

  <!-- Desktop Buttons -->
  <div class="hidden md:flex items-center justify-center gap-2">

    <button
      @click="openViewModal(user)"
      class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700 transition inline-flex items-center gap-1"
    >
      <i class="fa-solid fa-eye"></i>
      View
    </button>

    <button
      @click="openDeleteModal(user)"
      class="rounded-lg bg-rose-500 px-3 py-2 text-xs font-semibold text-white hover:bg-rose-600 transition inline-flex items-center gap-1"
    >
      <i class="fa-solid fa-trash"></i>
      Delete
    </button>

  </div>

  <!-- Mobile 3 Dot -->
  <div class="md:hidden relative inline-block">

    <button
      @click="toggleMenu(user.id)"
      class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700"
    >
      <i class="fa-solid fa-ellipsis-vertical"></i>
    </button>

    <!-- Dropdown Menu -->
    <div
      v-if="activeMenu === user.id"
      class="absolute right-0 mt-2 w-40 rounded-lg border border-slate-200 bg-white shadow-lg z-50 dark:border-slate-700 dark:bg-slate-800"
    >

      <!-- View -->
      <button
        @click="openViewModal(user); activeMenu = null"
        class="flex w-full items-center gap-2 px-4 py-3 text-sm text-blue-600 hover:bg-slate-50 dark:hover:bg-slate-700"
      >
        <i class="fa-solid fa-eye"></i>
        View
      </button>

      <!-- Delete -->
      <button
        @click="openDeleteModal(user); activeMenu = null"
        class="flex w-full items-center gap-2 px-4 py-3 text-sm text-rose-600 hover:bg-slate-50 dark:hover:bg-slate-700"
      >
        <i class="fa-solid fa-trash"></i>
        Delete
      </button>

    </div>

  </div>

</td>



      </tr>

      <!-- Empty State -->
      <tr v-if="filteredUsers.length === 0">
        <td
          colspan="7"
          class="px-4 py-8 text-center text-slate-500 dark:text-slate-400"
        >
          No users found.
        </td>
      </tr>

    </tbody>

  </table>

</div>



      <!-- Pagination -->
      <div class="mt-4 flex items-center justify-between">
        <button @click="prevPage" :disabled="currentPage === 1"
          class="rounded-lg border border-slate-300 px-3 py-2 text-sm hover:bg-slate-100 disabled:opacity-50">
          <i class="fa-solid fa-chevron-left"></i> Previous
        </button>
        <div class="flex gap-1">
          <button v-for="page in pageButtons" :key="page" @click="currentPage = page"
            :class="['px-3 py-2 text-sm rounded-lg border', currentPage === page ? 'bg-blue-600 text-white border-blue-600' : 'border-slate-300 hover:bg-slate-100']">
            {{ page }}
          </button>
        </div>
        <button @click="nextPage" :disabled="currentPage === pageCount"
          class="rounded-lg border border-slate-300 px-3 py-2 text-sm hover:bg-slate-100 disabled:opacity-50">
          Next <i class="fa-solid fa-chevron-right"></i>
        </button>
      </div>
    </section>

    <!-- Add User Modal -->
    <div v-if="isAddModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
      <div class="w-full max-w-md rounded-xl bg-white p-4 sm:p-6 shadow-xl">
        <div class="mb-6 flex items-center justify-between border-b border-slate-200 pb-4">
          <h3 class="text-2xl font-bold text-slate-900">Add User</h3>
          <button class="text-slate-400 transition hover:text-slate-600" @click="isAddModalOpen = false">✕</button>
        </div>

        <div class="space-y-4">
          <div>
            <p class="text-xs uppercase text-slate-400">Name</p>
            <input v-model="newUser.name" class="w-full rounded border border-slate-300 px-3 py-2" />
          </div>
          <div>
            <p class="text-xs uppercase text-slate-400">Email</p>
            <input v-model="newUser.email" type="email" class="w-full rounded border border-slate-300 px-3 py-2" />
          </div>
          <div>
            <p class="text-xs uppercase text-slate-400">Phone</p>
            <input v-model="newUser.phone" class="w-full rounded border border-slate-300 px-3 py-2" />
          </div>
          <div>
            <p class="text-xs uppercase text-slate-400">Password</p>
            <input v-model="newUser.password" type="password"
              class="w-full rounded border border-slate-300 px-3 py-2" />
          </div>
          <div>
            <p class="text-xs uppercase text-slate-400">Role</p>
            <select v-model="newUser.role" class="w-full rounded border border-slate-300 px-3 py-2">
              <option value="user">User</option>
              <option value="admin">Admin</option>
            </select>
          </div>
        </div>

        <div class="mt-6 flex justify-end gap-2">
          <button
            class="rounded-lg border border-slate-300 px-4 py-2 text-sm hover:bg-slate-100 inline-flex items-center gap-2"
            @click="isAddModalOpen = false">
            <i class="fa-solid fa-times"></i>
            Cancel
          </button>
          <button
            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 inline-flex items-center gap-2"
            @click="confirmAddUser">
            <i class="fa-solid fa-plus"></i>
            Add User
          </button>
        </div>
      </div>
    </div>

    <!-- View User Modal -->
    <div v-if="isViewModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
      <div
        class="w-full max-w-md rounded-xl bg-white p-4 sm:p-6 shadow-xl dark:bg-slate-950 dark:border-slate-700 dark:border">
        <div class="mb-6 flex items-center justify-between border-b border-slate-200 pb-4 dark:border-slate-700">
          <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ userToView?.name || userToView?.username
            || 'User' }}</h3>
          <button class="text-slate-400 transition hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-400"
            @click="closeViewModal">✕</button>
        </div>

        <div class="space-y-4">
          <div>
            <p class="text-xs uppercase text-slate-400 dark:text-slate-500">Email</p>
            <p class="text-sm font-semibold dark:text-slate-300">{{ userToView?.email || 'N/A' }}</p>
          </div>
          <div>
            <p class="text-xs uppercase text-slate-400 dark:text-slate-500">Phone</p>
            <p class="text-sm font-semibold dark:text-slate-300">{{ userToView?.phone || 'N/A' }}</p>
          </div>
          <div>
            <p class="text-xs uppercase text-slate-400 dark:text-slate-500">Role</p>
            <p class="text-sm font-semibold dark:text-slate-300">{{ userToView?.role?.toUpperCase() || 'USER' }}</p>
          </div>
          <div>
            <p class="text-xs uppercase text-slate-400 dark:text-slate-500">Joined</p>
            <p class="text-sm dark:text-slate-400">{{ formatDate(userToView?.created_at) }}</p>
          </div>
          <div v-if="userToView?.address">
            <p class="text-xs uppercase text-slate-400 dark:text-slate-500">Address</p>
            <p class="text-sm dark:text-slate-400">{{ userToView.address }}</p>
          </div>
        </div>

        <div class="mt-6 flex justify-end gap-2">
          <button
            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 inline-flex items-center gap-2 dark:bg-blue-700 dark:hover:bg-blue-800"
            @click="closeViewModal">
            <i class="fa-solid fa-xmark"></i>
            Close
          </button>
        </div>
      </div>
    </div>

    <!-- Delete User Modal -->
    <div v-if="isDeleteModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
      <div class="w-full max-w-md rounded-xl bg-white p-4 sm:p-6 shadow-xl">
        <div class="mb-6 flex items-center justify-between border-b border-slate-200 pb-4">
          <h3 class="text-xl font-bold text-slate-900">Delete User</h3>
          <button class="text-slate-400 transition hover:text-slate-600" @click="closeDeleteModal">✕</button>
        </div>

        <p class="text-slate-600">Are you sure you want to delete {{ userToDelete?.name || userToDelete?.username }}?
          This action cannot be undone.</p>

        <div class="mt-6 flex justify-end gap-2">
          <button
            class="rounded-lg border border-slate-300 px-4 py-2 text-sm hover:bg-slate-100 inline-flex items-center gap-2"
            @click="closeDeleteModal">
            <i class="fa-solid fa-times"></i>
            Cancel
          </button>
          <button
            class="rounded-lg bg-rose-500 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-600 inline-flex items-center gap-2"
            @click="confirmDeleteUser">
            <i class="fa-solid fa-trash"></i>
            Delete
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import axios from 'axios'
import { ref, computed, onMounted, watch } from 'vue'

type User = {
  id: number
  name?: string
  username?: string
  email?: string
  phone?: string
  role?: string
  address?: string
  created_at?: string
}

const API_BASE = 'http://localhost/Eshop/Backend/api/auth'

const users = ref<User[]>([])
const isLoading = ref(false)
const errorMessage = ref('')
const search = ref('')
const pageSize = ref(10)
const currentPage = ref(1)

const isViewModalOpen = ref(false)
const isAddModalOpen = ref(false)
const isDeleteModalOpen = ref(false)

const userToView = ref<User | null>(null)
const userToDelete = ref<User | null>(null)

const newUser = ref({
  name: '',
  email: '',
  phone: '',
  password: '',
  role: 'user'
})

const filteredUsers = computed(() => {
  if (!search.value) return users.value
  const term = search.value.toLowerCase()
  return users.value.filter((u) =>
    [u.id.toString(), u.name, u.username, u.email, u.phone].some((field) => field?.toString().toLowerCase().includes(term))
  )
})

const pageCount = computed(() => Math.max(1, Math.ceil(filteredUsers.value.length / pageSize.value)))
const startIndex = computed(() => (currentPage.value - 1) * pageSize.value)
const endIndex = computed(() => Math.min(startIndex.value + pageSize.value, filteredUsers.value.length))
const paginatedUsers = computed(() => filteredUsers.value.slice(startIndex.value, endIndex.value))

const pageButtons = computed(() => {
  const buttons = []
  for (let i = 1; i <= pageCount.value && i <= 5; i++) {
    buttons.push(i)
  }
  return buttons
})

async function fetchUsers() {
  isLoading.value = true
  errorMessage.value = ''
  try {
    const response = await axios.get(`${API_BASE}/get_users.php`, { withCredentials: true })
    let userList: User[] = []

    if (response.data?.users && Array.isArray(response.data.users)) {
      userList = response.data.users
    } else if (Array.isArray(response.data)) {
      userList = response.data
    }

    users.value = userList
  } catch (error) {
    console.error('Error fetching users:', error)
    errorMessage.value = 'Failed to load users'
  } finally {
    isLoading.value = false
  }
}

function openAddModal() {
  newUser.value = {
    name: '',
    email: '',
    phone: '',
    password: '',
    role: 'user'
  }
  isAddModalOpen.value = true
}

async function confirmAddUser() {
  if (!newUser.value.name || !newUser.value.email || !newUser.value.phone || !newUser.value.password) {
    errorMessage.value = 'Please fill all user fields.'
    return
  }

  isLoading.value = true
  errorMessage.value = ''

  try {
    const response = await axios.post(`${API_BASE}/create_user.php`, {
      name: newUser.value.name,
      email: newUser.value.email,
      phone: newUser.value.phone,
      password: newUser.value.password,
      role: newUser.value.role
    }, { withCredentials: true })

    if (response.data?.success) {
      isAddModalOpen.value = false
      await fetchUsers()
    } else {
      errorMessage.value = response.data?.message || 'Failed to add user'
    }
  } catch (error) {
    console.error('Error adding user:', error)
    errorMessage.value = 'Failed to add user'
  } finally {
    isLoading.value = false
  }
}

function openViewModal(user: User) {
  userToView.value = user
  isViewModalOpen.value = true
}

function closeViewModal() {
  isViewModalOpen.value = false
  userToView.value = null
}

function openDeleteModal(user: User) {
  userToDelete.value = user
  isDeleteModalOpen.value = true
}

function closeDeleteModal() {
  isDeleteModalOpen.value = false
  userToDelete.value = null
}

async function confirmDeleteUser() {
  if (!userToDelete.value) return

  isLoading.value = true
  errorMessage.value = ''

  try {
    await axios.post(`${API_BASE}/delete_user.php`, {
      id: userToDelete.value.id
    })
    closeDeleteModal()
    await fetchUsers()
  } catch (error) {
    console.error('Error deleting user:', error)
    errorMessage.value = 'Failed to delete user'
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

watch([search, pageSize, filteredUsers], () => {
  if (currentPage.value > pageCount.value) currentPage.value = pageCount.value
  if (currentPage.value < 1) currentPage.value = 1
})

onMounted(() => {
  fetchUsers()
})

const activeMenu = ref<number | null>(null)

function toggleMenu(id: number) {
  activeMenu.value =
    activeMenu.value === id ? null : id
}

</script>

<style scoped></style>
