<template>
  <div class="space-y-4 px-3 py-4 sm:space-y-3 sm:px-6 sm:py-8">
    <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100 sm:text-2xl">Dashboard</h2>

    <!-- Stats Cards -->
    <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

      <!-- PRODUCTS -->
      <div
        class="group rounded-2xl border border-indigo-200/60 bg-gradient-to-br from-indigo-50/40 to-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md dark:border-indigo-900/40 dark:from-indigo-500/10 dark:to-slate-950">

        <div class="flex items-center justify-between">
          <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Products</p>

          <div
            class="rounded-xl bg-indigo-50 p-2 text-indigo-600 ring-1 ring-indigo-100 dark:bg-indigo-500/10 dark:text-indigo-400 dark:ring-indigo-500/20">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                d="M21 8.5l-9-5-9 5 9 5 9-5zM3 8.5v7l9 5 9-5v-7M12 13.5v7" />
            </svg>
          </div>
        </div>

        <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
          {{ totalProducts }}
        </p>

      </div>

      <!-- ORDERS -->
      <div
        class="group rounded-2xl border border-emerald-200/60 bg-gradient-to-br from-emerald-50/40 to-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md dark:border-emerald-900/40 dark:from-emerald-500/10 dark:to-slate-950">

        <div class="flex items-center justify-between">
          <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Orders</p>

          <div
            class="rounded-xl bg-emerald-50 p-2 text-emerald-600 ring-1 ring-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                d="M8 7h8M8 11h8M8 15h5M6 3h12a2 2 0 012 2v16l-2-1-2 1-2-1-2 1-2-1-2 1-2-1V5a2 2 0 012-2z" />
            </svg>
          </div>
        </div>

        <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
          {{ totalOrders }}
        </p>

      </div>

      <!-- CART TODAY -->
      <div
        class="group rounded-2xl border border-amber-200/60 bg-gradient-to-br from-amber-50/40 to-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md dark:border-amber-900/40 dark:from-amber-500/10 dark:to-slate-950">

        <div class="flex items-center justify-between">
          <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Cart Today</p>

          <div
            class="rounded-xl bg-amber-50 p-2 text-amber-600 ring-1 ring-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-500/20">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                d="M3 3h2l1 5h13l2-5M7 13h10l2-5H6M7 13l-1 6h12M9 21a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z" />
            </svg>
          </div>
        </div>

        <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
          {{ cartToday }}
        </p>

      </div>

      <!-- USERS -->
      <div
        class="group rounded-2xl border border-sky-200/60 bg-gradient-to-br from-sky-50/40 to-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md dark:border-sky-900/40 dark:from-sky-500/10 dark:to-slate-950">

        <div class="flex items-center justify-between">
          <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Users</p>

          <div
            class="rounded-xl bg-sky-50 p-2 text-sky-600 ring-1 ring-sky-100 dark:bg-sky-500/10 dark:text-sky-400 dark:ring-sky-500/20">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                d="M16 14a4 4 0 10-8 0m8 0a4 4 0 01-8 0m8 0c2.21 0 4 1.79 4 4v2H4v-2c0-2.21 1.79-4 4-4" />
            </svg>
          </div>
        </div>

        <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
          {{ totalUsers }}
        </p>

      </div>

    </section>

    <!-- Chart Section -->
    <section class="w-full">
      <div
        class="rounded-2xl border border-slate-200/60 bg-gradient-to-br from-white to-slate-50 p-5 shadow-sm transition dark:border-slate-800 dark:from-slate-900 dark:to-slate-950 sm:p-6">

        <!-- HEADER -->
        <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

          <div>
            <h3 class="text-lg font-semibold tracking-tight text-slate-900 dark:text-slate-100">
              Sales Overview
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">
              Track performance in real time
            </p>
          </div>

          <!-- TOGGLES -->
          <div class="flex flex-wrap items-center gap-3">

            <!-- TIME TOGGLE -->
            <div
              class="flex items-center rounded-xl border border-slate-200 bg-white p-1 shadow-sm dark:border-slate-700 dark:bg-slate-900">
              <button @click="chartMode = 'day'"
                :class="chartMode === 'day'
                  ? 'rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white shadow'
                  : 'rounded-lg px-3 py-1.5 text-xs text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800'">
                Daily
              </button>

              <button @click="chartMode = 'month'"
                :class="chartMode === 'month'
                  ? 'rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white shadow'
                  : 'rounded-lg px-3 py-1.5 text-xs text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800'">
                Monthly
              </button>
            </div>

            <!-- STYLE TOGGLE -->
            <div
              class="flex items-center rounded-xl border border-slate-200 bg-white p-1 shadow-sm dark:border-slate-700 dark:bg-slate-900">
              <button @click="chartStyle = 'bar'"
                :class="chartStyle === 'bar'
                  ? 'rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white shadow'
                  : 'rounded-lg px-3 py-1.5 text-xs text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800'">
                Bar
              </button>

              <button @click="chartStyle = 'pie'"
                :class="chartStyle === 'pie'
                  ? 'rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white shadow'
                  : 'rounded-lg px-3 py-1.5 text-xs text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800'">
                Pie
              </button>
            </div>

          </div>
        </div>

        <!-- CHART AREA -->
        <div class="relative">

          <!-- BAR CHART -->
          <div v-if="chartStyle === 'bar'" class="relative h-72 sm:h-96">
            <div
              class="absolute inset-0 rounded-xl bg-gradient-to-tr from-indigo-50/30 via-transparent to-emerald-50/20 dark:from-indigo-500/10">
            </div>
            <canvas ref="barChartCanvas" class="relative z-10 h-full w-full"></canvas>
          </div>

          <!-- PIE CHART -->
          <div v-if="chartStyle === 'pie'" class="relative flex h-72 items-center justify-center sm:h-96">
            <div
              class="absolute inset-0 rounded-xl bg-gradient-to-tr from-emerald-50/30 via-transparent to-indigo-50/20 dark:from-emerald-500/10">
            </div>
            <canvas ref="pieChartCanvas" class="relative z-10 h-full w-full max-w-full"></canvas>
          </div>

        </div>

      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import axios from 'axios'
