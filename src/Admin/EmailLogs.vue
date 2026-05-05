<template>
  <div
    class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 md:px-3 py-4 sm:px-6 sm:py-8 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">
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
              <i class="fa-solid fa-envelope-open-text text-lg sm:text-2xl dark:text-white"></i>
            </div>

            <div class="min-w-0">
              <h1 class="text-lg sm:text-2xl lg:text-3xl font-bold tracking-tight truncate">
                Email Logs
              </h1>
              <p class="mt-1 text-xs sm:text-sm dark:text-indigo-100 leading-snug">
                View all sent email notifications (Development Mode)
              </p>
            </div>
          </div>

          <!-- right section -->
          <div class="flex w-full sm:w-auto gap-2">
            <button
              class="w-full sm:w-auto group inline-flex justify-center items-center gap-2 sm:gap-3 rounded-xl bg-white px-4 sm:px-6 py-2.5 sm:py-3 text-sm sm:text-base font-semibold text-indigo-600 shadow-lg transition-all hover:bg-indigo-50 hover:shadow-xl disabled:opacity-50"
              @click="fetchEmailLogs" :disabled="isLoading">
              <i :class="isLoading ? 'fa-solid fa-spinner fa-spin' : 'fa-solid fa-rotate-right'"
                class="text-base sm:text-lg transition-transform"></i>
              <span class="whitespace-nowrap">{{ isLoading ? 'Loading...' : 'Refresh' }}</span>
            </button>
          </div>
        </div>

        <!-- loading spinner -->
        <div v-if="isLoading"
          class="absolute top-3 right-3 h-5 w-5 sm:h-6 sm:w-6 animate-spin rounded-full border-2 border-white/30 border-t-white">
        </div>
      </div>

      <!-- Stats Card -->
      <div
        class="rounded-md bg-white p-4 shadow-lg border border-slate-200/60 dark:bg-slate-950 dark:border-slate-700 sm:rounded-md sm:p-6">
        <div class="flex items-center justify-between gap-3">
          <div class="min-w-0">
            <p class="text-xs font-medium text-slate-600 dark:text-slate-400 sm:text-sm">Total Emails Sent</p>
            <p class="text-xl font-bold text-slate-900 dark:text-slate-100 sm:text-2xl mt-1">{{ totalCount }}</p>
          </div>
          <div
            class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30 sm:h-12 sm:w-12 sm:rounded-md">
            <i class="fa-solid fa-envelope-open-text text-base text-blue-600 dark:text-blue-400 sm:text-xl"></i>
          </div>
        </div>
      </div>

      <!-- Alert Messages -->
      <div v-if="errorMessage" class="animate-fade-in">
        <div
          class="rounded-xl bg-gradient-to-r from-red-500 to-rose-500 p-4 text-white shadow-lg dark:from-red-900 dark:to-rose-900">
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

      <!-- Email Logs Table Card -->
      <div
        class="rounded-2xl bg-white p-8 shadow-xl border border-slate-200/60 dark:bg-slate-950 dark:border-slate-700">
        <!-- Search and Filters -->
        <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
          <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="relative">
              <i
                class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500"></i>
              <input v-model="search" type="text" placeholder="Search by recipient or subject..."
                class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-10 py-3 outline-none transition-all focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:placeholder-slate-400 dark:focus:border-indigo-500 lg:w-80" />
            </div>
            <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
              <i class="fa-solid fa-list"></i>
              <span>{{ filteredEmails.length }} of {{ emailLogs.length }} emails</span>
            </div>
          </div>
        </div>

        <!-- Email Logs Table -->
        <div
          class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm dark:border-slate-700 sm:rounded-xl">
          <!-- Desktop Table View -->
          <div class="hidden sm:block overflow-x-auto">
            <table class="w-full table-auto text-left text-sm">
              <thead class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900">
                <tr>
                  <th
                    class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                    Recipient</th>
                  <th
                    class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                    Subject</th>
                  <th
                    class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                    Type</th>
                  <th
                    class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                    Sent Date</th>
                  <th
                    class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                    Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                <tr v-for="email in paginatedEmails" :key="email.timestamp"
                  class="group transition-all hover:bg-gradient-to-r hover:from-indigo-50 hover:to-blue-50 dark:hover:from-slate-800 dark:hover:to-slate-800">
                  <td class="px-4 py-3">
                    <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ email.to }}</div>
                  </td>
                  <td class="px-4 py-3">
                    <div class="text-sm text-slate-700 dark:text-slate-300 max-w-xs truncate">{{ email.subject }}</div>
                  </td>
                  <td class="px-4 py-3">
                    <span :class="getEmailTypeClass(email.type)"
                      class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium">
                      <i :class="getEmailTypeIcon(email.type)"></i>
                      {{ email.type }}
                    </span>
                  </td>
                  <td class="px-4 py-3">
                    <div class="text-sm text-slate-600 dark:text-slate-400">{{ formatDate(email.timestamp) }}</div>
                  </td>
                  <td class="px-4 py-3">
                    <button @click="openEmailModal(email)"
                      class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2.5 py-1.5 text-xs font-medium text-slate-700 transition-all hover:bg-slate-200 hover:shadow-sm dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
                      <i class="fa-solid fa-eye"></i>
                      View
                    </button>
                  </td>
                </tr>
                <tr v-if="paginatedEmails.length === 0">
                  <td class="px-4 py-8 text-center text-slate-500 dark:bg-gray-800 dark:text-slate-400" colspan="5">
                    <div class="flex flex-col items-center gap-2">
                      <i class="fa-solid fa-envelope text-3xl text-slate-300 dark:text-slate-600"></i>
                      <p class="text-sm">No email logs found</p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Mobile Card View -->
          <div class="sm:hidden p-3 space-y-3">
            <div v-if="paginatedEmails.length === 0" class="text-center py-6 text-slate-500 dark:text-slate-400">
              <i class="fa-solid fa-envelope text-2xl text-slate-300 dark:text-slate-600 mb-2"></i>
              <p class="text-sm">No email logs found</p>
            </div>
            <div v-for="email in paginatedEmails" :key="email.timestamp"
              class="rounded-lg border border-slate-200 bg-white p-3 shadow-sm dark:border-slate-700 dark:bg-slate-900">
              <div class="flex items-start justify-between gap-2 mb-2">
                <span :class="getEmailTypeClass(email.type)"
                  class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium">
                  <i :class="getEmailTypeIcon(email.type)"></i>
                  {{ email.type }}
                </span>
              </div>
              <h4 class="font-bold text-slate-900 dark:text-slate-100 text-sm">{{ email.to }}</h4>
              <p class="text-sm text-slate-700 dark:text-slate-300 mt-1 line-clamp-2">{{ email.subject }}</p>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ formatDate(email.timestamp) }}</p>
              <div class="flex gap-1.5 mt-2.5">
                <button @click="openEmailModal(email)"
                  class="flex-1 inline-flex items-center justify-center gap-1 rounded-md bg-slate-100 px-2 py-1.5 text-xs font-medium text-slate-700 transition-all hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300">
                  <i class="fa-solid fa-eye"></i>
                  View
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <div class="text-sm text-slate-600">
            <span class="font-medium text-slate-900">{{ filteredEmails.length }}</span> emails found
            <span class="text-slate-400">•</span>
            Showing <span class="font-medium text-slate-900">{{ startIndex + 1 }}</span> to
            <span class="font-medium text-slate-900">{{ endIndex }}</span> of
            <span class="font-medium text-slate-900">{{ filteredEmails.length }}</span> entries
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

  <!-- View Email Modal -->
  <div v-if="isEmailModalOpen"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="w-full max-w-2xl rounded-2xl bg-white shadow-2xl dark:bg-slate-950">
      <!-- Gradient Header -->
      <div
        class="bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500 p-6 rounded-t-2xl dark:from-blue-900 dark:via-indigo-900 dark:to-purple-900">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
              <i class="fa-solid fa-envelope-open-text text-2xl text-white"></i>
            </div>
            <div>
              <h2 class="text-2xl font-bold text-white">Email Details</h2>
              <p class="text-blue-100">View complete email information</p>
            </div>
          </div>
          <button
            class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 text-white transition hover:bg-white/30 backdrop-blur-sm"
            @click="closeEmailModal">
            <i class="fa-solid fa-times text-xl"></i>
          </button>
        </div>
      </div>

      <!-- Modal Content -->
      <div class="p-6 dark:bg-slate-900 max-h-96 overflow-y-auto">
        <div class="space-y-6">
          <!-- Email Information -->
          <div
            class="rounded-xl bg-gradient-to-br from-slate-50 to-slate-100 p-4 dark:from-slate-800 dark:to-slate-900">
            <h3 class="mb-3 text-lg font-semibold text-slate-800 dark:text-slate-200">Email Information</h3>
            <div class="space-y-2">
              <div class="flex justify-between items-start py-1">
                <span class="text-sm font-medium text-slate-600 dark:text-slate-400">To:</span>
                <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ emailToView?.to }}</span>
              </div>
              <div class="flex justify-between items-start py-1">
                <span class="text-sm font-medium text-slate-600 dark:text-slate-400">Subject:</span>
                <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ emailToView?.subject }}</span>
              </div>
              <div class="flex justify-between items-start py-1">
                <span class="text-sm font-medium text-slate-600 dark:text-slate-400">Type:</span>
                <span :class="getEmailTypeClass(emailToView?.type)"
                  class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium">
                  <i :class="getEmailTypeIcon(emailToView?.type)"></i>
                  {{ emailToView?.type }}
                </span>
              </div>
              <div class="flex justify-between items-start py-1">
                <span class="text-sm font-medium text-slate-600 dark:text-slate-400">Date:</span>
                <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{
                  formatDate(emailToView?.timestamp) }}</span>
              </div>
            </div>
          </div>

          <!-- Email Body -->
          <div class="rounded-xl bg-gradient-to-br from-indigo-50 to-blue-50 p-4 dark:from-slate-800 dark:to-slate-900">
            <h3 class="mb-3 text-lg font-semibold text-slate-800 dark:text-slate-200">Email Body</h3>
            <div
              class="bg-white/70 dark:bg-slate-900/70 rounded-lg p-4 text-sm text-slate-700 dark:text-slate-300 whitespace-pre-wrap leading-relaxed break-words">
              {{ emailToView?.body }}
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 flex justify-end gap-4">
          <button
            class="rounded-xl border border-slate-300 bg-white px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 hover:shadow-md inline-flex items-center gap-2 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300"
            @click="closeEmailModal">
            <i class="fa-solid fa-times"></i>
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import axios from 'axios'
import { computed, onMounted, ref } from 'vue'

