<template>
  <div class="space-y-4 md:px-3 py-4 sm:space-y-6 sm:px-6 sm:py-8">
    <!-- Header -->
    <header
      class="flex flex-col gap-3 rounded-lg border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-950 sm:flex-row sm:items-center sm:justify-between sm:rounded-xl sm:p-5">
      <div class="flex items-center gap-3">
        <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100 sm:text-2xl">Orders</h1>
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

    <!-- Table Card -->
    <section
      class="rounded-lg border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-950 sm:rounded-xl">
      <!-- Search -->
      <div
        class="flex flex-col gap-3 border-b border-slate-200 p-3 dark:border-slate-700 sm:flex-row sm:items-center sm:justify-between sm:p-4">
        <!-- Search Input with Icon -->
        <div class="relative w-full sm:w-64">
          <i
            class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500"></i>

          <input v-model="search" type="text" placeholder="Search..." class="w-full rounded-lg border border-slate-300 pl-10 pr-3 py-2 text-sm outline-none
             focus:border-blue-500 focus:ring-2 focus:ring-blue-200
             dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100" />
        </div>

        <!-- Count -->
        <div class="text-xs text-slate-500 dark:text-slate-400 sm:text-sm">
          {{ filteredOrders.length }} of {{ orders.length }}
        </div>
      </div>

      <!-- Desktop Table -->
      <div class="hidden sm:block overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead class="bg-yellow-400 text-slate-600 dark:bg-slate-800 dark:text-slate-300">
            <tr>
              <th class="px-4 py-3">Order ID</th>
              <th class="px-4 py-3">Customer</th>
              <th class="px-4 py-3">Items</th>
              <th class="px-4 py-3">Total</th>
              <th class="px-4 py-3">Order Date</th>
              <th class="px-4 py-3">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in paginatedOrders" :key="order.id"
              class="border-t border-slate-100 hover:bg-yellow-100 dark:border-slate-700 dark:hover:bg-slate-800">
              <td class="px-4 py-3 font-semibold">{{ order.id }}</td>
              <td class="px-4 py-3 text-yellow-400 font-semibold">{{ order.user_name || 'Customer' }}</td>
              <td class="px-4 py-3 text-slate-700 font-semibold dark:text-white">{{(order.items || []).reduce((sum,
                item) => sum + (item.quantity ||
                  0), 0)}}</td>
              <td class="px-4 py-3 font-semibold text-slate-800 dark:text-white">${{
                parseFloat(String(order.total_amount ||
                  0)).toFixed(2) }}</td>
              <td class="px-4 py-3">{{ formatDate(order.created_at) }}</td>
              <td class="px-4 py-3 flex gap-2">
                <button @click="openViewModal(order)"
                  class="inline-flex items-center gap-1 rounded-md bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700">
                  <i class="fa-solid fa-eye"></i>
                  View
                </button>
                <button @click="openDeleteModal(order)"
                  class="inline-flex items-center gap-1 rounded-md bg-rose-500 px-3 py-2 text-xs font-semibold text-white hover:bg-rose-600">
                  <i class="fa-solid fa-trash"></i>
                  Delete
                </button>
              </td>
            </tr>
            <tr v-if="filteredOrders.length === 0">
              <td class="px-4 py-6 text-center text-slate-500 dark:text-slate-400" colspan="6">No orders found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Mobile Cards -->
      <!-- Mobile: Hidden buttons → Click 3 dots → Show View/Delete -->

      <div class="sm:hidden">
        <div
          class="grid grid-cols-12 gap-2 px-2 py-2 text-[11px] uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
          <div class="col-span-2 font-semibold">ID</div>
          <div class="col-span-4 font-semibold">Name</div>
          <div class="col-span-2 text-center font-semibold">Items</div>
          <div class="col-span-2 text-center font-semibold">Total</div>
          <div class="col-span-2 text-right font-semibold">Actions</div>
        </div>

        <div v-for="order in paginatedOrders" :key="order.id"
          class="border-b border-slate-200 px-2 py-2 dark:border-slate-700">
          <div class="grid grid-cols-12 items-center gap-2">

            <!-- Order ID -->
            <div class="col-span-2">
              <p class="text-sm font-semibold text-slate-900 dark:text-white">
                {{ order.id }}.
              </p>
            </div>

            <!-- Customer -->
            <div class="col-span-4 min-w-0">
              <p class="truncate text-sm font-medium text-slate-800 dark:text-slate-100">
                {{ order.user_name || 'Customer' }}
              </p>
              <p class="text-[11px] text-slate-500 dark:text-slate-400">
                {{ formatDate(order.created_at) }}
              </p>
            </div>

            <!-- Items -->
            <div class="col-span-2 text-center">
              <p class="text-xs text-slate-600 dark:text-slate-300">
                {{
                  (order.items || []).reduce(
                    (sum, item) => sum + (item.quantity || 0),
                    0
                  )
                }}
              </p>
            </div>

            <!-- Total -->
            <div class="col-span-2 text-center">
              <p class="text-sm font-semibold text-emerald-600">
                ${{ parseFloat(String(order.total_amount || 0)).toFixed(2) }}
              </p>
            </div>

            <!-- 3 Dot Button -->
            <div class="col-span-2 text-right relative">
              <button type="button" @click.stop="toggleOrderActions(order.id)"
                class="h-8 w-8 rounded-md bg-slate-100 text-slate-700 shadow-sm transition hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-100">
                <i :class="expandedOrderId === order.id
                  ? 'fa-solid fa-chevron-up'
                  : 'fa-solid fa-ellipsis-vertical'
                  " class="text-xs"></i>
              </button>

              <!-- Action Popup -->
              <div v-if="expandedOrderId === order.id"
                class="absolute right-0 top-full z-50 mt-2 min-w-[9rem] overflow-hidden rounded-lg border bg-white shadow-xl dark:border-slate-700 dark:bg-slate-900">
                <button type="button" @click="openViewModal(order)"
                  class="flex w-full items-center gap-2 px-4 py-2 text-sm text-left hover:bg-slate-100 dark:hover:bg-slate-800">
                  <i class="fa-solid fa-eye text-blue-500"></i>
                  View
                </button>

                <button type="button" @click="openDeleteModal(order)"
                  class="flex w-full items-center gap-2 px-4 py-2 text-sm text-left hover:bg-slate-100 dark:hover:bg-slate-800">
                  <i class="fa-solid fa-trash text-red-500"></i>
                  Delete
                </button>
              </div>
            </div>

          </div>
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

    <!-- Add Order Modal -->


    <!-- View Order Modal / Invoice -->
    <div v-if="isViewModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4 overflow-y-auto">
      <div
        class="w-full max-w-2xl rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200 dark:bg-slate-950 dark:ring-slate-700">
        <!-- Invoice Header -->
        <div
          class="border-b-2 border-slate-200 bg-gradient-to-r from-blue-50 to-blue-100 px-8 py-6 dark:border-slate-700 dark:from-blue-900/40 dark:to-blue-800/40">
          <div class="flex items-start justify-between">
            <div>
              <p class="text-3xl font-bold text-blue-900 dark:text-blue-100">INVOICE</p>
              <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Order {{ orderToView?.id }}</p>
            </div>
            <div class="text-right">
              <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">${{
                parseFloat(String(orderToView?.total_amount ||
                  0)).toFixed(2) }}</p>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ formatDate(orderToView?.created_at) }}</p>
            </div>
          </div>
        </div>

        <!-- Invoice Content -->
        <div class="px-8 py-6 space-y-6 dark:bg-slate-900">
          <!-- Customer Details -->
          <div class="grid grid-cols-2 gap-6">
            <div>
              <p class="text-xs uppercase font-semibold text-slate-500 dark:text-slate-400 mb-2">Bill To</p>
              <p class="text-lg font-bold text-slate-900 dark:text-slate-100">{{ orderToView?.user_name || 'Customer' }}
              </p>
            </div>
            <div class="text-right">
              <p class="text-xs uppercase font-semibold text-slate-500 dark:text-slate-400 mb-2">Delivery To</p>
              <p class="text-sm text-slate-700 dark:text-slate-300 whitespace-pre-wrap">{{ orderToView?.address || 'N/A'
              }}</p>
            </div>
          </div>

          <!-- Items Table -->
          <div class="border border-slate-300 rounded-lg overflow-hidden dark:border-slate-700">
            <table class="w-full text-sm">
              <thead class="bg-slate-100 dark:bg-slate-800">
                <tr>
                  <th class="md:px-4 px-2 py-3 text-left font-semibold text-slate-700 dark:text-slate-300">Product</th>
                  <th class="md:px-4  px-2 py-3 text-center font-semibold text-slate-700 dark:text-slate-300">Quantity</th>
                  <th class="md:px-4  px-2 py-3 text-right font-semibold text-slate-700 dark:text-slate-300">Price</th>
                  <th class="md:px-4  px-2 py-3 text-right font-semibold text-slate-700 dark:text-slate-300">Total</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, idx) in orderToView?.items || []" :key="idx"
                  class="border-t border-slate-200 hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800">
                  <td class="md:px-4 px-2 py-3">
                    <div class="flex items-center gap-1">
                      <img v-if="item.product_image" :src="item.product_image" :alt="item.product_name"
                        class="h-10 w-10 object-contain rounded" />
                      <span class="text-slate-900 dark:text-slate-200 font-medium">{{ item.product_name || 'Product'
                      }}</span>
                    </div>
                  </td>
                  <td class="md:px-4 px-2 py-3 text-center text-slate-700 dark:text-slate-400">{{ item.quantity }}</td>
                  <td class="md:px-4 px-2 py-3 text-right text-slate-700 dark:text-slate-400">${{ parseFloat(String(item.price
                    ||
                    0)).toFixed(2) }}
                  </td>
                  <td class="md:px-4 px-1 py-3 text-right font-semibold text-slate-900 dark:text-slate-200">${{ (item.quantity *
                    parseFloat(String(item.price || 0))).toFixed(2) }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Summary -->
          <div class="flex justify-end">
            <div class="w-64 space-y-2">
              <div class="flex justify-between py-2 border-b-2 border-slate-300 dark:border-slate-700">
                <span class="font-semibold text-slate-700 dark:text-slate-300">Total Amount:</span>
                <span class="font-bold text-2xl text-blue-600 dark:text-blue-400">${{
                  parseFloat(String(orderToView?.total_amount ||
                    0)).toFixed(2) }}</span>
              </div>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-4">Thank you for your order!</p>
            </div>
          </div>
        </div>

        <!-- Footer / Actions -->
        <div
          class="border-t border-slate-200 bg-slate-50 px-8 py-4 flex justify-end gap-3 rounded-b-2xl dark:border-slate-700 dark:bg-slate-800">
          <button
            class="rounded-lg border border-slate-300 px-5 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-700"
            @click="closeViewModal">
            <i class="fa-solid fa-times mr-1"></i>Close
          </button>
          <button
            class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-700 inline-flex items-center gap-2 dark:bg-blue-700 dark:hover:bg-blue-800"
            @click="printInvoice">
            <i class="fa-solid fa-print"></i>Print
          </button>
        </div>
      </div>
    </div>

    <!-- Delete Order Modal -->
    <div v-if="isDeleteModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
      <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
        <div class="mb-6 flex items-center justify-between border-b border-slate-200 pb-4">
          <h3 class="text-xl font-bold text-slate-900">Delete Order</h3>
          <button class="text-slate-400 transition hover:text-slate-600" @click="closeDeleteModal">✕</button>
        </div>

        <p class="text-slate-600">Are you sure you want to delete order {{ orderToDelete?.id }}? This action cannot be
          undone.</p>

        <div class="mt-6 flex justify-end gap-2">
          <button
            class="rounded-lg border border-slate-300 px-4 py-2 text-sm hover:bg-slate-100 inline-flex items-center gap-2"
            @click="closeDeleteModal">
            <i class="fa-solid fa-times"></i>
            Cancel
          </button>
          <button
            class="rounded-lg bg-rose-500 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-600 inline-flex items-center gap-2"
            @click="confirmDeleteOrder">
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

type Order = {
  id: number
  user_name?: string
  total_amount: number | string
  status: string
  created_at?: string
  address?: string
  items?: Array<{ product_name: string; quantity: number; price: number; product_image?: string }>
}

const API_BASE = 'http://localhost/Eshop/Backend/api/order'

const orders = ref<Order[]>([])
const isLoading = ref(false)
const errorMessage = ref('')
const search = ref('')
const pageSize = ref(10)
const currentPage = ref(1)
const expandedOrderId = ref<number | null>(null)

const isViewModalOpen = ref(false)
const isAddModalOpen = ref(false)
const isDeleteModalOpen = ref(false)

const orderToView = ref<Order | null>(null)
const orderToDelete = ref<Order | null>(null)

const newOrder = ref({
  user_id: '',
  total_amount: '',
  address: ''
})

const users = ref<Array<{ id: number; username: string }>>([])


const filteredOrders = computed(() => {
  if (!search.value) return orders.value
  const term = search.value.toLowerCase()
  return orders.value.filter((o) =>
    [o.id.toString(), o.user_name, o.status].some((field) => field?.toString().toLowerCase().includes(term))
  )
})

const pageCount = computed(() => Math.max(1, Math.ceil(filteredOrders.value.length / pageSize.value)))
const startIndex = computed(() => (currentPage.value - 1) * pageSize.value)
const endIndex = computed(() => Math.min(startIndex.value + pageSize.value, filteredOrders.value.length))
const paginatedOrders = computed(() => filteredOrders.value.slice(startIndex.value, endIndex.value))

const pageButtons = computed(() => {
  const buttons = []
  for (let i = 1; i <= pageCount.value && i <= 5; i++) {
    buttons.push(i)
  }
  return buttons
})

async function fetchOrders() {
  isLoading.value = true
  errorMessage.value = ''
  try {
    const response = await axios.get(`${API_BASE}/get_orders.php?all=1`, { withCredentials: true })
    let orderList: Order[] = []

    if (response.data?.orders && Array.isArray(response.data.orders)) {
      orderList = response.data.orders.map((o: any) => ({
        id: o.order_id ?? o.id,
        user_name: o.username || o.user_name || 'Customer',
        total_amount: o.total_amount,
        created_at: o.order_date || o.created_at,
        address: o.address,
        status: o.status || 'Pending',
        items: o.items || []
      }))
    } else if (Array.isArray(response.data)) {
      orderList = response.data.map((o: any) => ({
        id: o.order_id ?? o.id,
        user_name: o.username || o.user_name || 'Customer',
        total_amount: o.total_amount,
        created_at: o.order_date || o.created_at,
        address: o.address,
        status: o.status || 'Pending',
        items: o.items || []
      }))
    }

    orders.value = orderList
  } catch (error) {
    console.error('Error fetching orders:', error)
    errorMessage.value = 'Failed to load orders'
  } finally {
    isLoading.value = false
  }
}

async function fetchUsers() {
  try {
    const response = await axios.get('http://localhost/Eshop/Backend/api/auth/get_users.php', { withCredentials: true })
    if (response.data?.users && Array.isArray(response.data.users)) {
      users.value = response.data.users.map((u: { id: number; username: string }) => ({ id: u.id, username: u.username }))
    }
  } catch (error) {
    console.error('Error fetching users:', error)
  }
}

function openAddModal() {
  newOrder.value = {
    user_id: '',
    total_amount: '',
    address: ''
  }
  isAddModalOpen.value = true
}




function openViewModal(order: Order) {
  orderToView.value = order
  isViewModalOpen.value = true
}

function closeViewModal() {
  isViewModalOpen.value = false
  orderToView.value = null
}

function printInvoice() {
  window.print()
}

function toggleOrderActions(orderId: number) {
  expandedOrderId.value = expandedOrderId.value === orderId ? null : orderId
}

function openDeleteModal(order: Order) {
  orderToDelete.value = order
  isDeleteModalOpen.value = true
}

function closeDeleteModal() {
  isDeleteModalOpen.value = false
  orderToDelete.value = null
}

async function confirmDeleteOrder() {
  if (!orderToDelete.value) return

  isLoading.value = true
  errorMessage.value = ''

  try {
    await axios.post(`${API_BASE}/delete_order.php`, {
      id: orderToDelete.value.id
    }, { withCredentials: true })
    closeDeleteModal()
    await fetchOrders()
  } catch (error) {
    console.error('Error deleting order:', error)
    errorMessage.value = 'Failed to delete order'
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

watch([search, pageSize, filteredOrders], () => {
  if (currentPage.value > pageCount.value) currentPage.value = pageCount.value
  if (currentPage.value < 1) currentPage.value = 1
})

onMounted(() => {
  fetchOrders()
  fetchUsers()
})
</script>

<style scoped></style>
