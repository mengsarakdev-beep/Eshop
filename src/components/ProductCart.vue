<template>
  <div
    class="max-md:w-38 flex flex-col rounded-lg border border-slate-200 bg-white p-1 shadow transition-colors dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">

    <!-- Heart -->
    <div class="flex justify-end mb-2" :class="isLoggedIn ? 'cursor-pointer' : 'opacity-50 cursor-not-allowed'"
      @click.stop="handleHeartClick">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transition" :class="heartClass" viewBox="0 0 24 24"
        fill="none" stroke="currentColor">
        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5
             2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09
             C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5
             c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
      </svg>
    </div>

    <!-- Image -->
    <img :src="absoluteImageUrl(product.image)" class="h-32 object-contain rounded mb-2 cursor-pointer"
      @click="openDetail" :alt="product.name" @error="onImageError" />

    <!-- Info -->
    <h3 class="mx-auto text-sm font-semibold text-slate-800 dark:text-slate-100 md:mb-2">{{ product.name }}</h3>
    <p class="mx-auto text-sm font-bold text-indigo-600 dark:text-indigo-400 md:mb-2">
      ${{ product.price }}
    </p>

    <!-- Add to Cart -->
    <button @click.stop="addToCart" :disabled="isOutOfStock"
      class="mt-auto rounded py-1 text-sm font-thin text-white transition" :class="isOutOfStock
        ? 'cursor-not-allowed bg-slate-400 dark:bg-slate-600'
        : 'bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-400'">
      {{ isOutOfStock ? 'Out of Stock' : 'Add to Cart' }}
    </button>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, onMounted, watch } from "vue";
import type { Product } from "../types";
import { useWishlistStore } from "../store/wishlistStore";
import { useAuth } from "../store/authStore";
import { useCart } from "../store/cartStore";

const props = defineProps<{ product: Product }>();
const emit = defineEmits<{
  (e: "add-to-cart", product: Product): void;
  (e: "open-detail", product: Product): void;
}>();

const fallbackImage = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='150' height='150' viewBox='0 0 150 150'%3E%3Crect width='150' height='150' fill='%23f3f4f6'/%3E%3Ctext x='50%25' y='50%25' text-anchor='middle' dy='.3em' fill='%236b7280' font-size='12'%3ENo Image%3C/text%3E%3C/svg%3E";
const wishlistStore = useWishlistStore();
const auth = useAuth();
const cartStore = useCart();

// -------- Wishlist / Heart --------
onMounted(async () => {
  if (auth.isLoggedIn && !wishlistStore.loaded) {
    await wishlistStore.loadWishlist();
  }
});

const isLiked = computed(() => wishlistStore.products.includes(props.product.id));
const isLoggedIn = computed(() => auth.isLoggedIn);
const isOutOfStock = computed(() => {
  const stock = props.product.stock;
  return stock !== undefined && stock !== null && Number(stock) <= 0;
});
const isPopping = ref(false);

watch(isLiked, () => {
  isPopping.value = true;
  setTimeout(() => (isPopping.value = false), 200);
});

const heartClass = computed(() =>
  [
    isLiked.value ? "text-red-500 fill-red-500" : "text-gray-400 dark:text-slate-500",
    isPopping.value ? "scale-125" : "",
  ].join(" ")
);

// -------- Actions --------
function addToCart() {
  if (isOutOfStock.value) return;

  const productWithImage = {
    ...props.product,
    image: absoluteImageUrl(props.product.image),
  };

  const added = cartStore.addToCart(productWithImage);
  if (added) {
    emit("add-to-cart", productWithImage);
  }
}

function openDetail() {
  emit("open-detail", props.product);
}

// -------- Wishlist Handler --------
async function handleHeartClick() {
  if (!auth.isLoggedIn) {
    alert("Please login first");
    return;
  }
  try {
    await wishlistStore.toggleWishlist(props.product.id);
  } catch (error) {
    console.error("Wishlist error:", error);
  }
}

// -------- Helper --------
function absoluteImageUrl(path: string | undefined) {
  if (!path) return fallbackImage;
  return path.startsWith("http") ? path : `http://localhost/Eshop/Backend/uploads/${path}`;
}

function onImageError(event: Event) {
  (event.target as HTMLImageElement).src = fallbackImage;
}
</script>
