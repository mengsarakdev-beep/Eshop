// wishlistStore.ts
import { defineStore } from 'pinia'
import { ref, watch } from 'vue'
import { useAuth } from './authStore'

interface WishlistRow {
  product_id: number
}

interface WishlistApiResponse {
  success: boolean
  products?: number[]
  wishlists?: WishlistRow[]
  message?: string
}

interface WishlistItem {
  id: number
  name: string
  price: number | string
  image?: string
}

export const useWishlistStore = defineStore('wishlistStore', () => {
  const auth = useAuth()

  const products = ref<number[]>([])
  const loaded = ref(false)
  const wishlistCount = ref(0)

  const persistWishlist = (ids: number[]) => {
    const uniqueIds = ids
      .map((id) => Number(id))
      .filter((id, index, self) => Number.isFinite(id) && id > 0 && self.indexOf(id) === index)

    products.value = uniqueIds
    wishlistCount.value = uniqueIds.length

    if (auth.user_id) {
      localStorage.setItem(`wishlist_user_${auth.user_id}`, JSON.stringify(uniqueIds))
    }
  }

  watch(
    products,
    (newVal) => {
      wishlistCount.value = newVal.length

      if (auth.user_id) {
        localStorage.setItem(`wishlist_user_${auth.user_id}`, JSON.stringify(newVal))
      }
    },
    { deep: true },
  )

  const loadWishlist = async () => {
    if (!auth.user_id) {
      products.value = []
      wishlistCount.value = 0
      loaded.value = true
      return
    }

    try {
      loaded.value = false

      const saved = localStorage.getItem(`wishlist_user_${auth.user_id}`)
      if (saved) {
        const savedProducts = JSON.parse(saved)
        if (Array.isArray(savedProducts)) {
          persistWishlist(savedProducts)
        }
      }

      const res = await fetch('http://localhost/Eshop/Backend/api/wishlists/get_wishlist.php', {
        credentials: 'include',
      })

      const data: WishlistApiResponse = await res.json()

      if (data.success) {
        const backendProducts = Array.isArray(data.products)
          ? data.products
          : Array.isArray(data.wishlists)
            ? data.wishlists.map((item) => Number(item.product_id))
            : []

        persistWishlist(backendProducts)
      }
    } catch (err) {
      console.error('Wishlist load error:', err)
    } finally {
      loaded.value = true
    }
  }

  const addToWishlist = async (productId: number) => {
    if (!auth.isLoggedIn) {
      alert('Please login first')
      return false
    }

    try {
      const res = await fetch('http://localhost/Eshop/Backend/api/wishlists/add_to_wishlist.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({
          product_id: productId,
          user_id: auth.user_id,
        }),
      })

      const data = await res.json()

      if (data.success) {
        persistWishlist([...products.value, productId])
        return true
      }

      console.warn(data.message)
    } catch (err) {
      console.error('Wishlist add error:', err)
    }

    return false
  }

  const removeFromWishlist = async (productId: number) => {
    if (!auth.isLoggedIn) {
      alert('Please login first')
      return false
    }

    try {
      const res = await fetch(
        'http://localhost/Eshop/Backend/api/wishlists/remove_from_wishlist.php',
        {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          credentials: 'include',
          body: JSON.stringify({
            product_id: productId,
            user_id: auth.user_id,
          }),
        },
      )

      const data = await res.json()

      if (data.success) {
        persistWishlist(products.value.filter((id) => id !== productId))
        return true
      }

      console.warn(data.message)
    } catch (err) {
      console.error('Wishlist remove error:', err)
    }

    return false
  }

  const toggleWishlist = async (productId: number) => {
    return products.value.includes(productId)
      ? removeFromWishlist(productId)
      : addToWishlist(productId)
  }

  const clearWishlist = async () => {
    if (!auth.isLoggedIn) {
      alert('Please login first')
      return false
    }

    try {
      const res = await fetch('http://localhost/Eshop/Backend/api/wishlists/clear_wishlist.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
      })

      const data = await res.json()

      if (data.success) {
        persistWishlist([])
        return true
      }

      console.warn(data.message)
    } catch (err) {
      console.error('Wishlist clear error:', err)
    }

    return false
  }

  const fetchWishlistItems = async (): Promise<WishlistItem[]> => {
    if (!auth.user_id) return []

    try {
      const res = await fetch(
        'http://localhost/Eshop/Backend/api/wishlists/get_wishlist_items.php',
        {
          credentials: 'include',
        },
      )

      const data = await res.json()

      if (data.success && Array.isArray(data.products)) {
        const uniqueProducts = data.products.filter(
          (item: WishlistItem, index: number, array: WishlistItem[]) =>
            index === array.findIndex((product) => Number(product.id) === Number(item.id)),
        )

        persistWishlist(uniqueProducts.map((item: WishlistItem) => Number(item.id)))
        return uniqueProducts
      }
    } catch (err) {
      console.error('Wishlist items error:', err)
    }

    if (!products.value.length) {
      return []
    }

    try {
      const res = await fetch('http://localhost/Eshop/Backend/api/product/get_products.php')
      const data = await res.json()

      if (data.success && Array.isArray(data.products)) {
        return data.products.filter((item: WishlistItem) =>
          products.value.includes(Number(item.id)),
        )
      }
    } catch (err) {
      console.error('Wishlist fallback error:', err)
    }

    return []
  }

  watch(
    () => auth.user_id,
    async (newUser) => {
      if (newUser) {
        await loadWishlist()
      } else {
        products.value = []
        wishlistCount.value = 0
        loaded.value = true
      }
    },
    { immediate: true },
  )

  return {
    products,
    loaded,
    wishlistCount,
    loadWishlist,
    addToWishlist,
    removeFromWishlist,
    toggleWishlist,
    clearWishlist,
    fetchWishlistItems,
  }
})
