<template>
  <div class="relative ml-6 mb-4">
    <div class="flex items-center border border-gray-300 rounded-full px-3 py-1.5 bg-white">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="11" cy="11" r="8"/>
        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
      </svg>

      <input
        v-model="search"
        type="text"
        placeholder="Search products..."
        class="w-full outline-none pl-2"
      />
    </div>
  </div>

  <div v-if="products.length === 0" class="text-gray-500 text-center mt-6">
    No products found.
  </div>

  <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mt-6">
    <div v-for="product in products" :key="product.id" class="border p-4 rounded shadow hover:shadow-lg transition">
      <img :src="product.image" class="h-40 w-full object-cover rounded" alt="Product Image" />
      <h3 class="font-bold mt-2 text-lg">{{ product.name }}</h3>
      <p class="text-gray-500 text-sm">{{ product.category_name }}</p>
      <p class="text-red-500 font-semibold mt-1">${{ product.price.toFixed(2) }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import axios from "axios";
import { ref, watch, onMounted } from "vue";

interface Product {
  id: number;
  name: string;
  price: number;
  image: string;
  category_name: string;
}

const search = ref<string>("");
const products = ref<Product[]>([]);

const fetchProducts = async () => {
  try {
    const res = await fetch(
      `http://localhost/Eshop/Backend/api/product/search_product.php?search=${encodeURIComponent(search.value)}`
    );
    const data = await res.json();

    if (data.success) {
      products.value = data.products;
    } else {
      products.value = [];
      console.error("API Error:", data.message);
    }
  } catch (error) {
    console.error("Fetch error:", error);
    products.value = [];
  }
};

// Watch search input and fetch products dynamically
watch(search, () => {
  fetchProducts();
});

// Fetch all products initially
onMounted(() => {
  fetchProducts();
});
</script>
