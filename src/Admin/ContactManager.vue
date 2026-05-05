<template>
  <div
    class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 md:px-3 md:py-4 py-2 sm:px-6 sm:py-8 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">
    <div class="mx-auto max-w-7xl space-y-8">
      <!-- Header Section -->
      <div
        class="relative overflow-hidden rounded-2xl p-4 sm:p-6 lg:p-8 text-gray-700 dark:text-white border dark:bg-gray-900 shadow-sm">
        <!-- overlay -->
        <div class="absolute inset-0 bg-black/2"></div>

        <!-- content -->
        <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <!-- left section -->
          <div class="flex items-start sm:items-center gap-3 sm:gap-4">
            <div
              class="flex h-10 w-10 sm:h-14 sm:w-14 items-center justify-center rounded-xl sm:rounded-2xl bg-white/20 backdrop-blur-sm flex-shrink-0">
              <i class="fa-solid fa-envelope  text-lg sm:text-2xl dark:text-white"></i>
            </div>

            <div class="min-w-0">
              <h1 class="text-lg sm:text-2xl lg:text-3xl font-bold tracking-tight truncate">
                Contact Messages
              </h1>
              <p class="mt-1 text-xs sm:text-sm dark:text-indigo-100 leading-snug">
                Manage customer inquiries and messages
              </p>
            </div>
          </div>

          <!-- right section -->
          <div class="flex w-full sm:w-auto">
            <button
              class="w-full sm:w-auto group inline-flex justify-center items-center gap-2 sm:gap-3 rounded-xl bg-white px-4 sm:px-6 py-2.5 sm:py-3 text-sm sm:text-base font-semibold text-indigo-600 shadow-lg transition-all hover:bg-indigo-50 hover:shadow-xl disabled:opacity-50"
              @click="markAllAsRead" :disabled="isLoading || unreadCount === 0">
              <i class="fa-solid fa-check-double text-base sm:text-lg transition-transform group-hover:scale-110"></i>
              <span class="whitespace-nowrap">Mark All Read</span>
            </button>
          </div>
        </div>

        <!-- loading spinner -->
        <div v-if="isLoading"
          class="absolute top-3 right-3 h-5 w-5 sm:h-6 sm:w-6 animate-spin rounded-full border-2 border-white/30 border-t-white">
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 gap-3 sm:grid-cols-3 sm:gap-6">
        <div
          class="rounded-md bg-white p-4 shadow-lg border border-slate-200/60 dark:bg-slate-950 dark:border-slate-700 sm:rounded-md sm:p-6">
          <div class="flex items-center justify-between gap-3">
            <div class="min-w-0">
              <p class="text-xs font-medium text-slate-600 dark:text-slate-400 sm:text-sm">Total Messages</p>
              <p class="text-xl font-bold text-slate-900 dark:text-slate-100 sm:text-2xl mt-1">{{ totalCount }}</p>
            </div>
            <div
              class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30 sm:h-12 sm:w-12 sm:rounded-md">
              <i class="fa-solid fa-envelope text-base text-blue-600 dark:text-blue-400 sm:text-xl"></i>
            </div>
          </div>
        </div>
        <div
          class="rounded-md bg-white p-4 shadow-lg border border-slate-200/60 dark:bg-slate-950 dark:border-slate-700 sm:rounded-md sm:p-6">
          <div class="flex items-center justify-between gap-3">
            <div class="min-w-0">
              <p class="text-xs font-medium text-slate-600 dark:text-slate-400 sm:text-sm">Unread Messages</p>
              <p class="text-xl font-bold text-red-600 dark:text-red-400 sm:text-2xl mt-1">{{ unreadCount }}</p>
            </div>
            <div
              class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-red-100 dark:bg-red-900/30 sm:h-12 sm:w-12 sm:rounded-md">
              <i class="fa-solid fa-envelope-open text-base text-red-600 dark:text-red-400 sm:text-xl"></i>
            </div>
          </div>
        </div>
        <div
          class="rounded-md bg-white p-4 shadow-lg border border-slate-200/60 dark:bg-slate-950 dark:border-slate-700 sm:rounded-md sm:p-6">
          <div class="flex items-center justify-between gap-3">
            <div class="min-w-0">
              <p class="text-xs font-medium text-slate-600 dark:text-slate-400 sm:text-sm">Read Messages</p>
              <p class="text-xl font-bold text-green-600 dark:text-green-400 sm:text-2xl mt-1">{{ totalCount -
                unreadCount }}</p>
            </div>
            <div
              class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900/30 sm:h-12 sm:w-12 sm:rounded-md">
              <i class="fa-solid fa-check-circle text-base text-green-600 dark:text-green-400 sm:text-xl"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Alert Messages -->
      <div v-if="errorMessage" class="animate-fade-in">
        <div
          class="rounded-md bg-gradient-to-r from-red-500 to-rose-500 p-4 text-white shadow-lg dark:from-red-900 dark:to-rose-900">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <i class="fa-solid fa-exclamation-triangle text-xl"></i>
              <span class="font-medium">{{ errorMessage }}</span>
            </div>
            <button @click="errorMessage = ''" class="text-red-100 hover:text-white transition-colors">
              <i class="fa-solid fa-times"></i>
            </button>
          </div>
        </div>
      </div>

      <div v-if="successMessage" class="animate-fade-in">
        <div
          class="rounded-md bg-gradient-to-r from-green-500 to-emerald-500 p-4 text-white shadow-lg dark:from-green-900 dark:to-emerald-900">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <i class="fa-solid fa-check-circle text-xl"></i>
              <span class="font-medium">{{ successMessage }}</span>
            </div>
            <button @click="successMessage = ''" class="text-green-100 hover:text-white transition-colors">
              <i class="fa-solid fa-times"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Messages Table Card -->
      <div
        class="rounded-md bg-white md:p-8 p-2 shadow-xl border border-slate-200/60 dark:bg-slate-950 dark:border-slate-700">
        <!-- Search and Filters -->
        <div class="mb-5 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
          <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="relative">
              <i
                class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500"></i>
              <input v-model="search" type="text" placeholder="Search messages..."
                class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-10 py-2 outline-none transition-all focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:placeholder-slate-400 dark:focus:border-indigo-500 lg:w-80" />
            </div>
            <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
              <i class="fa-solid fa-list"></i>
              <span>{{ filteredMessages.length }} of {{ messages.length }} messages</span>
            </div>
          </div>

          <div class="flex items-center gap-3">
            <label class="text-sm font-medium text-slate-700 dark:text-slate-300" for="statusFilter">Status:</label>
            <select id="statusFilter" v-model="statusFilter"
              class="rounded-lg border border-slate-200 bg-white px-3 py-2 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
              <option value="all">All Messages</option>
              <option value="unread">Unread Only</option>
              <option value="read">Read Only</option>
            </select>
          </div>
        </div>

        <!-- Messages Table -->
        <!-- Mobile Table View (responsive with header) -->
