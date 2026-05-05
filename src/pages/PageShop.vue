<template>
  <div class="min-h-screen bg-slate-50 text-slate-900 transition-colors dark:bg-slate-950 dark:text-slate-100">
    <MyHeader @search-changed="handleSearch" />
    <Category @change-category="handleCategory" />

    <div class="mx-auto max-w-7xl p-0">
      <div class="grid grid-cols-2 gap-2 md:grid-cols-5 md:gap-4">
        <ProductCart v-for="p in paginatedProducts" :key="p.id" :product="p" @add-to-cart="handleAddToCart"
          @open-detail="() => openProductDetail(p)" />
      </div>
    </div>

    <div class="mx-auto max-w-7xl px-1 py-3 mt-5">
      <div
        class="flex dark:bg-slate-950 flex-wrap items-center justify-between gap-2 rounded-xl bg-white/90 p-1 shadow-sm sm:justify-center">
        <button @click="setPage(currentPage - 1)" :disabled="currentPage === 1"
          class="rounded-lg border text-white bg-red-700 border-slate-300 px-1.5 py-2 text-sm transition disabled:cursor-not-allowed disabled:opacity-50">
          Previous
        </button>

        <button v-for="page in totalPages" :key="page" @click="setPage(page)"
          :class="currentPage === page ? 'bg-slate-900 text-white' : 'bg-white text-slate-900 hover:bg-slate-100'"
          class="rounded-lg border border-slate-200  px-4 py-2 text-sm font-semibold transition">
          {{ page }}
        </button>

        <button @click="setPage(currentPage + 1)" :disabled="currentPage === totalPages"
          class="rounded-lg border bg-blue-700 text-white border-slate-300 px-4 py-2 text-sm  transition disabled:cursor-not-allowed disabled:opacity-50">
          Next
        </button>

      </div>
      <span class="w-full block text-center text-sm text-slate-500 sm:w-auto mt-1">
        Page {{ currentPage }} of {{ totalPages }}
      </span>
    </div>

    <!-- Product Detail Modal -->
    <ProductDetail v-if="selectedProduct" :show="showDetail" :product="selectedProduct" @close="closeProductDetail"
      @add-to-cart="handleAddToCart" />
  </div>
</template>

<script setup lang="ts">
import { computed, ref, watch, onMounted } from "vue";
import { useRoute } from "vue-router";
import type { Product } from "../types";
import MyHeader from "../components/myHeader.vue";
import Category from "../components/Category.vue";
import ProductCart from "../components/ProductCart.vue";
import ProductDetail from "../components/ProductDetail.vue";

// ---------------------
// State
// ---------------------
const route = useRoute();
const products = ref<Product[]>([]);
const currentPage = ref(1);
const perPage = 12;
const selectedProduct = ref<Product | null>(null);
const showDetail = ref(false);
const search = ref("");
const selectedCategory = ref<number | null>(null);

const totalPages = computed(() => Math.max(1, Math.ceil(products.value.length / perPage)));
const paginatedProducts = computed(() => {
  const start = (currentPage.value - 1) * perPage;
  return products.value.slice(start, start + perPage);
});

// ---------------------
// Fetch products list
// ---------------------
const fetchProducts = async () => {
  try {
    let url = `http://localhost/Eshop/Backend/api/product/search_product.php?search=${encodeURIComponent(
      search.value
    )}`;
    if (selectedCategory.value) url += `&category_id=${selectedCategory.value}`;

    const res = await fetch(url);
    const data = await res.json();

    if (data.success && Array.isArray(data.products)) {
      // Ensure each product has a proper main image
      products.value = data.products.map((p: any) => ({
        ...p,
        image: p.image || "http://localhost/Eshop/Backend/uploads/default.png",
      }));
    } else {
      products.value = [];
    }
  } catch (error) {
    console.error("Fetch error:", error);
    products.value = [];
  }
};

// ---------------------
// Search & Category handlers
// ---------------------
const handleSearch = (value: string) => {
  currentPage.value = 1;
  search.value = value;
};
const handleCategory = (categoryId: number | null) => {
  currentPage.value = 1;
  selectedCategory.value = categoryId;
};

// ---------------------
// Watchers
// ---------------------
watch([search, selectedCategory], fetchProducts);
watch(products, () => {
  if (currentPage.value > totalPages.value) {
    currentPage.value = totalPages.value;
  }
});

// ---------------------
// Open product detail modal
// ---------------------
const openProductDetail = async (product: Product) => {
  try {
    console.log('Opening product detail for:', product);
    // Fetch full product detail from backend
    const res = await fetch(
      `http://localhost/Eshop/Backend/api/product/get_product_detail.php?id=${product.id}`
    );
    const data = await res.json();
    console.log('Fetched data:', data);
    if (data.success && data.product) {
      // Ensure images array has full URLs
      const images = (data.product.images ?? []).map((img: string) =>
        img.startsWith("http") ? img : `http://localhost/Eshop/Backend/uploads/${img}`
      );

      selectedProduct.value = {
        ...data.product,
        image: images[0] ?? data.product.image,
        images,
      };
      showDetail.value = true;
      console.log('Set selectedProduct:', selectedProduct.value);
      console.log('Set showDetail:', showDetail.value);
    } else {
      alert(data.message || "Failed to load product details");
    }
  } catch (error) {
    console.error("Error fetching product detail:", error);
    alert("Failed to load product details");
  }
};

// ---------------------
// Close modal
// ---------------------
const closeProductDetail = () => {
  selectedProduct.value = null;
  showDetail.value = false;
};

const setPage = (page: number) => {
  if (page < 1 || page > totalPages.value) return;
  currentPage.value = page;
  window.scrollTo({ top: 0, behavior: "smooth" });
};

// ---------------------
// Add to cart
// ---------------------
const handleAddToCart = (product: Product) => {
  console.log("Add to cart:", product);
};

// ---------------------
// Handle route search query
// ---------------------
onMounted(() => {
  if (route.query.search) {
    search.value = String(route.query.search);
  }
  fetchProducts();
});

watch(
  () => route.query.search,
  (newSearch) => {
    search.value = String(newSearch || "");
    fetchProducts();
  }
);
</script>