type EmailLog = {
  to: string
  subject: string
  type: string
  body: string
  timestamp: string
}

const API_BASE = 'http://localhost/Eshop/Backend/api/contact'

const emailLogs = ref<EmailLog[]>([])
const totalCount = ref(0)
const isLoading = ref(false)
const errorMessage = ref('')

const search = ref('')
const pageSize = ref(10)
const currentPage = ref(1)

const isEmailModalOpen = ref(false)
const emailToView = ref<EmailLog | null>(null)

const filteredEmails = computed(() => {
  let filtered = emailLogs.value

  // Search filter
  if (search.value) {
    const term = search.value.toLowerCase()
    filtered = filtered.filter(e =>
      e.to.toLowerCase().includes(term) ||
      e.subject.toLowerCase().includes(term) ||
      e.type.toLowerCase().includes(term)
    )
  }

  return filtered
})

const pageCount = computed(() => Math.max(1, Math.ceil(filteredEmails.value.length / pageSize.value)))
const startIndex = computed(() => (currentPage.value - 1) * pageSize.value)
const endIndex = computed(() => Math.min(startIndex.value + pageSize.value, filteredEmails.value.length))
const paginatedEmails = computed(() => filteredEmails.value.slice(startIndex.value, endIndex.value))

async function fetchEmailLogs() {
  isLoading.value = true
  errorMessage.value = ''
  try {
    const response = await axios.get(`${API_BASE}/get_email_logs.php`, {
      withCredentials: true
    })

    if (response.data?.success) {
      emailLogs.value = response.data.emails || []
      totalCount.value = response.data.total || 0
    } else {
      throw new Error(response.data?.message || 'Failed to load email logs')
    }
  } catch (error) {
    console.error('Error fetching email logs:', error)
    errorMessage.value = 'Failed to load email logs. Make sure you\'re in development mode.'
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

function getEmailTypeIcon(type: string | undefined): string {
  const typeMap: Record<string, string> = {
    'reply': 'fa-solid fa-reply',
    'confirmation': 'fa-solid fa-check-circle',
    'notification': 'fa-solid fa-bell',
    'order': 'fa-solid fa-shopping-bag',
    'welcome': 'fa-solid fa-handshake'
  }
  return typeMap[type?.toLowerCase() || ''] || 'fa-solid fa-envelope'
}

function getEmailTypeClass(type: string | undefined): string {
  const typeMap: Record<string, string> = {
    'reply': 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
    'confirmation': 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    'notification': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    'order': 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
    'welcome': 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400'
  }
  return typeMap[type?.toLowerCase() || ''] || 'bg-slate-100 text-slate-800 dark:bg-slate-900/30 dark:text-slate-400'
}

function openEmailModal(email: EmailLog) {
  emailToView.value = email
  isEmailModalOpen.value = true
}

function closeEmailModal() {
  isEmailModalOpen.value = false
  emailToView.value = null
}

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
  fetchEmailLogs()
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