<div class=" overflow-x-auto">
  <table class="w-full md:min-w-[700px] text-left text-sm">

    <!-- Header -->
    <thead class="bg-slate-100 dark:bg-slate-800">
      <tr>
        <th class="md:px-3 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300">Status</th>
        <th class="md:px-3 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300">Name</th>
        <th class="hidden md:flex md:px-3 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300">Email</th>
        <th class="px-3 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300">Message</th>
        <th class="hidden md:flex md:px-3 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300">Date</th>
        <th class="md:px-3 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300">Actions</th>
      </tr>
    </thead>

    <!-- Body -->
    <tbody class="divide-y divide-slate-200 dark:divide-slate-700 dark:bg-slate-900">

      <!-- Empty state -->
      <tr v-if="paginatedMessages.length === 0">
        <td colspan="6" class="px-3 py-6 text-center text-slate-500">
          No messages found
        </td>
      </tr>

      <!-- Rows -->
      <tr
        v-for="message in paginatedMessages"
        :key="message.id"
        class="hover:bg-slate-50 dark:hover:bg-slate-800 transition"
      >

        <!-- Status -->
        <td class="px-3 py-2">
          <span
            :class="message.is_read
              ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
              : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'"
            class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium"
          >
            <i :class="message.is_read ? 'fa-solid fa-check-circle' : 'fa-solid fa-envelope'"></i>
            {{ message.is_read ? 'Read' : 'Unread' }}
          </span>
        </td>

        <!-- Name -->
        <td class="md:px-3 py-2 font-semibold text-slate-900 dark:text-slate-100">
          {{ message.name }}
        </td>

        <!-- Email -->
        <td class="hidden md:flex md:px-3 py-2 text-slate-600 dark:text-slate-400">
          {{ message.email }}
        </td>

        <!-- Message -->
        <td class="px-3 py-2 text-slate-700 dark:text-slate-300 max-w-[200px] truncate">
          {{ message.message }}
        </td>

        <!-- Date -->
        <td class="hidden md:flex px-3 py-2 text-slate-500 dark:text-slate-400 whitespace-nowrap">
          {{ formatDate(message.created_at) }}
        </td>

        <!-- Actions (3-dot dropdown) -->
        <td class="px-3 py-2 relative overflow-visible">

  <details class="relative inline-block">

    <!-- Trigger -->
    <summary
      class="cursor-pointer list-none block px-2 py-1 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 rounded"
    >
      <i class="fa-solid fa-ellipsis-vertical"></i>
    </summary>

    <!-- Dropdown -->
    <div
      class="absolute right-0 mt-2 w-32 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded shadow-lg z-50"
    >

      <button
        @click="openViewModal(message)"
        class="w-full text-left px-3 py-2 text-xs hover:bg-slate-100 dark:hover:bg-slate-800"
      >
        View
      </button>

      <button
        v-if="!message.is_read"
        @click="markAsRead(message.id)"
        class="w-full text-left px-3 py-2 text-xs hover:bg-slate-100 dark:hover:bg-slate-800 text-green-600"
      >
        Mark as Read
      </button>

      <button
        @click="openDeleteModal(message)"
        class="w-full text-left px-3 py-2 text-xs hover:bg-slate-100 dark:hover:bg-slate-800 text-red-600"
      >
        Delete
      </button>

    </div>

  </details>

