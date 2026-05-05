<template>
  <div v-if="show && product"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div
      class="relative w-full  lg:w-auto grid max-w-[calc(100vw-1rem)] sm:max-w-[calc(100vw-1.5rem)] md:max-w-4xl lg:max-w-6xl grid-cols-1 md:gap-4 overflow-hidden rounded border border-slate-200/60 bg-white/95 shadow-2xl shadow-slate-900/10 backdrop-blur-xl transition-all duration-300 dark:border-slate-700/70 dark:bg-slate-950/95 lg:grid-cols-[1.05fr_0.95fr] sm:gap-4 max-h-[calc(100vh-1rem)]">




      <!-- Close Button -->
      <button @click="$emit('close')"
        class="absolute right-5 top-5 z-20 inline-flex h-11 w-11 items-center justify-center rounded-full bg-white/90 text-slate-700 shadow-md shadow-slate-200/60 transition hover:-translate-y-0.5 hover:bg-white dark:bg-slate-900/90 dark:text-slate-100 dark:shadow-slate-950/60 sm:right-4 sm:top-4 sm:h-10 sm:w-10">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>

      <!-- Images -->
      <section
        class="relative min-h-0 flex flex-col md:gap-4 overflow-y-auto bg-slate-100 p-2 dark:bg-slate-900/70 sm:p-5 md:p-6 lg:p-8">

        <div
          class="overflow-hidden mb-1 rounded-[1.5rem] bg-gradient-to-br from-slate-100 via-white to-slate-100 p-2 shadow-inner shadow-slate-200/40 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">
          <p v-if="product.category_name"
            class="md:hidden absolute top-0 left-0 flex w-16 justify-center rounded bg-indigo-100 py-1 text-xs  font-semibold uppercase tracking-[0.18em] text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-200">
            {{ product.category_name }}
          </p><img :src="selectedImage" :alt="product.name"
            class="h-[200px] w-full rounded-[1.5rem] object-contain transition duration-500 hover:scale-105 sm:h-[260px] md:h-[320px] lg:h-[420px]"
            @error="onImageError" />
        </div>

        <div class="flex gap-3 overflow-x-auto pb-1">
          <button v-for="(img, index) in allImages" :key="index" @click="selectedImage = img"
            class="relative flex h-12 w-12 flex-shrink-0 overflow-hidden rounded border transition-all duration-200 hover:scale-105 sm:h-14 sm:w-14 md:h-16 md:w-16 lg:h-20 lg:w-20"
            :class="selectedImage === img ? 'border-indigo-500 shadow-lg shadow-indigo-200/40' : 'border-slate-200 dark:border-slate-700'">
            <img :src="img" class="h-full w-full object-cover" @error="onImageErrorThumbnail(index)" />
            <span v-if="selectedImage === img"
              class="absolute inset-x-0 bottom-0 rounded bg-indigo-600/70 py-0.4 text-center md:text-[11px] text-[8px] font-semibold text-white">Selected</span>
          </button>
        </div>
        <div class="md:hidden grid gap-3 sm:grid-cols-2">
            <div
              class="rounded flex md:block items-center gap-3 justify-center bg-slate-50 md:p-4 text-sm text-slate-700 shadow-sm dark:bg-slate-900/70 dark:text-slate-300">
              <p class="font-semibold">Stock status</p>
              <p class="md:mt-2 text-base font-semibold" :class="stockClass">{{ stockText }}</p>
              <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ product.stock ?? 0 }} items available</p>
            </div>
          </div>

        <!-- <p v-if="allImages.length > 1" class="text-center text-sm text-slate-500 dark:text-slate-400">
          {{ allImages.length }} product images available
        </p> -->
      </section>

      <!-- Details -->
      <section class="flex min-w-0 flex-col justify-between md:gap-5 gap-1 overflow-y-auto p-5 sm:p-6 md:p-7 lg:p-8">
        <div class="md:space-y-4 space-y-2">
          <div class="flex flex-col relative items-start justify-between md:gap-4 sm:flex-row sm:items-center">
            <div class="-mt-3">
              <p v-if="product.category_name"
                class="hidden md:flex rounded bg-indigo-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-200">
                {{ product.category_name }}
              </p>
              <h2
                class=" md:mt-3 md:text-2xl text-md font-semibold tracking-tight text-slate-900 dark:text-white sm:text-3xl">
                {{ product.name }}
              </h2>
            </div>
            <button @click="handleHeartClick"
              class="inline-flex  absolute right-1  md:top-2 md:left-72 h-12 w-12 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 shadow-sm transition hover:border-rose-300 hover:bg-rose-50 hover:text-rose-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-rose-900/30 dark:hover:text-rose-400"
              :aria-pressed="isLiked">
              <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round"
                :class="isLiked ? 'fill-rose-500 text-rose-500' : 'text-slate-400 dark:text-slate-300'">
                <path
                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
              </svg>
            </button>
          </div>

          <div class="flex flex-wrap items-center gap-3  md:text-sm  text-slate-500 dark:text-slate-400">
            <span
              class="inline-flex text-red-600 md:text-xl text-sm font-semibold items-center gap-2 rounded bg-slate-100 md:px-3 px-1 py-1 dark:bg-slate-800">
              <strong class="text-blue-700 md:text-xl text-sm dark:text-slate-200">Price:</strong> {{ formattedPrice }}$
            </span>
            <span
              class="inline-flex items-center text-sm gap-2 rounded bg-emerald-100 md:px-3 px-1 md:py-2 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              Best value
            </span>
          </div>

          <div v-if="product.description"
            class="rounded bg-slate-50 md:p-5 p-1 shadow-sm shadow-slate-200/50 dark:bg-slate-900/70 dark:shadow-slate-950/50">
            <p class="break-words text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-line">
              {{ product.description }}
            </p>
          </div>

          <div class="hidden md:grid gap-3 sm:grid-cols-2">
            <div
              class="rounded flex md:block items-center gap-3 justify-center bg-slate-50 md:p-4 text-sm text-slate-700 shadow-sm dark:bg-slate-900/70 dark:text-slate-300">
              <p class="font-semibold">Stock status</p>
              <p class="md:mt-2 text-base font-semibold" :class="stockClass">{{ stockText }}</p>
              <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ product.stock ?? 0 }} items available</p>
            </div>
          </div>
        </div>

        <div class="md:space-y-4 space-y-1">
          <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-4">

            <!-- Quantity Box -->
            <div
              class="flex items-center justify-between rounded border border-slate-200 bg-white md:px-3 md:py-2 px-2 shadow-sm dark:border-slate-700 dark:bg-slate-900 w-28 sm:w-auto">
              <button @click="decrementQuantity"
                class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800">
                −
              </button>

              <span class="mx-4 min-w-[2.5rem] text-center text-lg font-semibold text-slate-900 dark:text-white">
                {{ quantity }}
              </span>

              <button @click="incrementQuantity"
                class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800">
                +
              </button>
            </div>

            <!-- Helper Text -->
            <p class="text-sm hidden md:flex text-slate-500 dark:text-slate-400 text-center sm:text-left">
              Select quantity before checkout.
            </p>

          </div>

          <div class="flex md:grid md:gap-3 gap-1 sm:grid-cols-2">
            <button @click="addToCart" :disabled="stockCount <= 0 || isAddingToCart"
              class="group relative inline-flex w-full items-center justify-center rounded bg-gradient-to-r from-indigo-600 to-sky-600 px-3 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 transition hover:from-indigo-700 hover:to-sky-700 disabled:cursor-not-allowed disabled:opacity-50 sm:px-5 sm:py-1 lg:px-6">
              <span class="relative z-10 flex items-center gap-2">
                <svg v-if="isAddingToCart" class="h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                </svg>
                <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.1 5H19M7 13l-1.1-5m1.1 5h10m-10 0v8a2 2 0 002 2h6a2 2 0 002-2v-8" />
                </svg>
                {{ isAddingToCart ? 'Adding...' : stockCount <= 0 ? 'Out of Stock' : 'Add to Cart' }} </span>
            </button>

            <button @click="handleHeartClick"
              class="inline-flex w-full items-center justify-center rounded border border-slate-200 bg-white md:px-6 px-2 md:py-4 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
              <svg class="h-5 w-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
              </svg>
              Add to Wishlist
            </button>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from "vue";
