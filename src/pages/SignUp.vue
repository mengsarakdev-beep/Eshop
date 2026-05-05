<template>
  <div class="flex min-h-screen items-center justify-center bg-slate-100 p-4 transition-colors dark:bg-slate-950">
    <div
      class="w-full max-w-md rounded-2xl bg-white md:p-8 p-4 shadow-lg transition-colors dark:bg-slate-900 dark:shadow-slate-950/40">
      <h2 class="mb-2 text-center text-3xl font-bold text-gray-800 dark:text-slate-100">Sign Up</h2>


      <form @submit.prevent="handleSignUp" class="space-y-5 md:m-2 md:my-10 my-3">
        <div
          class="flex items-center overflow-hidden rounded-full border border-gray-300 bg-white shadow-sm transition-all hover:shadow-md dark:border-slate-700 dark:bg-slate-800">
          <span
            :class="focusedInput === 'name' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-400 dark:bg-slate-700 dark:text-slate-300'"
            class="material-icons flex items-center justify-center px-4 py-2.5 transition-colors">
            person
          </span>
          <input type="text" placeholder="Name" v-model="signUp.name" @focus="focusedInput = 'name'"
            @blur="focusedInput = ''"
            class="flex-1 bg-transparent px-4 py-2.5 text-slate-800 outline-none focus:ring-2 focus:ring-blue-500 transition dark:text-slate-100 dark:placeholder:text-slate-400"
            required />
        </div>

        <div
          class="flex items-center overflow-hidden rounded-full border border-gray-300 bg-white shadow-sm transition-all hover:shadow-md dark:border-slate-700 dark:bg-slate-800">
          <span
            :class="focusedInput === 'phoneNumber' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-400 dark:bg-slate-700 dark:text-slate-300'"
            class="material-icons px-4 py-2.5 flex items-center justify-center transition-colors">
            phone
          </span>
          <input type="tel" placeholder="Phone Number" v-model="signUp.phoneNumber"
            @focus="focusedInput = 'phoneNumber'" @blur="focusedInput = ''"
            class="flex-1 bg-transparent px-4 py-2.5 text-slate-800 outline-none focus:ring-2 focus:ring-blue-500 transition dark:text-slate-100 dark:placeholder:text-slate-400"
            required />
        </div>

        <div
          class="flex items-center overflow-hidden rounded-full border border-gray-300 bg-white shadow-sm transition-all hover:shadow-md dark:border-slate-700 dark:bg-slate-800">
          <span
            :class="focusedInput === 'email' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-400 dark:bg-slate-700 dark:text-slate-300'"
            class="material-icons px-4 py-2.5 flex items-center justify-center transition-colors">
            email
          </span>
          <input type="email" placeholder="Email" v-model="signUp.email" @focus="focusedInput = 'email'"
            @blur="focusedInput = ''"
            class="flex-1 bg-transparent px-4 py-2.5 text-slate-800 outline-none focus:ring-2 focus:ring-blue-500 transition dark:text-slate-100 dark:placeholder:text-slate-400"
            required />
        </div>

        <div
          class="flex items-center overflow-hidden rounded-full border border-gray-300 bg-white shadow-sm transition-all hover:shadow-md dark:border-slate-700 dark:bg-slate-800">
          <span
            :class="focusedInput === 'password' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-400 dark:bg-slate-700 dark:text-slate-300'"
            class="material-icons px-4 py-2.5 flex items-center justify-center transition-colors">
            lock
          </span>
          <input type="password" placeholder="Password" v-model="signUp.password" @focus="focusedInput = 'password'"
            @blur="focusedInput = ''"
            class="flex-1 bg-transparent px-4 py-2.5 text-slate-800 outline-none focus:ring-2 focus:ring-blue-500 transition dark:text-slate-100 dark:placeholder:text-slate-400"
            required />
        </div>

        <div
          class="flex items-center overflow-hidden rounded-full border border-gray-300 bg-white shadow-sm transition-all hover:shadow-md dark:border-slate-700 dark:bg-slate-800">
          <span
            :class="focusedInput === 'confirmPassword' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-400 dark:bg-slate-700 dark:text-slate-300'"
            class="material-icons px-4 py-2.5 flex items-center justify-center transition-colors">
            lock
          </span>
          <input type="password" placeholder="Confirm Password" v-model="signUp.confirmPassword"
            @focus="focusedInput = 'confirmPassword'" @blur="focusedInput = ''"
            class="flex-1 bg-transparent px-4 py-2.5 text-slate-800 outline-none focus:ring-2 focus:ring-blue-500 transition dark:text-slate-100 dark:placeholder:text-slate-400"
            required />
        </div>

        <button type="submit"
          class="w-full rounded-full bg-blue-600 py-2.5 font-semibold text-white shadow-md transition hover:bg-indigo-600 hover:shadow-lg">
          Sign Up
        </button>

        <p class="mt-4 text-center text-gray-500 dark:text-slate-300">
          Already have an account?
          <router-link to="/login" class="font-semibold text-blue-600 hover:underline dark:text-cyan-300">
            Login
          </router-link>
        </p>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
defineOptions({
  name: 'SignUpPage',
})

import { onMounted, reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '../store/authStore'

interface SessionUser {
  id: number
  username: string
  email: string
  phone: string
  role: string
  image?: string
}

interface SessionResponse {
  loggedIn: boolean
  user?: SessionUser
}

const router = useRouter()
const auth = useAuth()
const API_REGISTER = 'http://localhost/Eshop/Backend/api/auth/register.php'
const API_SESSION = 'http://localhost/Eshop/Backend/api/auth/check_session.php'

const signUp = reactive({
  name: '',
  phoneNumber: '',
  email: '',
  password: '',
  confirmPassword: ''
})

const focusedInput = ref('')

function applySessionUser(user: SessionUser) {
  auth.setUser(user.id, user.username, user.email, user.phone, user.role, user.image ?? '', false)
  router.replace(user.role.toLowerCase() === 'admin' ? '/admin' : '/')
}

const handleSignUp = async () => {
  if (signUp.password !== signUp.confirmPassword) {
    alert('Passwords do not match!')
    return
  }

  try {
    const res = await fetch(API_REGISTER, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        name: signUp.name,
        phoneNumber: signUp.phoneNumber,
        email: signUp.email,
        password: signUp.password
      })
    })
    const data = await res.json()
    if (data.success) {
      alert('Registration successful! You can now login.')
      signUp.name = ''
      signUp.phoneNumber = ''
      signUp.email = ''
      signUp.password = ''
      signUp.confirmPassword = ''
      router.push('/login')
    } else {
      alert(data.message)
    }
  } catch (err) {
    console.error(err)
    alert('Error connecting to server')
  }
}

onMounted(async () => {
  try {
    const res = await fetch(API_SESSION, { credentials: 'include' })
    const data: SessionResponse = await res.json()

    if (data.loggedIn && data.user) {
      applySessionUser(data.user)
    }
  } catch (err) {
    console.error(err)
  }
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/icon?family=Material+Icons');
</style>
