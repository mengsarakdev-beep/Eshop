<template>
  <div
    class="min-h-screen bg-gradient-to-br  from-slate-50 via-blue-50 to-indigo-50 md:px-3 px-0 md:py-4 py-0 sm:px-6 sm:py-8 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">
    <div class="mx-auto w-full max-w-full space-y-8">
      <!-- Header Section -->
      <div class="relative overflow-hidden rounded-md p-6 sm:p-8 text-white border w-full max-w-full">

        <div
          class="relative text-gray-700 flex dark:text-white md:gap-4 gap-2 sm:flex-row sm:items-center sm:justify-between">
          <div class="flex items-center md:gap-4 gap-2">

            <div>
              <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">Product</h1>
            </div>
          </div>
          <button
            class="md:w-full text-xs md:text-lg sm:w-auto group inline-flex items-center justify-center gap-3 rounded-md bg-blue-700 hover:bg-blue-800 md:px-6 p-2 md:py-3 font-semibold text-white shadow-lg transition-all  hover:shadow-xl disabled:opacity-50"
            @click="openAddModal" :disabled="isLoading">
            <i class="fa-solid fa-plus md:text-lg text-xs transition-transform group-hover:scale-110"></i>
            Add Product
          </button>
        </div>
        <div v-if="isLoading"
          class="absolute top-4 right-4 h-6 w-6 animate-spin rounded-full border-2 border-white/30 border-t-white">
        </div>
      </div>

      <!-- Alert Messages -->
      <div v-if="errorMessage" class="animate-fade-in">
        <div
          class="rounded-xl bg-gradient-to-r from-red-500 to-rose-500 p-4 text-white shadow-lg dark:from-red-900 dark:to-rose-900">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <i class="fa-solid fa-exclamation-triangle text-xl"></i>
              <span class="font-medium">{{ errorMessage }}</span>
            </div>
            <button @click="errorMessage = ''" class="text-red-100 hover:text-white transition-colors">
              <i class="fa-solid fa-times"></i>
            </button>
          </div>
        </div>
      </div>

      <div v-if="successMessage" class="animate-fade-in">
        <div
          class="rounded-xl bg-gradient-to-r from-green-500 to-emerald-500 p-4 text-white shadow-lg dark:from-green-900 dark:to-emerald-900">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <i class="fa-solid fa-check-circle text-xl"></i>
              <span class="font-medium">{{ successMessage }}</span>
            </div>
            <button @click="successMessage = ''" class="text-green-100 hover:text-white transition-colors">
              <i class="fa-solid fa-times"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Products Table Card -->
      <div
        class="rounded-md bg-white md:p-4 sm:p-8 shadow-xl border border-slate-200/60 dark:bg-slate-950 dark:border-slate-700">
        <!-- Search and Filters -->
        <div class="mb-4 flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between w-full">
          <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
            <div class="relative">
              <i
                class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500"></i>
              <input v-model="search" type="text" placeholder="Search products..."
                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-10 py-3 outline-none transition-all focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:placeholder-slate-400 dark:focus:border-indigo-500 sm:w-80" />
            </div>
            <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
              <i class="fa-solid fa-list"></i>
              <span>{{ filteredProducts.length }} of {{ products.length }} products</span>
            </div>
          </div>

          <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <label class="text-sm font-medium text-slate-700 dark:text-slate-300 hidden sm:inline" for="pageSize">Show
              per page:</label>
            <select id="pageSize" v-model.number="pageSize"
              class="w-full sm:w-auto rounded-lg border border-slate-200 bg-white px-3 py-2 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 text-sm">
              <option v-for="n in [5, 10, 20, 50]" :key="n" :value="n">{{ n }}</option>
            </select>
          </div>
        </div>

        <!-- Products Table -->
        <div class="overflow-hidden rounded-md bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
          <!-- Mobile Card View -->
          <!-- Mobile View : Small Compact Row + Column Style -->
          <div v-if="isMobileView" class="w-full space-y-1">
            <div
              class="grid grid-cols-12 gap-2 px-2 py-2 text-[11px] uppercase tracking-[0.12em] text-slate-500 dark:text-slate-400">
              <div class="col-span-2 font-semibold">ID</div>
              <div class="col-span-4 font-semibold">Name</div>
              <div class="col-span-2 text-center font-semibold">Price</div>
              <div class="col-span-2 text-center font-semibold">Stock</div>
              <div class="col-span-2 text-right font-semibold">Actions</div>
            </div>

            <div v-for="product in paginatedProducts" :key="product.id"
              class="border-b border-slate-200 py-2 px-2 dark:border-slate-700">

              <!-- Row Layout -->
              <div class="grid grid-cols-12 items-center gap-2">

                <!-- Image -->
                <div class="col-span-2">
                  <img :src="product.image || 'https://via.placeholder.com/50'" :alt="product.name"
                    class="w-12 h-12 rounded object-contain" @error="handleImageError" />
                </div>

                <!-- Product Name -->
                <div class="col-span-4 min-w-0">
                  <h3 class="text-sm font-medium truncate text-slate-800 dark:text-white">
                    {{ product.name }}
                  </h3>
                  <p class="text-xs text-slate-500 truncate">
                    {{ product.category_name }}
                  </p>
                </div>

                <!-- Price -->
                <div class="col-span-2 text-center">
                  <p class="text-sm font-semibold text-emerald-600">
                    ${{ product.price }}
                  </p>
                </div>

                <!-- Stock -->
                <div class="col-span-2 text-center">
                  <span :class="product.stock > 10
                    ? 'text-green-600'
                    : product.stock > 0
                      ? 'text-yellow-600'
                      : 'text-red-600'" class="text-xs font-medium">
                    {{ product.stock }}
                  </span>
                </div>

                <!-- Action -->
                <div class="col-span-2 text-right">
                  <button @click="toggleProductActions(product.id)" class="w-8 h-8 rounded dark:bg-slate-800">
                    <i :class="expandedProductId === product.id
                      ? 'fa-solid fa-chevron-up'
                      : 'fa-solid fa-ellipsis-vertical'" class="text-xs"></i>
                  </button>
                </div>

              </div>

              <!-- Expand Actions -->
              <div v-if="expandedProductId === product.id" class="flex justify-end gap-2 mt-2">
                <button @click="openViewModal(product)"
                  class="px-3 py-2 rounded bg-slate-100 text-xs flex items-center gap-2 dark:bg-slate-800">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5
         c4.478 0 8.268 2.943 9.542 7
         -1.274 4.057-5.064 7-9.542 7
         -4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  View
                </button>

                <button @click="openEditModal(product)"
                  class="px-3 py-2 rounded bg-blue-500 text-white text-xs flex items-center gap-2">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11
         a2 2 0 002 2h11a2 2 0 002-2v-5" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1
         1-4 9.5-9.5z" />
                  </svg>
                  Edit
                </button>

                <button @click="openDeleteModal(product)"
                  class="px-3 py-2 rounded bg-red-500 text-white text-xs flex items-center gap-2">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862
         a2 2 0 01-1.995-1.858L5 7" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 11v6M14 11v6M9 7V4
         a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16" />
                  </svg>
                  Delete
                </button>
              </div>

            </div>

          </div>

          <!-- Desktop Table View -->
          <div v-else class="overflow-x-auto w-full">
            <table class="w-full min-w-full sm:min-w-[900px] table-auto">
              <thead class=" bg-green-400 dark:from-slate-800 dark:to-slate-900">
                <tr>
                  <th
                    class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                    ID</th>
                  <th
                    class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                    Image
                  </th>
                  <th
                    class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                    Product
                  </th>
                  <th
                    class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                    Price
                  </th>
                  <th
                    class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                    Stock
                  </th>
                  <th
                    class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                    Category
                  </th>
                  <th
                    class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                <tr v-for="product in paginatedProducts" :key="product.id"
                  class="group transition-all hover:bg-green-100 hover:from-indigo-50 hover:to-blue-50 dark:hover:from-slate-800 dark:hover:to-slate-800">
                  <td class="px-6 py-4">
                    <span
                      class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-800">
                      {{ product.id }}
                    </span>
                  </td>
                  <td class="px-5 py-2.5">
                    <div class="relative">
                      <img :key="product.image" :src="product.image || 'https://via.placeholder.com/64?text=No+Image'"
                        :alt="product.name || 'Product Image'"
                        class="h-12 w-12 rounded-sm object-contain shadow-sm ring-2 ring-slate-200 group-hover:ring-indigo-300 transition-all"
                        @error="handleImageError" />
                      <div class="absolute -top-1 -right-1 h-2 w-2 rounded-full bg-green-400 ring-2 ring-white"></div>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="flex items-center">
                      <div>
                        <div class="dark:text-white text-sm font-semibold text-slate-900">{{ product.name }}</div>
                        <div class="text-xs text-slate-500">Product ID: {{ product.id }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <span class="text-md font-semibold text-emerald-600">{{ product.price }}</span>
                  </td>
                  <td class="px-6 py-4">
                    <span
                      :class="product.stock > 10 ? 'text-green-600' : product.stock > 0 ? 'text-amber-600' : 'text-red-600'"
                      class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-xs font-medium">
                      <i
                        :class="product.stock > 10 ? 'fa-solid fa-circle-check' : product.stock > 0 ? 'fa-solid fa-triangle-exclamation' : 'fa-solid fa-circle-xmark'"></i>
                      {{ product.stock }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <span
                      class="inline-flex items-center rounded-sm bg-indigo-100 px-3 py-1 text-xs font-medium text-indigo-800">
                      <i class="fa-solid fa-tag mr-1"></i>
                      {{ product.category_name }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                      <button @click="openViewModal(product)"
                        class="inline-flex items-center gap-2 rounded-sm bg-slate-100 md:px-3 md:py-2 text-xs font-medium text-slate-700 transition-all hover:bg-slate-200 hover:shadow-sm">
                        <i class="fa-solid fa-eye"></i>
                        View
                      </button>
                      <button @click="openEditModal(product)"
                        class="inline-flex items-center gap-2 rounded-sm bg-blue-500 px-3 py-2 text-xs font-medium text-white transition-all hover:bg-blue-600 hover:shadow-sm">
                        <i class="fa-solid fa-pen-to-square"></i>
                        Edit
                      </button>
                      <button @click="openDeleteModal(product)"
                        class="inline-flex items-center gap-2 rounded-sm bg-red-500 px-3 py-2 text-xs font-medium text-white transition-all hover:bg-red-600 hover:shadow-sm">
                        <i class="fa-solid fa-trash"></i>
                        Delete
                      </button>
                    </div>
                  </td>
                </tr>
                <tr v-if="paginatedProducts.length === 0">
                  <td class="px-6 py-12 text-center text-slate-500" colspan="7">
                    <div class="flex flex-col items-center gap-3">
                      <i class="fa-solid fa-box-open text-4xl text-slate-300"></i>
                      <p class="text-lg font-medium">No products found</p>
                      <p class="text-sm text-slate-400">Try adjusting your search or add a new product</p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Pagination -->
        <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <div class="text-sm text-slate-600">
            <span class="font-medium text-slate-900">{{ filteredProducts.length }}</span> products found
            <span class="text-slate-400">•</span>
            Showing <span class="font-medium text-slate-900">{{ startIndex + 1 }}</span> to
            <span class="font-medium text-slate-900">{{ endIndex }}</span> of
            <span class="font-medium text-slate-900">{{ filteredProducts.length }}</span> entries
          </div>

          <div class="flex flex-wrap items-center gap-2 justify-center sm:justify-end">
            <button
              class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-all hover:border-slate-300 hover:bg-slate-50 hover:shadow-sm disabled:cursor-not-allowed disabled:opacity-50"
              :disabled="currentPage === 1" @click="prevPage">
              <i class="fa-solid fa-chevron-left"></i>
              Previous
            </button>

            <div class="flex items-center gap-1">
              <button v-for="page in pageCount" :key="page"
                class="rounded-lg px-4 py-2.5 text-sm font-medium transition-all" :class="page === currentPage
                  ? 'bg-indigo-600 text-white shadow-lg'
                  : 'border border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50'"
                @click="currentPage = page">
                {{ page }}
              </button>
            </div>

            <button
              class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-all hover:border-slate-300 hover:bg-slate-50 hover:shadow-sm disabled:cursor-not-allowed disabled:opacity-50"
              :disabled="currentPage === pageCount" @click="nextPage">
              Next
              <i class="fa-solid fa-chevron-right"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add Product Modal -->
  <div v-if="isAddModalOpen"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div
      class="w-full max-w-xl sm:max-w-4xl rounded-md bg-white shadow-2xl border border-slate-200/60 max-h-[90vh] overflow-hidden dark:bg-slate-950 dark:border-slate-700">
      <!-- Modal Header -->
      <div
        class="relative bg-gradient-to-r from-emerald-600 to-teal-600 px-8 py-6 text-white dark:from-emerald-900 dark:to-teal-900">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-md bg-white/20 backdrop-blur-sm">
              <i class="fa-solid fa-plus text-xl"></i>
            </div>
            <div>
              <h3 class="text-2xl font-bold">Add New Product</h3>
              <p class="text-emerald-100">Create a new product in your catalog</p>
            </div>
          </div>
          <button
            class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 text-white transition hover:bg-white/30 backdrop-blur-sm"
            @click="closeAddModal">
            <i class="fa-solid fa-times text-xl"></i>
          </button>
        </div>
      </div>

      <!-- Modal Content -->
      <div class="p-4 sm:p-8 dark:bg-slate-900">
        <div class="grid gap-6 sm:grid-cols-1 lg:grid-cols-2">
          <!-- Left Column - Image Upload -->
          <div class="space-y-6">
            <!-- Image Preview -->
            <div class="rounded-md bg-gradient-to-br from-slate-50 to-slate-100 p-6">
              <h3 class="mb-4 text-lg font-semibold text-slate-800">Product Image</h3>
              <div class="flex flex-col items-center">
                <div class="relative">
                  <img :key="form.image" :src="form.image || 'https://via.placeholder.com/200?text=No+Image'"
                    :alt="form.name || 'Product'"
                    class="h-48 w-48 rounded-xl object-contain shadow-lg ring-4 ring-white" @error="handleImageError" />
                  <div v-if="form.image" class="absolute -top-2 -right-2">
                    <button
                      class="flex h-8 w-8 items-center justify-center rounded-md bg-red-500 text-white transition hover:bg-red-600"
                      @click="clearImage">
                      <i class="fa-solid fa-trash text-sm"></i>
                    </button>
                  </div>
                </div>
                <p class="mt-3 text-sm text-slate-600">Upload a main product image</p>
              </div>
            </div>

            <!-- Image Upload Section -->
            <div class="rounded-md bg-gradient-to-br from-blue-50 to-indigo-50 p-6">
              <h3 class="mb-4 text-lg font-semibold text-slate-800">Upload Images</h3>
              <div class="space-y-4">
                <!-- Main Image Upload -->
                <div>
                  <label class="mb-2 block text-sm font-medium text-slate-700">Main Image</label>
                  <div class="relative">
                    <input ref="addImageInput" type="file" accept="image/*" multiple
                      class="absolute inset-0 h-full w-full cursor-pointer opacity-0" @change="handleImageUpload" />
                    <div
                      class="flex items-center justify-center rounded-lg border-2 border-dashed border-blue-300 bg-blue-50/50 p-4 transition hover:bg-blue-50">
                      <div class="text-center">
                        <i class="fa-solid fa-cloud-upload-alt mb-2 text-2xl text-blue-500"></i>
                        <p class="text-sm text-blue-700">Click to upload main image</p>
                        <p class="text-xs text-blue-500">JPG, PNG, GIF, WebP (multiple allowed)</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column - Form Fields -->
          <div class="space-y-6 ">
            <!-- Basic Information -->
            <div class="rounded-md bg-gradient-to-br from-emerald-50 to-teal-50 p-6">
              <h3 class="mb-4 text-lg font-semibold text-slate-800">Basic Information</h3>
              <div class="space-y-4">
                <div>
                  <label class="mb-2 block text-sm font-medium text-slate-700">Product Name *</label>
                  <input v-model="form.name" type="text" placeholder="Enter product name"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none" />
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                  <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Price *</label>
                    <input v-model="form.price" type="text" placeholder="e.g., $99.99"
                      class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none" />
                  </div>
                  <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Stock *</label>
                    <input v-model.number="form.stock" type="number" placeholder="0"
                      class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none" />
                  </div>
                </div>

                <div>
                  <label class="mb-2 block text-sm font-medium text-slate-700">Category *</label>
                  <select v-model.number="form.category_id"
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                    <option value="0" disabled>Select a category</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                      {{ category.name }}
                    </option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Description -->
            <div class="rounded-md bg-gradient-to-br from-purple-50 to-pink-50 p-6">
              <h3 class="mb-4 text-lg font-semibold text-slate-800">Description</h3>
              <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Product Description</label>
                <textarea v-model="form.description" placeholder="Enter detailed product description"
                  class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm transition focus:border-purple-500 focus:ring-2 focus:ring-purple-200 outline-none resize-none"
                  rows="4"></textarea>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 flex flex-col gap-4 border-t border-slate-200 pt-6 sm:flex-row sm:justify-end">
          <button
            class="rounded-xl border border-slate-300 bg-white px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 hover:shadow-md inline-flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
            @click="closeAddModal" :disabled="isLoading">
            <i class="fa-solid fa-times"></i>
            Cancel
          </button>
          <button
            class="rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 px-8 py-3 text-sm font-semibold text-white transition hover:from-emerald-600 hover:to-teal-600 hover:shadow-lg inline-flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
            @click="addProduct" :disabled="isLoading || !form.name || !form.price || form.category_id === 0">
            <i :class="isLoading ? 'fa-solid fa-spinner fa-spin' : 'fa-solid fa-plus'"></i>
            {{ isLoading ? 'Creating...' : 'Create Product' }}
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Product Modal -->
  <div v-if="isEditModalOpen"
    class="fixed inset-0 z-50 flex items-end md:items-center justify-center bg-black/50 backdrop-blur-sm p-0 md:p-4">
    <div
      class="w-full h-full md:h-auto md:max-h-[90vh] md:max-w-5xl overflow-y-auto bg-white md:rounded-2xl shadow-2xl dark:bg-slate-900">

      <!-- Header -->
      <div class="bg-gradient-to-r from-amber-500 via-orange-500 to-red-500 px-3 py-3 md:px-6 md:py-5 md:rounded-t-2xl">
        <div class="flex items-center justify-between">

          <div class="flex items-center gap-2 md:gap-3">
            <div class="flex h-9 w-9 md:h-12 md:w-12 items-center justify-center rounded-lg bg-white/20">
              <i class="fa-solid fa-pen-to-square text-white text-sm md:text-2xl"></i>
            </div>

            <div>
              <h2 class="text-sm md:text-2xl font-bold text-white">
                Edit Product
              </h2>
              <p class="text-[11px] md:text-sm text-amber-100">
                Update product information and images
              </p>
            </div>
          </div>

          <button @click="closeEditModal"
            class="flex h-8 w-8 md:h-10 md:w-10 items-center justify-center rounded-md bg-white/20 text-white">
            <i class="fa-solid fa-times text-sm md:text-lg"></i>
          </button>

        </div>
      </div>

      <!-- Content -->
      <div class="p-2 md:p-6 dark:bg-slate-900">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-6">

          <!-- LEFT -->
          <div class="space-y-3">

            <!-- Current Image -->
            <div class="rounded-lg bg-slate-50 p-3 md:p-4 dark:bg-slate-800">
              <h3 class="mb-2 text-sm md:text-lg font-semibold dark:text-white">
                Current Image
              </h3>

              <div class="flex flex-col items-center">

                <div class="relative">
                  <img :key="form.image" :src="form.image || 'https://via.placeholder.com/200?text=No+Image'"
                    :alt="form.name || 'Product'"
                    class="w-28 h-28 md:w-48 md:h-48 rounded-xl object-contain border shadow"
                    @error="handleImageError" />

                  <button v-if="form.image" @click="clearImage"
                    class="absolute -top-2 -right-2 flex h-7 w-7 md:h-8 md:w-8 items-center justify-center rounded-full bg-red-500 text-white">
                    <i class="fa-solid fa-trash text-xs"></i>
                  </button>
                </div>

                <p class="mt-2 text-xs md:text-sm text-slate-500">
                  Main Product Image
                </p>

              </div>
            </div>

            <!-- Upload -->
            <div class="rounded-lg bg-slate-50 p-3 md:p-4 dark:bg-slate-800">
              <h3 class="mb-2 text-sm md:text-lg font-semibold dark:text-white">
                Upload Images
              </h3>

              <div class="space-y-3">

                <!-- Main -->
                <div>
                  <label class="text-xs md:text-sm font-medium text-slate-600 dark:text-white">
                    Change Main Image
                  </label>

                  <div class="relative mt-1">
                    <input ref="editImageInput" type="file" accept="image/*"
                      class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" @change="handleImageUploadEdit" />

                    <div
                      class="rounded-lg border-2 border-dashed border-blue-300 bg-blue-50 p-3 md:p-4 text-center dark:bg-slate-700">
                      <i class="fa-solid fa-cloud-upload-alt text-blue-500 text-lg"></i>
                      <p class="text-xs md:text-sm mt-1">
                        Upload Main Image
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Additional -->
                <div>
                  <label class="text-xs md:text-sm font-medium text-slate-600 dark:text-white">
                    Additional Images
                  </label>

                  <div class="relative mt-1">
                    <input ref="editAdditionalImagesInput" type="file" multiple accept="image/*"
                      class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                      @change="handleAdditionalImagesUploadEdit" />

                    <div
                      class="rounded-lg border-2 border-dashed border-indigo-300 bg-indigo-50 p-3 md:p-4 text-center dark:bg-slate-700">
                      <i class="fa-solid fa-images text-indigo-500 text-lg"></i>
                      <p class="text-xs md:text-sm mt-1">
                        Upload More Images
                      </p>
                    </div>
                  </div>
                </div>

              </div>
            </div>

          </div>

          <!-- RIGHT -->
          <div class="space-y-3">

            <!-- Basic Info -->
            <div class="rounded-lg bg-emerald-50 p-3 md:p-5 dark:bg-slate-800">
              <h3 class="mb-3 text-sm md:text-lg font-semibold dark:text-white">
                Basic Information
              </h3>

              <div class="space-y-3">

                <div>
                  <label class="text-xs md:text-sm font-medium">
                    Product Name
                  </label>
                  <input v-model="form.name" type="text" placeholder="Enter product name"
                    class="w-full rounded-lg border px-3 py-2 md:px-4 md:py-3 text-sm outline-none mt-1" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                  <div>
                    <label class="text-xs md:text-sm font-medium">
                      Price
                    </label>
                    <input v-model="form.price" type="text" placeholder="$99.99"
                      class="w-full rounded-lg border px-3 py-2 md:px-4 md:py-3 text-sm outline-none mt-1" />
                  </div>

                  <div>
                    <label class="text-xs md:text-sm font-medium">
                      Stock
                    </label>
                    <input v-model.number="form.stock" type="number" placeholder="0"
                      class="w-full rounded-lg border px-3 py-2 md:px-4 md:py-3 text-sm outline-none mt-1" />
                  </div>

                </div>

                <div>
                  <label class="text-xs md:text-sm font-medium">
                    Category
                  </label>
                  <select v-model.number="form.category_id"
                    class="w-full rounded-lg border px-3 py-2 md:px-4 md:py-3 text-sm outline-none mt-1">
                    <option value="0" disabled>Select category</option>

                    <option v-for="category in categories" :key="category.id" :value="category.id">
                      {{ category.name }}
                    </option>
                  </select>
                </div>

              </div>
            </div>

            <!-- Description -->
            <div class="rounded-lg bg-purple-50 p-3 md:p-5 dark:bg-slate-800">
              <h3 class="mb-3 text-sm md:text-lg font-semibold dark:text-white">
                Description
              </h3>

              <textarea v-model="form.description" rows="4" placeholder="Enter product description"
                class="w-full rounded-lg border px-3 py-2 md:px-4 md:py-3 text-sm resize-none outline-none"></textarea>
            </div>

          </div>

        </div>

        <!-- Footer -->
        <div class="mt-4 border-t pt-4 flex flex-col md:flex-row gap-2 md:justify-end">

          <button @click="closeEditModal" :disabled="isLoading"
            class="w-full md:w-auto rounded-lg border px-5 py-2.5 text-sm font-medium">
            Cancel
          </button>

          <button @click="updateProduct" :disabled="isLoading || !form.name || !form.price || form.category_id === 0"
            class="w-full md:w-auto rounded-lg bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-2.5 text-sm font-medium text-white">
            <i :class="isLoading ? 'fa-solid fa-spinner fa-spin' : 'fa-solid fa-save'" class="mr-2"></i>
            {{ isLoading ? 'Updating...' : 'Update Product' }}
          </button>

        </div>

      </div>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div v-if="isDeleteModalOpen"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-2">
    <div class="w-full max-w-md rounded-md bg-white shadow-2xl">
      <!-- Gradient Header -->
      <div class="bg-gradient-to-r from-red-500 via-rose-500 to-pink-500 p-6 rounded-t-2xl">
        <div class="flex items-center gap-3">
          <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
            <i class="fa-solid fa-exclamation-triangle text-2xl text-white"></i>
          </div>
          <div>
            <h2 class="text-2xl font-bold text-white">Delete Product</h2>
            <p class="text-red-100">This action cannot be undone</p>
          </div>
        </div>
      </div>

      <!-- Modal Content -->
      <div class="p-2 sm:p-6">
        <div class="text-center">
          <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-100 mb-4">
            <i class="fa-solid fa-trash text-2xl text-red-600"></i>
          </div>
          <h3 class="text-lg font-semibold text-slate-800 mb-2">Confirm Deletion</h3>
          <p class="text-sm text-slate-600 mb-6">
            Are you sure you want to delete <strong class="text-slate-800">{{ productToDelete?.name }}</strong>?
            This action cannot be undone and will permanently remove the product from your store.
          </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end gap-2">
          <button
            class="rounded-md border border-slate-300 bg-white px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 hover:shadow-md inline-flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
            @click="closeDeleteModal" :disabled="isLoading">
            <i class="fa-solid fa-times"></i>
            Cancel
          </button>
          <button
            class="rounded-md bg-gradient-to-r from-red-500 to-rose-500 px-6 py-3 text-sm font-semibold text-white transition hover:from-red-600 hover:to-rose-600 hover:shadow-lg inline-flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
            @click="confirmDeleteProduct" :disabled="isLoading">
            <i :class="isLoading ? 'fa-solid fa-spinner fa-spin' : 'fa-solid fa-trash'"></i>
            {{ isLoading ? 'Deleting...' : 'Delete Product' }}
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- View Product Modal -->
  <!-- Clean Small Beautiful Style (Desktop + Mobile Responsive) -->
  <div v-if="isViewModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-2 md:p-4">
    <div
      class="w-full max-w-sm md:max-w-4xl max-h-[95vh] overflow-y-auto rounded-xl bg-white shadow-lg dark:bg-slate-900 border border-slate-200 dark:border-slate-700">

      <!-- Header -->
      <div class="flex items-center justify-between border-b px-4 py-3 md:px-6 md:py-4 dark:border-slate-700">

        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-lg bg-blue-100 flex items-center justify-center">
            <i class="fa-solid fa-eye text-blue-500"></i>
          </div>

          <div>
            <h2 class="text-sm md:text-xl font-semibold dark:text-white">
              Product Details
            </h2>
            <p class="text-xs text-slate-500">
              View product information
            </p>
          </div>
        </div>

        <button @click="closeViewModal" class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800">
          <i class="fa-solid fa-times text-sm dark:text-white"></i>
        </button>

      </div>

      <!-- Content -->
      <div class="p-4 md:p-6">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">

          <!-- Left -->
          <div class="space-y-4">

            <!-- Main Image -->
            <div class="rounded-lg border p-4 dark:border-slate-700">
              <h3 class="text-sm font-medium mb-3 dark:text-white">
                Main Image
              </h3>

              <div class="flex justify-center">
                <img :src="productToView?.image || 'https://via.placeholder.com/200'" :alt="productToView?.name"
                  class="w-28 h-28 md:w-48 md:h-48 rounded-lg object-contain" @error="handleImageError" />
              </div>
            </div>

            <!-- Gallery -->
            <div v-if="productToView?.images && productToView.images.length > 0"
              class="rounded-lg border p-4 dark:border-slate-700">
              <h3 class="text-sm font-medium mb-3 dark:text-white">
                Gallery ({{ productToView.images.length }})
              </h3>

              <div class="grid grid-cols-3 md:grid-cols-4 gap-2">
                <img v-for="(img, index) in productToView.images" :key="index"
                  :src="img || 'https://via.placeholder.com/100'" :alt="'Image ' + (index + 1)"
                  class="w-full h-20 md:h-24 rounded-md object-cover border" @error="handleImageError" />
              </div>
            </div>

          </div>

          <!-- Right -->
          <div class="space-y-4">

            <!-- Info -->
            <div class="rounded-lg border p-4 dark:border-slate-700">
              <h3 class="text-sm font-medium mb-4 dark:text-white">
                Product Information
              </h3>

              <div class="space-y-3 text-sm">

                <div class="flex justify-between">
                  <span class="text-slate-500">Name</span>
                  <span class="font-medium text-right dark:text-white">
                    {{ productToView?.name }}
                  </span>
                </div>

                <div class="flex justify-between">
                  <span class="text-slate-500">Price</span>
                  <span class="font-semibold text-emerald-600">
                    {{ productToView?.price }}
                  </span>
                </div>

                <div class="flex justify-between">
                  <span class="text-slate-500">Stock</span>
                  <span class="dark:text-white">
                    {{ productToView?.stock }}
                  </span>
                </div>

                <div class="flex justify-between">
                  <span class="text-slate-500">Category</span>
                  <span class="dark:text-white">
                    {{ productToView?.category_name }}
                  </span>
                </div>

              </div>
            </div>

            <!-- Description -->
            <div v-if="productToView?.description" class="rounded-lg border p-4 dark:border-slate-700">
              <h3 class="text-sm font-medium mb-2 dark:text-white">
                Description
              </h3>

              <p class="text-sm text-slate-600 dark:text-slate-300 whitespace-pre-wrap">
                {{ productToView?.description }}
              </p>
            </div>

          </div>

        </div>

        <!-- Footer -->
        <div class="mt-6 flex justify-end">
          <button @click="closeViewModal"
            class="w-full md:w-auto rounded-lg bg-blue-500 hover:bg-blue-600 px-6 py-2.5 text-sm font-medium text-white">
            Close
          </button>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import axios from 'axios'
import { computed, onBeforeUnmount, onMounted, reactive, ref, watch } from 'vue'

type Product = {
  id: number
  name: string
  price: string | number
  stock: number
  category_id: number
  category_name: string
  image: string
  images: string[]
  description?: string
}

type Category = {
  id: number
  name: string
}

const API_BASE = 'http://localhost/Eshop/Backend/api/product'
const CATEGORY_API = 'http://localhost/Eshop/Backend/api/category'

const products = ref<Product[]>([])
const categories = ref<Category[]>([])
const isLoading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

const search = ref('')
const pageSize = ref(10)
const currentPage = ref(1)
const isMobileView = ref(false)
const expandedProductId = ref<number | null>(null)

const isAddModalOpen = ref(false)
const isEditModalOpen = ref(false)
const isDeleteModalOpen = ref(false)
const isViewModalOpen = ref(false)

const productToEdit = ref<Product | null>(null)
const productToDelete = ref<Product | null>(null)
const productToView = ref<Product | null>(null)

const form = reactive<{ id?: number; name: string; price: string; stock: number; category_id: number; image: string; images: string[]; description: string }>({
  name: '',
  price: '',
  stock: 0,
  category_id: 0,
  image: '',
  images: [],
  description: '',
})

const addImageInput = ref<HTMLInputElement | null>(null)
const editImageInput = ref<HTMLInputElement | null>(null)
const editAdditionalImagesInput = ref<HTMLInputElement | null>(null)
const isDraggingAdd = ref(false)

const filteredProducts = computed(() => {
  if (!search.value) return products.value
  const term = search.value.toLowerCase()
  return products.value.filter((p) =>
    [p.id.toString(), p.name, p.category_name, p.price].some((field) => field.toString().toLowerCase().includes(term))
  )
})

const pageCount = computed(() => Math.max(1, Math.ceil(filteredProducts.value.length / pageSize.value)))
const startIndex = computed(() => (currentPage.value - 1) * pageSize.value)
const endIndex = computed(() => Math.min(startIndex.value + pageSize.value, filteredProducts.value.length))
const paginatedProducts = computed(() => filteredProducts.value.slice(startIndex.value, endIndex.value))

const updateMobileView = () => {
  isMobileView.value = window.innerWidth <= 768
  if (!isMobileView.value) {
    expandedProductId.value = null
  }
}

function toggleProductActions(productId: number) {
  expandedProductId.value = expandedProductId.value === productId ? null : productId
}

async function fetchProducts() {
  isLoading.value = true
  errorMessage.value = ''
  try {
    const response = await axios.get(`${API_BASE}/get_products.php`)
    interface ApiProduct {
      id: number
      name: string
      price: number | string
      stock: number
      category_id?: number
      category_name?: string
      image?: string
      images?: string[]
      description?: string
    }
    let productList: ApiProduct[] = []

    // Handle different response formats
    if (response.data?.products && Array.isArray(response.data.products)) {
      productList = response.data.products
    } else if (response.data?.data && Array.isArray(response.data.data)) {
      productList = response.data.data
    } else if (Array.isArray(response.data)) {
      productList = response.data
    }

    if (productList.length > 0) {
      products.value = productList.map((p: ApiProduct) => ({
        id: p.id,
        name: p.name,
        price: `$${p.price}`,
        stock: p.stock,
        category_id: p.category_id || 0,
        category_name: p.category_name || 'Uncategorized',
        image: p.image || 'https://via.placeholder.com/64?text=No+Image',
        images: Array.isArray(p.images) ? p.images : [],
        description: p.description || '',
      }))
    }
  } catch (error) {
    console.error('Error fetching products:', error)
    errorMessage.value = 'Failed to load products'
  } finally {
    isLoading.value = false
  }
}

async function fetchCategories() {
  try {
    const response = await axios.get(`${CATEGORY_API}/get_categories.php`)
    if (response.data?.categories && Array.isArray(response.data.categories)) {
      categories.value = response.data.categories
    }
  } catch (error) {
    console.error('Error fetching categories:', error)
  }
}

onMounted(() => {
  updateMobileView()
  window.addEventListener('resize', updateMobileView)
  fetchCategories()
  fetchProducts()
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', updateMobileView)
})

function openAddModal() {
  Object.assign(form, { id: 0, name: '', price: '', stock: 0, category_id: 0, image: '', images: [], description: '' })
  isAddModalOpen.value = true
}

function closeAddModal() {
  isAddModalOpen.value = false
  form.image = ''
  form.images = []
  if (addImageInput.value) {
    addImageInput.value.value = ''
  }
}

function addProduct() {
  saveProduct()
}

function saveProduct() {
  if (!form.name || !form.price || form.category_id === 0) {
    errorMessage.value = 'Please fill in Name, Price, and Category'
    return
  }

  isLoading.value = true
  errorMessage.value = ''
  const priceValue = String(form.price).replace(/\$/g, '')

  const formData = new FormData()
  formData.append('name', form.name)
  formData.append('price', priceValue)
  formData.append('stock', form.stock?.toString() || '0')
  formData.append('category_id', form.category_id.toString())
  formData.append('description', form.description || '')

  if (addImageInput.value?.files && addImageInput.value.files.length > 0) {
    for (let i = 0; i < addImageInput.value.files.length; i++) {
      const file = addImageInput.value.files[i]
      if (!file) continue
      formData.append('images[]', file)
    }
  }

  axios.post(`${API_BASE}/add_product.php`, formData, {
    headers: { 'Content-Type': 'multipart/form-data' },
  })
    .then(() => {
      successMessage.value = `Product "${form.name}" added successfully!`
      closeAddModal()
      fetchProducts()
      setTimeout(() => { successMessage.value = '' }, 3000)
    })
    .catch((error) => {
      console.error('Error adding product:', error)
      errorMessage.value = error.response?.data?.message || 'Failed to add product'
    })
    .finally(() => {
      isLoading.value = false
    })
}

function openEditModal(product: Product) {
  productToEdit.value = product
  Object.assign(form, {
    id: product.id,
    name: product.name,
    price: product.price,
    stock: product.stock,
    category_id: product.category_id || 0,
    image: product.image || '',
    images: [],
    description: product.description || '',
  })
  isEditModalOpen.value = true
}

function closeEditModal() {
  isEditModalOpen.value = false
  productToEdit.value = null
  if (editImageInput.value) {
    editImageInput.value.value = ''
  }
}

function openViewModal(product: Product) {
  isLoading.value = true
  axios.get(`${API_BASE}/get_product_detail.php?id=${product.id}`)
    .then((response) => {
      if (response.data?.success && response.data?.product) {
        productToView.value = {
          ...response.data.product,
          price: `$${response.data.product.price}`,
          images: Array.isArray(response.data.product.images) ? response.data.product.images : [],
        }
        isViewModalOpen.value = true
      }
    })
    .catch((error) => {
      console.error('Error fetching product detail:', error)
      productToView.value = product
      isViewModalOpen.value = true
    })
    .finally(() => {
      isLoading.value = false
    })
}

function closeViewModal() {
  isViewModalOpen.value = false
  productToView.value = null
}

function handleImageError(event: Event) {
  const img = event.target as HTMLImageElement
  img.src = 'https://via.placeholder.com/200?text=No+Image'
}

function handleDropAdd(event: DragEvent) {
  event.preventDefault()
  isDraggingAdd.value = false
  const files = event.dataTransfer?.files
  if (!files) return
  processFilesAdd(files)
}

function handleImageUploadAdd(event: Event) {
  const input = event.target as HTMLInputElement
  const files = input.files
  if (!files) return
  processFilesAdd(files)
}

function processFilesAdd(files: FileList) {
  Array.from(files).forEach((file) => {
    if (!file.type.startsWith('image/')) return
    const reader = new FileReader()
    reader.onload = (e) => {
      const imageData = (e.target?.result as string) || ''
      if (!form.images.includes(imageData)) {
        form.images.push(imageData)
        form.image = imageData // Set the first image as the main image
      }
    }
    reader.readAsDataURL(file)
  })
}

function removeImageAdd(index: number) {
  form.images.splice(index, 1)
  if (form.image === undefined || form.images.length === 0) {
    form.image = form.images[0] || ''
  }
}

function handleImageUpload(event: Event) {
  const input = event.target as HTMLInputElement
  const file = input.files ? input.files[0] : null
  if (!file) return

  const reader = new FileReader()
  reader.onload = (e) => {
    form.image = (e.target?.result as string) || ''
  }
  reader.readAsDataURL(file)
}

function handleImageUploadEdit(event: Event) {
  const input = event.target as HTMLInputElement
  const files = input.files
  if (!files) return
  // For edit, we just need the files to be sent to backend via the formData
  // The actual form.images array is only for add modal preview
}

function handleAdditionalImagesUploadEdit(event: Event) {
  const input = event.target as HTMLInputElement
  const files = input.files
  if (!files) return
  // For edit, additional images are handled in the updateProduct method
  // via the editAdditionalImagesInput ref
}

function clearImage() {
  form.image = ''
  if (addImageInput.value) {
    addImageInput.value.value = ''
  }
  if (editImageInput.value) {
    editImageInput.value.value = ''
  }
}

function updateProduct() {
  if (!productToEdit.value || !form.name || !form.price || form.category_id === 0) {
    errorMessage.value = 'Please fill in Name, Price, and Category'
    return
  }

  isLoading.value = true
  errorMessage.value = ''
  const priceValue = String(form.price).replace(/\$/g, '')

  const formData = new FormData()
  formData.append('id', productToEdit.value.id.toString())
  formData.append('name', form.name)
  formData.append('price', priceValue)
  formData.append('stock', form.stock?.toString() || '0')
  formData.append('category_id', form.category_id.toString())
  formData.append('description', form.description || '')

  if (editImageInput.value?.files && editImageInput.value.files.length > 0) {
    for (let i = 0; i < editImageInput.value.files.length; i++) {
      const file = editImageInput.value.files[i]
      if (!file) continue
      formData.append('images[]', file)
    }
  }

  // Handle additional images
  if (editAdditionalImagesInput.value?.files && editAdditionalImagesInput.value.files.length > 0) {
    for (let i = 0; i < editAdditionalImagesInput.value.files.length; i++) {
      const file = editAdditionalImagesInput.value.files[i]
      if (!file) continue
      formData.append('additional_images[]', file)
    }
  }

  axios.post(`${API_BASE}/update_product.php`, formData, {
    headers: { 'Content-Type': 'multipart/form-data' },
  })
    .then(() => {
      successMessage.value = `Product "${form.name}" updated successfully!`
      closeEditModal()
      fetchProducts()
      setTimeout(() => { successMessage.value = '' }, 3000)
    })
    .catch((error) => {
      console.error('Error updating product:', error)
      errorMessage.value = error.response?.data?.message || 'Failed to update product'
    })
    .finally(() => {
      isLoading.value = false
    })
}

function openDeleteModal(product: Product) {
  productToDelete.value = product
  isDeleteModalOpen.value = true
}

function closeDeleteModal() {
  isDeleteModalOpen.value = false
  productToDelete.value = null
}

function confirmDeleteProduct() {
  if (!productToDelete.value) return

  isLoading.value = true
  errorMessage.value = ''
  const productName = productToDelete.value.name

  axios.post(`${API_BASE}/delete_product.php`,
    { id: productToDelete.value.id },
    { headers: { 'Content-Type': 'application/json' } }
  )
    .then(() => {
      successMessage.value = `Product "${productName}" deleted successfully!`
      closeDeleteModal()
      fetchProducts()
      setTimeout(() => { successMessage.value = '' }, 3000)
    })
    .catch((error) => {
      console.error('Error deleting product:', error)
      errorMessage.value = error.response?.data?.message || 'Failed to delete product'
    })
    .finally(() => {
      isLoading.value = false
    })
}

function prevPage() {
  if (currentPage.value > 1) currentPage.value -= 1
}

function nextPage() {
  if (currentPage.value < pageCount.value) currentPage.value += 1
}

watch([search, pageSize, filteredProducts], () => {
  if (currentPage.value > pageCount.value) currentPage.value = pageCount.value
  if (currentPage.value < 1) currentPage.value = 1
})
</script>