import type { Product } from "../types";
import { useAuth } from "../store/authStore";
import { useCart } from "../store/cartStore";
import { useWishlistStore } from "../store/wishlistStore";

const props = defineProps<{ show: boolean; product: Product | null }>();
const emit = defineEmits<{
  (e: "close"): void;
  (e: "add-to-cart", product: Product & { quantity?: number }): void;
}>();

const fallbackImage = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='400' viewBox='0 0 400 400'%3E%3Crect width='400' height='400' fill='%23f3f4f6'/%3E%3Ctext x='50%25' y='50%25' text-anchor='middle' dy='.3em' fill='%236b7280' font-size='24'%3ENo Image%3C/text%3E%3C/svg%3E";
const uploadsBaseUrl = "http://localhost/Eshop/Backend/uploads/";

const auth = useAuth();
const cartStore = useCart();
const wishlistStore = useWishlistStore();
const selectedImage = ref(fallbackImage);
const allImages = ref<string[]>([]);
const quantity = ref(1);
const isAddingToCart = ref(false);

onMounted(async () => {
  if (auth.isLoggedIn && !wishlistStore.loaded) {
    await wishlistStore.loadWishlist();
  }
});

function normalizeImageUrl(path?: string | null) {
  if (!path) return fallbackImage;
  return path.startsWith("http") || path.startsWith("data:")
    ? path
    : `${uploadsBaseUrl}${path.replace(/^\/+/, "")}`;
}

