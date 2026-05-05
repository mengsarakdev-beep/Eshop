<template>
  <div
    class="min-h-screen flex flex-col bg-slate-50 text-slate-900 transition-colors dark:bg-slate-950 dark:text-slate-100">
    <Header v-if="!hideLayout" />

    <main class="container mx-auto flex-1 px-1.5 py-8 transition-colors">
      <router-view />

    </main>

    <FooterLink v-if="!hideLayout" />
  </div>
</template>

<script setup lang="ts">
import { onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useWishlistStore } from './store/wishlistStore'

import Header from "./components/myHeader.vue";
import FooterLink from "./components/FooterLink.vue";


const store = useWishlistStore()
const route = useRoute()

// Computed property to detect meta.hideLayout
const hideLayout = computed(() => {
  return route.meta.hideLayout === true
})

onMounted(async () => {
  if (!store.loaded) {
    await store.loadWishlist()
  }
})
</script>
