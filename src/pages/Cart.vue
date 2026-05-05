<script setup lang="ts">
import { useCart } from "../store/cartStore"
import { useRouter } from "vue-router"

const cartStore = useCart()
const router = useRouter()

// Navigate to checkout page
function goToCheckout() {
  if (cartStore.cart.length === 0) {
    alert("Your cart is empty!")
    return
  }
  router.push("/checkout")
}
</script>

<template>
  <div class="mx-auto max-w-5xl p-6 transition-colors">
    <h2 class="mb-5 mt-5 text-xl font-semibold text-slate-900 dark:text-slate-100">Your Cart</h2>

    <!-- Empty Cart Message -->
    <div v-if="cartStore.cart.length === 0" class="mb-4 text-gray-500 dark:text-slate-300 text-center font-semibold text-lg">
      <p class="block mb-5">Your cart is empty</p>
      <button class="items-center">
        <router-link :to="{ name: 'Shop' }" class="block bg-indigo-600 hover:bg-blue-700 text-white font-semibold px-10 py-10 rounded-md shadow-md transition duration-300 ease-in-out">
          Shop Now
        </router-link>
      </button>
    </div>



    <!-- Cart Items -->
    <div v-for="item in cartStore.cart" :key="item.id"
      class="mb-2 flex items-center gap-4 rounded border-b border-slate-200 bg-white p-2 pb-3 text-slate-900 shadow transition-colors dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
      <img :src="item.image" class="w-20 h-20 rounded object-contain" />

      <div class="flex-1">
        <h3 class="font-semibold">{{ item.name }}</h3>
        <p class="text-indigo-600 dark:text-indigo-400">${{ (item.price * item.quantity).toFixed(2) }}</p>

        <!-- Quantity Controls -->
        <div class="mt-1 flex items-center gap-2">
          <button @click="cartStore.decreaseQty(item.id)"
            class="rounded bg-gray-200 px-2 dark:bg-slate-700 dark:text-slate-100">-</button>
          <span>{{ item.quantity }}</span>
          <button @click="cartStore.increaseQty(item.id)"
            class="rounded bg-gray-200 px-2 dark:bg-slate-700 dark:text-slate-100">+</button>
        </div>
      </div>

      <!-- Remove Button -->
      <button @click="cartStore.removeItem(item.id)" class="text-red-500 hover:text-red-700">
        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M19 7L5 7M6 7v12a2 2 0 002 2h8a2 2 0 002-2V7M10 11v6M14 11v6M9 7V5a2 2 0 012-2h2a2 2 0 012 2v2" />
        </svg>
      </button>
    </div>

    <!-- Total Price -->
    <div v-if="cartStore.cart.length > 0"
      class="mt-4 flex justify-between text-lg font-bold text-indigo-600 dark:text-indigo-400">
      <span>Total:</span>
      <span>${{ cartStore.totalPrice.toFixed(2) }}</span>
    </div>

    <!-- Checkout Button -->
    <button @click="goToCheckout" :disabled="cartStore.cart.length === 0"
      class="mt-6 w-full rounded bg-indigo-600 py-2 text-white transition hover:bg-indigo-700 disabled:opacity-50 dark:bg-indigo-500 dark:hover:bg-indigo-400">
      Checkout
    </button>
  </div>
</template>