const allImagesComputed = computed(() => {
  if (!props.product) return [fallbackImage];

  const normalizedImages = [props.product.image, ...(props.product.images ?? [])]
    .map((img) => normalizeImageUrl(img))
    .filter(Boolean);

  return normalizedImages.length
    ? normalizedImages.filter((value, index, self) => self.indexOf(value) === index)
    : [fallbackImage];
});

watch(
  () => props.product,
  () => {
    const images = allImagesComputed.value;
    allImages.value = images;
    selectedImage.value = images[0] ?? fallbackImage;
    quantity.value = 1;
  },
  { immediate: true }
);

const formattedPrice = computed(() =>
  props.product
    ? props.product.price.toLocaleString("en-US", { style: "currency", currency: "USD" })
    : ""
);

const stockCount = computed(() => Number(props.product?.stock ?? 0));

const stockText = computed(() =>
  stockCount.value > 10 ? "In stock" : stockCount.value > 0 ? "Low stock" : "Out of stock"
);

const stockClass = computed(() =>
  stockCount.value > 10
    ? "text-emerald-700 dark:text-emerald-300"
    : stockCount.value > 0
      ? "text-amber-700 dark:text-amber-300"
      : "text-rose-700 dark:text-rose-300"
);

const isLiked = computed(() => {
  if (!props.product) return false;
  return wishlistStore.products.includes(props.product.id);
});

function incrementQuantity() {
  if (!props.product) return;
  const maxStock = Number(props.product.stock ?? 0);
  if (quantity.value < maxStock) {
    quantity.value += 1;
  }
}

function decrementQuantity() {
  if (quantity.value > 1) {
    quantity.value -= 1;
  }
}

async function addToCart() {
  if (!props.product || stockCount.value <= 0) return;

  isAddingToCart.value = true;

  try {
    const productWithImage = {
      ...props.product,
      image: normalizeImageUrl(props.product.image),
    };

    for (let i = 0; i < quantity.value; i++) {
      const added = cartStore.addToCart(productWithImage);
      if (!added) {
        alert("Cannot add more items. Maximum stock reached in your cart.");
        break;
      }
    }

    emit("add-to-cart", { ...productWithImage, quantity: quantity.value });
  } finally {
    isAddingToCart.value = false;
  }
}

async function handleHeartClick() {
  if (!props.product) return;

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

// ✅ Fallback for main image
function onImageError(event: Event) {
  (event.target as HTMLImageElement).src = fallbackImage;
}

// ✅ Fallback for thumbnails individually
function onImageErrorThumbnail(index: number) {
  return (event: Event) => {
    (event.target as HTMLImageElement).src = fallbackImage;
    // update the allImages array so clicking selects fallback
    const imgs = [...allImages.value];
    imgs[index] = fallbackImage;
    allImages.value = imgs;
  };
}
</script>