import { ref, onMounted, onBeforeUnmount, computed, watch, nextTick } from 'vue'
import { Chart as ChartJS, registerables } from 'chart.js'
import type { Chart } from 'chart.js'
import { useTheme } from '../store/themeStore'

ChartJS.register(...registerables)

const API_BASE = 'http://localhost/Eshop/Backend/api'
const { isDarkMode, initializeTheme } = useTheme()

const isLoading = ref(false)
const totalProducts = ref(0)
const totalOrders = ref(0)
const totalUsers = ref(0)
const totalRevenue = ref(0)
const cartToday = ref(0)
const recentOrders = ref<Array<{ id: string | number; user: string; total: string; status: string; date: string }>>([])

type OrderData = {
  created_at?: string
  total_amount?: number | string
  items?: Array<{ quantity?: number | string }>
  quantity?: number | string
}

function getOrderQuantity(order: OrderData) {
  if (Array.isArray(order.items)) {
    return order.items.reduce((sum, item) => sum + Number(item.quantity || 0), 0)
  }
  return Number(order.quantity || 0)
}

const ordersData = ref<OrderData[]>([])
const chartMode = ref<'day' | 'month'>('day')
const chartStyle = ref<'bar' | 'pie'>('bar')

const barChartCanvas = ref<HTMLCanvasElement | null>(null)
const pieChartCanvas = ref<HTMLCanvasElement | null>(null)

let barChartInstance: Chart | null = null
let pieChartInstance: Chart | null = null

const chartData = computed(() => {
  return chartMode.value === 'day' ? buildDayBuckets(ordersData.value) : buildMonthBuckets(ordersData.value)
})

function buildMonthBuckets(orders: OrderData[]) {
  const buckets: Record<string, number> = {}
  const now = new Date()

  // last 6 months, include current
  for (let i = 5; i >= 0; i--) {
    const d = new Date(now.getFullYear(), now.getMonth() - i, 1)
    const label = d.toLocaleDateString('en-US', { month: 'short', year: '2-digit' })
    buckets[label] = 0
  }

  orders.forEach((order) => {
    if (!order.created_at) return
    const date = new Date(order.created_at)
    if (isNaN(date.getTime())) return
    const label = date.toLocaleDateString('en-US', { month: 'short', year: '2-digit' })
    if (!(label in buckets)) return
    const quantity = getOrderQuantity(order)
    if (Number.isNaN(quantity)) return
    buckets[label] = (buckets[label] || 0) + quantity
  })

  const max = Math.max(...Object.values(buckets), 1)

  return Object.entries(buckets).map(([label, value]) => ({
    label,
    value,
    normalized: value === 0 ? 0 : value / max
  }))
}

