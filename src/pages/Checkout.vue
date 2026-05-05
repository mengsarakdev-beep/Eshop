<script setup lang="ts">
defineOptions({
  name: 'CheckoutPage',
})

import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useCart } from '../store/cartStore'
import { useAuth } from '../store/authStore'

const cartStore = useCart()
const router = useRouter()
const auth = useAuth()

const name = ref('')
const phone = ref('')
const address = ref('')
const payment = ref<'ABA'>('ABA')
const loading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')
const qrUrl = ref('')
const transactionId = ref('')
const orderId = ref<number | null>(null)
const paymentConfirmed = ref(false)
const paymentTotal = ref(0)
const showQrModal = ref(false)

const fallbackQr = '/image.png'
const ABA_RATE = 4100
const transactionPlaceholder = 'Will appear after QR generation'

const totalAmount = computed(() => Number(cartStore.totalPrice || 0))
const payableAmount = computed(() => paymentTotal.value || totalAmount.value)
const payableAmountKhr = computed(() => Math.round(payableAmount.value * ABA_RATE))
const showQrPanel = computed(() => !!orderId.value && !!qrUrl.value && !paymentConfirmed.value)
const hasActiveOrder = computed(() => !!orderId.value && !paymentConfirmed.value)
const canSubmit = computed(() => {
  return !!name.value.trim() && !!phone.value.trim() && !!address.value.trim() && cartStore.cart.length > 0 && !loading.value && !hasActiveOrder.value
})

onMounted(() => {
  if (!auth.isLoggedIn) {
    alert('Please login to proceed to checkout')
    router.replace('/login')
    return
  }

  cartStore.loadCart()
  name.value = auth.username || ''
  phone.value = auth.phone || ''
})

async function placeOrder() {
  errorMessage.value = ''
  successMessage.value = ''
  paymentConfirmed.value = false
  qrUrl.value = ''
  transactionId.value = ''
  orderId.value = null
  paymentTotal.value = 0

  if (hasActiveOrder.value) {
    errorMessage.value = `Order #${orderId.value} is already created. Please scan the QR and confirm payment first.`
    return
  }

  if (!name.value.trim() || !phone.value.trim() || !address.value.trim()) {
    errorMessage.value = 'Please fill in your name, phone, and address.'
    return
  }

  if (cartStore.cart.length === 0) {
    errorMessage.value = 'Your cart is empty.'
    return
  }

  if (!auth.user_id) {
    errorMessage.value = 'Please login first.'
    router.replace('/login')
    return
  }

  loading.value = true

  try {
    const apiUrl = 'http://localhost/Eshop/Backend/api/order/create_checkout.php'

    const payload = {
      user_id: auth.user_id,
      name: name.value.trim(),
      phone: phone.value.trim(),
      address: address.value.trim(),
      payment_method: payment.value,
      total_amount: totalAmount.value,
      cart_items: cartStore.cart.map((item) => ({
        product_id: item.id,
        quantity: item.quantity,
        price: item.price,
      })),
    }

    const res = await fetch(apiUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    })

    const data = await res.json()

    if (!data.success) {
      throw new Error(data.message || 'Checkout failed')
    }

    orderId.value = Number(data.order_id)
    paymentTotal.value = Number(data.total ?? totalAmount.value)
    qrUrl.value = data.qr_url || fallbackQr
    transactionId.value = data.transaction_id || ''
    showQrModal.value = true
    successMessage.value = `Order #${orderId.value} created successfully. Please scan the QR and confirm payment.`
  } catch (error: unknown) {
    console.error('Checkout error:', error)
    errorMessage.value = error instanceof Error ? error.message : 'Failed to place order.'
  } finally {
    loading.value = false
  }
}


</script>