</td>

      </tr>
    </tbody>
  </table>
</div>

        <!-- Pagination -->
        <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <div class="text-sm text-slate-600">
            <span class="font-medium text-slate-900">{{ filteredMessages.length }}</span> messages found
            <span class="text-slate-400">•</span>
            Showing <span class="font-medium text-slate-900">{{ startIndex + 1 }}</span> to
            <span class="font-medium text-slate-900">{{ endIndex }}</span> of
            <span class="font-medium text-slate-900">{{ filteredMessages.length }}</span> entries
          </div>

          <div class="flex items-center gap-2">
            <button
              class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition-all hover:border-slate-300 hover:bg-slate-50 hover:shadow-sm disabled:cursor-not-allowed disabled:opacity-50"
              :disabled="currentPage === 1" @click="prevPage">
              <i class="fa-solid fa-chevron-left"></i>
              Previous
            </button>

            <div class="flex items-center gap-1">
              <button v-for="page in pageCount" :key="page"
                class="rounded-lg px-3 py-2 text-sm font-medium transition-all" :class="page === currentPage
                  ? 'bg-indigo-600 text-white shadow-lg'
                  : 'border border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50'"
                @click="currentPage = page">
                {{ page }}
              </button>
            </div>

            <button
              class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition-all hover:border-slate-300 hover:bg-slate-50 hover:shadow-sm disabled:cursor-not-allowed disabled:opacity-50"
              :disabled="currentPage === pageCount" @click="nextPage">
              Next
              <i class="fa-solid fa-chevron-right"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- View Message Modal -->
  <div v-if="isViewModalOpen"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="w-full max-w-2xl rounded-md bg-white shadow-2xl dark:bg-slate-950">
      <!-- Gradient Header -->
      <div
        class="bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500 p-3 rounded-md dark:from-blue-900 dark:via-indigo-900 dark:to-purple-900">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
              <i class="fa-solid fa-envelope-open text-2xl text-white"></i>
            </div>
            <div>
              <h2 class="md:text-2xl font-bold text-white">Message Details</h2>
              <p class="md:text-blue-100">View complete message information</p>
            </div>
          </div>
          <button
            class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 text-white transition hover:bg-white/30 backdrop-blur-sm"
            @click="closeViewModal">
            <i class="fa-solid fa-times text-xl"></i>
          </button>
        </div>
      </div>

      <!-- Modal Content -->
      <div class="p-4 dark:bg-slate-900">
        <div class="space-y-6">
          <!-- Message Information -->
          <div class="grid gap-4 md:grid-cols-2">
            <div
              class="rounded-md bg-gradient-to-br from-slate-50 to-slate-100 p-4 dark:from-slate-800 dark:to-slate-900">
              <h3 class="mb-3 text-lg font-semibold text-slate-800 dark:text-slate-200">Sender Information</h3>
              <div class="space-y-2">
                <div class="flex justify-between items-center py-1">
                  <span class="text-sm font-medium text-slate-600 dark:text-slate-400">Name:</span>
                  <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ messageToView?.name
                    }}</span>
                </div>
                <div class="flex justify-between items-center py-1">
                  <span class="text-sm font-medium text-slate-600 dark:text-slate-400">Email:</span>
                  <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ messageToView?.email
                    }}</span>
                </div>
                <div class="flex justify-between items-center py-1">
                  <span class="text-sm font-medium text-slate-600 dark:text-slate-400">Date:</span>
                  <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{
                    formatDate(messageToView?.created_at) }}</span>
                </div>
                <div class="flex justify-between items-center py-1">
                  <span class="text-sm font-medium text-slate-600">Status:</span>
                  <span :class="messageToView?.is_read ? 'text-green-600' : 'text-red-600'"
                    class="text-sm font-semibold">
                    {{ messageToView?.is_read ? 'Read' : 'Unread' }}
                  </span>
                </div>
              </div>
            </div>

            <div class="rounded-md  dark:bg-gray-900 p-4">
              <h3 class="mb-3 text-lg font-semibold text-slate-800 dark:text-white">
                Message Content
              </h3>

              <div class="bg-white/70 dark:bg-gray-900/70 rounded-lg p-4 max-h-48 overflow-y-auto">
                <p class="text-sm text-slate-700 dark:text-zinc-300 whitespace-pre-wrap leading-relaxed">
                  {{ messageToView?.message }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 flex justify-end gap-4">
          <button
            class="rounded-xl border border-slate-300 bg-white px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 hover:shadow-md inline-flex items-center gap-2"
            @click="closeViewModal">
            <i class="fa-solid fa-times"></i>
            Close
          </button>

          <button v-if="!messageToView?.is_read"
            class="rounded-xl bg-gradient-to-r from-green-500 to-emerald-500 px-6 py-3 text-sm font-semibold text-white transition hover:from-green-600 hover:to-emerald-600 hover:shadow-lg inline-flex items-center gap-2"
            @click="messageToView?.id && markAsRead(messageToView.id)">
            <i class="fa-solid fa-check"></i>
            Mark as Read
          </button>
        </div>
      </div>
    </div>
  </div>




  <!-- Delete Confirmation Modal -->
  <div v-if="isDeleteModalOpen"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl">
      <div class="bg-gradient-to-r from-red-500 via-rose-500 to-pink-500 p-6 rounded-t-2xl">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
              <i class="fa-solid fa-trash text-2xl text-white"></i>
            </div>
            <div>
              <h2 class="text-2xl font-bold text-white">Delete Message</h2>
              <p class="text-rose-100">Confirm deletion of this contact message.</p>
            </div>
          </div>
          <button
            class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 text-white transition hover:bg-white/30 backdrop-blur-sm"
            @click="closeDeleteModal">
            <i class="fa-solid fa-times text-xl"></i>
          </button>
        </div>
      </div>
      <div class="p-6">
        <div class="space-y-4">
          <p class="text-sm text-slate-600">Are you sure you want to permanently delete the message from <strong>{{
            messageToDelete?.name }}</strong> (<span class="text-slate-700">{{ messageToDelete?.email }}</span>)?</p>
          <p class="text-sm text-slate-600">This action cannot be undone.</p>
        </div>
        <div class="mt-6 flex justify-end gap-3">
          <button
            class="rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
            @click="closeDeleteModal" :disabled="isDeletingMessage">
            Cancel
          </button>
          <button
            class="rounded-xl bg-gradient-to-r from-red-500 to-rose-500 px-5 py-3 text-sm font-semibold text-white transition hover:from-red-600 hover:to-rose-600 disabled:opacity-50 disabled:cursor-not-allowed"
            @click="confirmDeleteMessage" :disabled="isDeletingMessage">
            <i :class="isDeletingMessage ? 'fa-solid fa-spinner fa-spin' : 'fa-solid fa-trash'"></i>
            {{ isDeletingMessage ? 'Deleting...' : 'Delete Message' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import axios from 'axios'
import { computed, onMounted, ref } from 'vue'

type ContactMessage = {
  id: number
  user_id: number | null
  name: string
  email: string
  message: string
  is_read: number
  created_at: string
}

const API_BASE = 'http://localhost/Eshop/Backend/api/contact'

const messages = ref<ContactMessage[]>([])
const totalCount = ref(0)
const unreadCount = ref(0)
const isLoading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

const search = ref('')
const statusFilter = ref('all')
const pageSize = ref(10)
const currentPage = ref(1)

const isViewModalOpen = ref(false)
const messageToView = ref<ContactMessage | null>(null)


const isSendingReply = ref(false)
const isDeleteModalOpen = ref(false)
const messageToDelete = ref<ContactMessage | null>(null)
const isDeletingMessage = ref(false)



const filteredMessages = computed(() => {
  let filtered = messages.value

  // Status filter
  if (statusFilter.value === 'unread') {
    filtered = filtered.filter(m => m.is_read === 0)
  } else if (statusFilter.value === 'read') {
    filtered = filtered.filter(m => m.is_read === 1)
  }

  // Search filter
  if (search.value) {
    const term = search.value.toLowerCase()
    filtered = filtered.filter(m =>
      m.name.toLowerCase().includes(term) ||
      m.email.toLowerCase().includes(term) ||
      m.message.toLowerCase().includes(term)
    )
  }

  return filtered
})

const pageCount = computed(() => Math.max(1, Math.ceil(filteredMessages.value.length / pageSize.value)))
const startIndex = computed(() => (currentPage.value - 1) * pageSize.value)
const endIndex = computed(() => Math.min(startIndex.value + pageSize.value, filteredMessages.value.length))
const paginatedMessages = computed(() => filteredMessages.value.slice(startIndex.value, endIndex.value))

async function fetchMessages() {
  isLoading.value = true
  errorMessage.value = ''
  try {
    const response = await axios.get(`${API_BASE}/get_contacts.php`, {
      withCredentials: true
    })

    if (response.data?.success) {
      messages.value = response.data.contacts || []
      totalCount.value = response.data.total || 0
      unreadCount.value = response.data.unread || 0
    } else {
      throw new Error(response.data?.message || 'Failed to load messages')
    }
  } catch (error) {
    console.error('Error fetching messages:', error)
    errorMessage.value = 'Failed to load contact messages'
  } finally {
    isLoading.value = false
  }
}

async function markAsRead(messageId: number) {
  isLoading.value = true
  errorMessage.value = ''
  try {
    const response = await axios.post(`${API_BASE}/mark_read.php`, {
      id: messageId
    }, {
      withCredentials: true,
      headers: {
        'Content-Type': 'application/json'
      }
    })

    if (response.data?.success) {
      successMessage.value = 'Message marked as read'
      await fetchMessages() // Refresh the list
      if (messageToView.value?.id === messageId) {
        messageToView.value.is_read = 1
      }
    } else {
      throw new Error(response.data?.message || 'Failed to mark message as read')
    }
  } catch (error) {
    console.error('Error marking message as read:', error)
    errorMessage.value = 'Failed to mark message as read'
  } finally {
    isLoading.value = false
  }
}

async function markAllAsRead() {
  isLoading.value = true
  errorMessage.value = ''
  try {
    const response = await axios.post(`${API_BASE}/mark_read.php`, {}, {
      withCredentials: true,
      headers: {
        'Content-Type': 'application/json'
      }
    })

    if (response.data?.success) {
      successMessage.value = 'All messages marked as read'
      await fetchMessages() // Refresh the list
    } else {
      throw new Error(response.data?.message || 'Failed to mark all messages as read')
    }
  } catch (error) {
    console.error('Error marking all messages as read:', error)
    errorMessage.value = 'Failed to mark all messages as read'
  } finally {
    isLoading.value = false
  }
}

function formatDate(dateString: string | undefined): string {
  if (!dateString) return 'N/A'
  try {
    const date = new Date(dateString)
    return date.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  } catch {
    return dateString
  }
}

function openViewModal(message: ContactMessage) {
  messageToView.value = message
  isViewModalOpen.value = true
}

function closeViewModal() {
  isViewModalOpen.value = false
  messageToView.value = null
}



function openDeleteModal(message: ContactMessage) {
  messageToDelete.value = message
  isDeleteModalOpen.value = true
}

function closeDeleteModal() {
  isDeleteModalOpen.value = false
  messageToDelete.value = null
}

async function confirmDeleteMessage() {
  if (!messageToDelete.value) {
    return
  }

  isDeletingMessage.value = true
  errorMessage.value = ''
  try {
    const response = await axios.post(`${API_BASE}/delete_contact.php`, {
      id: messageToDelete.value.id
    }, {
      withCredentials: true,
      headers: {
        'Content-Type': 'application/json'
      }
    })

    if (response.data?.success) {
      successMessage.value = 'Message deleted successfully'
      await fetchMessages()
      closeDeleteModal()
    } else {
      throw new Error(response.data?.message || 'Failed to delete message')
    }
  } catch (error) {
    console.error('Error deleting message:', error)
    errorMessage.value = 'Failed to delete message'
  } finally {
    isDeletingMessage.value = false
  }
}





isSendingReply.value = true
errorMessage.value = ''





function prevPage() {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

function nextPage() {
  if (currentPage.value < pageCount.value) {
    currentPage.value++
  }
}

onMounted(() => {
  fetchMessages()
})
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