function buildDayBuckets(orders: OrderData[]) {
  const buckets: Record<string, number> = {}
  const now = new Date()

  // last 7 days
  for (let i = 6; i >= 0; i--) {
    const d = new Date(now)
    d.setDate(now.getDate() - i)
    const label = d.toLocaleDateString('en-US', { month: 'short', day: '2-digit' })
    buckets[label] = 0
  }

  orders.forEach((order) => {
    if (!order.created_at) return
    const date = new Date(order.created_at)
    if (isNaN(date.getTime())) return
    const label = date.toLocaleDateString('en-US', { month: 'short', day: '2-digit' })
    if (!(label in buckets)) return
    const quantity = getOrderQuantity(order)
    if (Number.isNaN(quantity)) return
    buckets[label] = (buckets[label] || 0) + quantity
  })

  const max = Math.max(...Object.values(buckets), 1)

  return Object.entries(buckets).map(([label, value]) => ({
    label,
    value,
    normalized: value === 0 ? 0 : value / max
  }))
}

async function fetchDashboardData() {
  isLoading.value = true
  try {
    // Fetch products count
    const productsRes = await axios.get(`${API_BASE}/product/get_products.php`, { withCredentials: true })
    const productList = Array.isArray(productsRes.data) ? productsRes.data : productsRes.data?.products || []
    totalProducts.value = productList.length

    // Fetch orders (admin: all orders)
    const ordersRes = await axios.get(`${API_BASE}/order/get_orders.php?all=1`, { withCredentials: true })
    const orderList = Array.isArray(ordersRes.data) ? ordersRes.data : ordersRes.data?.orders || []

    // Process unique orders to avoid duplicates per item row
    const orderMap = new Map<string | number, any>()
    orderList.forEach((order: any) => {
      const id = order.order_id || order.id || order.order_id
      if (!orderMap.has(id)) {
        orderMap.set(id, order)
      }
    })

    const uniqueOrders = Array.from(orderMap.values())
    ordersData.value = uniqueOrders

    // Calculate totals
    totalOrders.value = uniqueOrders.length
    totalRevenue.value = uniqueOrders.reduce((sum: number, order: any) => sum + Number(order.total_amount || 0), 0)

    // Today cart items from each unique order's items
    const today = new Date().toDateString()
    cartToday.value = uniqueOrders.reduce((sum: number, order: any) => {
      const orderDate = order.created_at ? new Date(order.created_at).toDateString() : ''
      if (orderDate === today) {
        sum += getOrderQuantity(order)
      }
      return sum
    }, 0)

    // Fetch users count
    const usersRes = await axios.get('http://localhost/Eshop/Backend/api/auth/get_users.php', { withCredentials: true })
    const userList = Array.isArray(usersRes.data) ? usersRes.data : usersRes.data?.users || []
    totalUsers.value = userList.length

    // Set recent orders (limit to 3)
    recentOrders.value = orderList.slice(0, 3).map((order: { id?: number | string; user_name?: string; total_amount?: number | string; status?: string; created_at?: string }) => ({
      id: order.id || '#' + Math.random().toString(36).substr(2, 4),
      user: order.user_name || 'Unknown',
      total: `$${parseFloat(String(order.total_amount || 0)).toFixed(2)}`,
      status: order.status || 'Pending',
      date: order.created_at ? new Date(order.created_at).toLocaleDateString('en-US', { year: 'numeric', month: '2-digit', day: '2-digit' }) : 'N/A'
    }))

    // Keep sales chart data live from ordersData computed property
    ordersData.value = uniqueOrders

  } catch (error) {
    console.error('Error fetching dashboard data:', error)
    // Set default values on error
    totalProducts.value = 0
    recentOrders.value = []
  } finally {
    isLoading.value = false
  }
}

function getChartColors() {
  const isDark = isDarkMode.value
  return {
    textColor: isDark ? '#cbd5e1' : '#1e293b',
    gridColor: isDark ? '#475569' : '#e2e8f0',
    backgroundColor: isDark ? '#0f172a' : '#ffffff',
    barColor: '#3b82f6'
  }
}

