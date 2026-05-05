<template>
  <section
    class="mx-auto mt-10 max-w-5xl overflow-hidden rounded bg-white shadow transition-colors dark:bg-slate-900 dark:text-slate-100">
    <div class="flex flex-col md:flex-row">

      <!-- Contact Form (Left) -->
      <div class="md:w-1/2 p-6 space-y-6">
        <h2 class="mb-4 text-center text-2xl font-bold text-gray-800 dark:text-slate-100 md:text-left">Contact Us</h2>

        <div v-if="feedbackMessage" :class="feedbackType === 'success'
          ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
          : 'border-rose-200 bg-rose-50 text-rose-700'" class="rounded-2xl border px-4 py-3 text-sm">
          <div class="flex items-center justify-between">
            <span>{{ feedbackMessage }}</span>
          </div>
        </div>

        <form @submit.prevent="handleSubmit" class="flex flex-col gap-5">

          <!-- Name -->
          <div
            class="flex items-center overflow-hidden rounded-full border border-gray-300 bg-white shadow-sm transition-all hover:shadow-md dark:border-slate-700 dark:bg-slate-800">
            <span
              :class="focusedInput === 'name' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-400 dark:bg-slate-700 dark:text-slate-300'"
              class="material-icons px-4 py-3 flex items-center justify-center transition-colors">
              person
            </span>
            <input type="text" placeholder="Name" v-model="form.name" @focus="focusedInput = 'name'"
              @blur="focusedInput = ''"
              class="flex-1 bg-transparent px-4 py-3 text-slate-800 outline-none focus:ring-2 focus:ring-indigo-500 transition dark:text-slate-100 dark:placeholder:text-slate-400" />
          </div>

          <!-- Email -->
          <div
            class="flex items-center overflow-hidden rounded-full border border-gray-300 bg-white shadow-sm transition-all hover:shadow-md dark:border-slate-700 dark:bg-slate-800">
            <span
              :class="focusedInput === 'email' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-400 dark:bg-slate-700 dark:text-slate-300'"
              class="material-icons px-4 py-3 flex items-center justify-center transition-colors">
              email
            </span>
            <input type="email" placeholder="Email" v-model="form.email" @focus="focusedInput = 'email'"
              @blur="focusedInput = ''"
              class="flex-1 bg-transparent px-4 py-3 text-slate-800 outline-none focus:ring-2 focus:ring-indigo-500 transition dark:text-slate-100 dark:placeholder:text-slate-400" />
          </div>

          <!-- Message -->
          <div
            class="flex overflow-hidden rounded-2xl border border-gray-300 bg-white shadow-sm transition-all hover:shadow-md dark:border-slate-700 dark:bg-slate-800">
            <!-- Icon -->
            <span
              :class="focusedInput === 'message' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-400 dark:bg-slate-700 dark:text-slate-300'"
              class="material-icons rounded-l-2xl px-4 py-3 flex items-center justify-center transition-colors">
              message
            </span>

            <!-- Textarea -->
            <textarea placeholder="Message" v-model="form.message" @focus="focusedInput = 'message'"
              @blur="focusedInput = ''"
              class="min-h-[120px] flex-1 resize-y rounded-r-2xl bg-transparent px-4 py-3 text-slate-800 outline-none focus:ring-2 focus:ring-indigo-500 transition dark:text-slate-100 dark:placeholder:text-slate-400"></textarea>
          </div>

          <!-- Submit Button -->
          <button type="submit" :disabled="isSubmitting"
            class="w-full bg-green-500 text-white py-3 rounded-full font-semibold hover:bg-green-600 transition shadow-md hover:shadow-lg disabled:cursor-not-allowed disabled:opacity-60">
            {{ isSubmitting ? 'Sending...' : 'Send' }}
          </button>

        </form>
      </div>

      <!-- Map (Right) -->
      <div class="h-64 border-t border-slate-200 md:h-auto md:w-1/2 md:border-l md:border-t-0 dark:border-slate-700">
        <iframe class="w-full h-full" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.925477122509!2d106.69277607475605!3d10.775944992319933!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x317529292b3e9b3f%3A0x96d824f712345678!2sHo%20Chi%20Minh%20City%2C%20Vietnam!5e0!3m2!1sen!2sus!4v1698234567890!5m2!1sen!2sus"
          allowfullscreen></iframe>
      </div>

    </div>
  </section>
</template>

<script setup lang="ts">
defineOptions({
  name: 'ContactPage',
})

import { reactive, ref, computed } from 'vue'
import { useAuth } from '../store/authStore'

const auth = useAuth()
const isLoggedIn = computed(() => auth.isLoggedIn)

const CONTACT_API = 'http://localhost/Eshop/Backend/api/contact/submit_contact.php'

const form = reactive({
  name: '',
  email: '',
  message: ''
})

const focusedInput = ref('')
const isSubmitting = ref(false)
const feedbackMessage = ref('')
const feedbackType = ref<'success' | 'error'>('success')

const resetForm = () => {
  form.name = ''
  form.email = ''
  form.message = ''
}

const handleSubmit = async () => {
  feedbackMessage.value = ''

  if (!form.name.trim() || !form.email.trim() || !form.message.trim()) {
    feedbackType.value = 'error'
    feedbackMessage.value = 'Please fill in all fields.'
    return
  }

  isSubmitting.value = true

  try {
    const response = await fetch(CONTACT_API, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      credentials: 'include',
      body: JSON.stringify({
        name: form.name.trim(),
        email: form.email.trim(),
        message: form.message.trim()
      })
    })

    const data = await response.json()

    if (!data.success) {
      throw new Error(data.message || 'Failed to send your message.')
    }

    feedbackType.value = 'success'
    feedbackMessage.value = 'Your message has been sent successfully. We will respond to your inquiry via email.'
    resetForm()
  } catch (error) {
    console.error('Contact form error:', error)
    feedbackType.value = 'error'
    feedbackMessage.value = error instanceof Error ? error.message : 'Failed to send your message.'
  } finally {
    isSubmitting.value = false
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/icon?family=Material+Icons');

/* Input & textarea focus effect */
input:focus,
textarea:focus {
  outline: none;
  border-color: #4f46e5;
  /* indigo-600 */
  box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.3);
}
</style>