<template>
  <div class="max-w-7xl mx-auto p-4 md:p-6 ">
    <div class="dark:border-white border mb-3 rounded-lg bg-gradient-to-r  text-gray-700 dark:text-white shadow-md mt-6 p-3 max-w-7xl">
      <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-200">Secure Checkout</p>
      <h1 class="mt-2 text-3xl font-bold md:text-2xl">Complete your order</h1>
      <p class="mt-2 max-w-2xl text-sm text-slate-400 md:text-base">
        Fill in your delivery details and use ABA QR for quick mobile payment.
      </p>
    </div>

    <div v-if="errorMessage" class="mb-4 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700">
      {{ errorMessage }}
    </div>

    <div v-if="successMessage" class="mb-2 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-700">
      {{ successMessage }}
    </div>

    <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr] ">
      <div class="space-y-6">
        <section class="dark:bg-gray-900  rounded-md bg-white p-6 shadow-sm ring-1 ring-slate-200">
          <h2 class="text-md font-semibold text-slate-900 dark:text-white">Customer information</h2>
          <div class="mt-4 grid gap-4">
            <input v-model="name" readonly placeholder="Full Name"
              class="dark:bg-slate-800 w-full dark:text-white cursor-not-allowed rounded-sm border border-slate-200 bg-slate-100 px-4 py-3 text-slate-500 outline-none" />
            <input v-model="phone" readonly placeholder="Phone Number"
              class="dark:bg-slate-800 dark:text-white w-full cursor-not-allowed rounded-sm border border-slate-200 bg-slate-100 px-4 py-3 text-slate-500 outline-none" />
            <p class="-mt-1 text-xs text-slate-500">Name and phone come from your account profile and cannot be edited here.</p>
            <textarea v-model="address" rows="4" placeholder="Delivery Address"
              class="dark:bg-slate-800 w-full rounded-sm border border-slate-200 px-4 py-3 outline-none transition focus:border-indigo-400" />
          </div>
        </section>


      </div>

      <div class="space-y-6">
        <section class="rounded-md bg-white p-6 border dark:border-white dark:bg-gray-800 shadow-sm ring-1 ring-slate-200">
          <h2 class="md:text-xl font-semibold text-slate-900 dark:text-white">Order summary</h2>

          <div v-if="cartStore.cart.length" class="mt-4 space-y-3">
            <div v-for="item in cartStore.cart" :key="item.id"
              class="flex items-center justify-between  rounded-md dark:bg-gray-700 border bg-slate-50 p-3">
              <div>
                <p class="dark:text-white font-semibold text-slate-900">{{ item.name }}</p>
                <p class="text-sm text-slate-400">Qty: {{ item.quantity }}</p>
              </div>
              <span class="dark:text-white font-semibold text-slate-900">${{ (item.price * item.quantity).toFixed(2) }}</span>
            </div>
          </div>
          <p v-else class="mt-4 text-sm text-slate-500">Your cart is empty.</p>

          <p v-if="hasActiveOrder"
            class="mt-4 rounded-xl border border-cyan-200 bg-cyan-50 px-4 py-3 text-sm text-cyan-700">
            Order <span class="font-semibold">#{{ orderId }}</span> is already saved. Please complete the ABA QR payment
            before creating another order.
          </p>

          <div class="mt-4 border-t  border-slate-200 pt-4">
            <div class="flex items-center dark:text-white justify-between text-lg font-bold text-slate-900">
              <span>Total</span>
              <span>${{ totalAmount.toFixed(2) }}</span>
            </div>
          </div>

          <button @click="placeOrder" :disabled="!canSubmit"
            class="mt-5 w-full rounded-md  bg-indigo-600 py-3 font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60">
            {{ loading ? 'Processing...' : hasActiveOrder ? `Order #${orderId} Created` : `Checkout -
            $${totalAmount.toFixed(2)}` }}
          </button>
        </section>

        <section v-if="paymentConfirmed" class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
          <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-center">
            <div
              class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100 text-2xl text-emerald-700">
              ✓</div>
            <h3 class="mt-3 text-xl font-bold text-emerald-700">Payment Success</h3>
            <p class="mt-2 text-sm text-emerald-700">
              Your payment for order <span class="font-semibold">#{{ orderId }}</span> was received successfully.
            </p>
          </div>
        </section>
      </div>
    </div>

    <div v-if="showQrModal && showQrPanel" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div class="w-full max-w-lg rounded-md bg-white p-6 shadow-2xl">
        <div class="flex items-center justify-between gap-3">
          <div>
            <h2 class="text-xl font-bold text-slate-900">ABA QR Payment</h2>
            <p class="text-sm text-slate-500">Scan the QR and click after payment.</p>
          </div>
          <button type="button" @click="showQrModal = false"
            class="rounded bg-slate-100 px-3 py-1.5 text-sm text-slate-600 transition hover:bg-slate-200">
            Close
          </button>
        </div>

        <div class="mt-4 rounded-2xl bg-slate-50 p-4 text-center">
          <img :src="qrUrl || fallbackQr" alt="ABA QR"
            class="mx-auto w-full max-w-[260px] rounded-2xl border border-slate-200 bg-white" />

          <div class="mt-4 grid gap-3 rounded-2xl bg-white p-4 text-left ring-1 ring-slate-200 sm:grid-cols-2">
            <div>
              <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Order ID</p>
              <p class="mt-1 font-semibold text-slate-900">#{{ orderId || 'Pending' }}</p>
            </div>
            <div>
              <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Transaction</p>
              <p class="mt-1 break-all font-semibold text-slate-900">{{ transactionId || transactionPlaceholder }}</p>
            </div>
            <div>
              <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Amount (USD)</p>
              <p class="mt-1 text-lg font-bold text-indigo-700">${{ payableAmount.toFixed(2) }}</p>
            </div>
            <div>
              <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Amount (KHR)</p>
              <p class="mt-1 text-lg font-bold text-cyan-700">៛{{ payableAmountKhr.toLocaleString() }}</p>
            </div>
          </div>

          <p class="mt-3 text-sm text-slate-500">Account name: <span class="font-semibold text-slate-800">MENG
              SARAK</span></p>
        </div>


      </div>
    </div>
  </div>
</template>