const dataLabelPlugin = {
  id: 'dataLabelPlugin',
  afterDatasetsDraw(chart: any) {
    const ctx = chart.ctx
    if (!ctx) return

    const labelColor = chart.options?.plugins?.datalabels?.color || '#1e293b'
    ctx.save()
    ctx.fillStyle = labelColor
    ctx.textAlign = 'center'
    ctx.textBaseline = 'middle'
    ctx.font = '12px ui-sans-serif, system-ui, sans-serif'

    chart.data.datasets.forEach((dataset: any, datasetIndex: number) => {
      const meta = chart.getDatasetMeta(datasetIndex)
      meta.data.forEach((element: any, index: number) => {
        const rawValue = dataset.data[index]
        if (rawValue == null) return

        const value = typeof rawValue === 'number' ? String(Math.round(rawValue)) : String(rawValue)
        const position = element.getCenterPoint?.() || { x: element.x, y: element.y }
        if (!position) return

        const offsetY = chart.config.type === 'bar' ? -12 : 0
        ctx.fillText(value, position.x, position.y + offsetY)
      })
    })

    ctx.restore()
  }
}

function destroyCharts() {
  if (barChartInstance) {
    barChartInstance.destroy()
    barChartInstance = null
  }
  if (pieChartInstance) {
    pieChartInstance.destroy()
    pieChartInstance = null
  }
}

function renderCurrentChart() {
  if (chartStyle.value === 'bar') {
    initializeBarChart()
  } else {
    initializePieChart()
  }
}

function initializeBarChart() {
  if (!barChartCanvas.value) return

  // Destroy existing chart if it exists
  if (barChartInstance) {
    barChartInstance.destroy()
  }

  const colors = getChartColors()
  const ctx = barChartCanvas.value.getContext('2d')
  if (!ctx) return

  const chartConfig = {
    type: 'bar' as const,
    plugins: [dataLabelPlugin],
    data: {
      labels: chartData.value.map((d) => d.label),
      datasets: [
        {
          label: 'Products Ordered',
          data: chartData.value.map((d) => d.value),
          backgroundColor: '#3b82f6',
          borderColor: '#1e40af',
          borderWidth: 1,
          borderRadius: 6,
          hoverBackgroundColor: '#1e3a8a'
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: true,
          labels: {
            color: colors.textColor,
            font: { size: 12 },
            padding: 15
          }
        },
        tooltip: {
          backgroundColor: 'rgba(0, 0, 0, 0.8)',
          titleColor: '#ffffff',
          bodyColor: '#ffffff',
          borderColor: colors.gridColor,
          borderWidth: 1,
          padding: 12,
          displayColors: true,
          callbacks: {
            label: function (context: any) {
              return `${context.parsed.y} products`
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            color: colors.textColor,
            font: { size: 11 },
            stepSize: 1,
            precision: 0,
            callback: function (value: any) {
              return `${value}`
            }
          },
          grid: {
            color: colors.gridColor,
            drawBorder: false
          }
        },
        x: {
          ticks: {
            color: colors.textColor,
            font: { size: 11 }
          },
          grid: {
            display: false
          }
        }
      }
    } as any
  }

  barChartInstance = new ChartJS(ctx, chartConfig)
}

function initializePieChart() {
  if (!pieChartCanvas.value) return

  // Destroy existing chart if it exists
  if (pieChartInstance) {
    pieChartInstance.destroy()
  }

  const colors = getChartColors()
  const ctx = pieChartCanvas.value.getContext('2d')
  if (!ctx) return

  const pieColors = ['#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316']

  const chartConfig = {
    type: 'doughnut' as const,
    plugins: [dataLabelPlugin],
    data: {
      labels: chartData.value.map((d) => d.label),
      datasets: [
        {
          data: chartData.value.map((d) => d.value),
          backgroundColor: pieColors.slice(0, chartData.value.length),
          borderColor: colors.backgroundColor,
          borderWidth: 2,
          hoverOffset: 10
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'right' as const,
          labels: {
            color: colors.textColor,
            font: { size: 12 },
            padding: 15,
            usePointStyle: true
          }
        },
        tooltip: {
          backgroundColor: 'rgba(0, 0, 0, 0.8)',
          titleColor: '#ffffff',
          bodyColor: '#ffffff',
          borderColor: colors.gridColor,
          borderWidth: 1,
          padding: 12,
          callbacks: {
            label: function (context: any) {
              return `${context.parsed} products`
            }
          }
        }
      }
    } as any
  }

  pieChartInstance = new ChartJS(ctx, chartConfig)
}

// Watch for chart data or dark mode changes
watch([chartData, isDarkMode], async () => {
  await nextTick()
  renderCurrentChart()
})

// Watch for chart style changes
watch(chartStyle, async () => {
  await nextTick()
  renderCurrentChart()
})

onBeforeUnmount(() => {
  destroyCharts()
})

onMounted(() => {
  initializeTheme()
  fetchDashboardData().then(async () => {
    await nextTick()
    renderCurrentChart()
  })
})


</script>
