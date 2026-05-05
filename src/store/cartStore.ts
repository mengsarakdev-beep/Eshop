import { defineStore } from 'pinia'
import { ref, computed, watch } from 'vue'
import { useAuth } from './authStore'
import type { Product } from '../types'

interface CartItem {
  id: number
  name: string
  price: number
  image: string
  quantity: number
  stock?: number
}

export const useCart = defineStore('cart', () => {
  const auth = useAuth()

  const cart = ref<CartItem[]>([])

  const getCartKey = () => {
    return auth.user_id ? `cart_user_${auth.user_id}` : 'cart_guest'
  }

  function loadCart() {
    const saved = localStorage.getItem(getCartKey())
    cart.value = saved ? JSON.parse(saved) : []
  }

  function saveCart() {
    localStorage.setItem(getCartKey(), JSON.stringify(cart.value))
  }

  // 🔥 watch user change
  watch(
    () => auth.user_id,
    () => {
      loadCart()
    },
    { immediate: true },
  )

  // 🔥 watch cart change
  watch(
    cart,
    () => {
      saveCart()
    },
    { deep: true },
  )

  const addToCart = (product: Product) => {
    const stock = product.stock !== undefined ? Number(product.stock) : undefined

    if (stock !== undefined && stock <= 0) {
      return false
    }

    const existing = cart.value.find((i) => i.id === product.id)

    if (existing) {
      if (existing.stock !== undefined && existing.quantity >= existing.stock) {
        return false
      }

      existing.quantity++
      if (stock !== undefined) existing.stock = stock
      return true
    }

    cart.value.push({
      id: product.id,
      name: product.name,
      price: product.price,
      image: product.image || '',
      quantity: 1,
      stock,
    })

    return true
  }

  const increaseQty = (id: number) => {
    const item = cart.value.find((i) => i.id === id)
    if (item && (item.stock === undefined || item.quantity < item.stock)) item.quantity++
  }

  const decreaseQty = (id: number) => {
    const item = cart.value.find((i) => i.id === id)
    if (item && item.quantity > 1) item.quantity--
  }

  const removeItem = (id: number) => {
    cart.value = cart.value.filter((i) => i.id !== id)
  }

  const clearCart = () => {
    cart.value = []
  }

  const totalPrice = computed(() =>
    cart.value.reduce((sum, item) => sum + item.price * item.quantity, 0),
  )

  const cartCount = computed(() => cart.value.reduce((sum, item) => sum + item.quantity, 0))

  return {
    cart,
    loadCart,
    addToCart,
    increaseQty,
    decreaseQty,
    removeItem,
    clearCart,
    totalPrice,
    cartCount,
  }
})
