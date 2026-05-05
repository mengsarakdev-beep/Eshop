<template>
  <div class="flex min-h-screen items-center justify-center bg-slate-100 p-4 transition-colors dark:bg-slate-950">
    <div
      class="w-full max-w-md rounded-2xl bg-white md:p-14 p-4 shadow-lg transition-colors dark:bg-slate-900 dark:shadow-slate-950/40">
      <h2 class="mb-2 text-center text-3xl font-bold text-gray-800 dark:text-slate-100">Login</h2>


      <form @submit.prevent="handleLogin" class="space-y-5 mt-8">
        <div
          class="flex items-center overflow-hidden rounded-full border border-gray-300 bg-white shadow-sm transition hover:shadow-md dark:border-slate-700 dark:bg-slate-800">
          <span
            :class="focusedInput === 'identifier' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-400 dark:bg-slate-700 dark:text-slate-300'"
            class="material-icons px-4 py-2.5">
            account_circle
          </span>

          <input type="text" placeholder="Phone Number or Email" v-model="login.identifier"
            @focus="focusedInput = 'identifier'" @blur="focusedInput = ''"
            class="flex-1 bg-transparent px-4 py-2.5 text-slate-800 outline-none focus:ring-2 focus:ring-indigo-500 dark:text-slate-100 dark:placeholder:text-slate-400" />
        </div>

        <div
          class="flex items-center overflow-hidden rounded-full border border-gray-300 bg-white shadow-sm transition hover:shadow-md dark:border-slate-700 dark:bg-slate-800">
          <span
            :class="focusedInput === 'password' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-400 dark:bg-slate-700 dark:text-slate-300'"
            class="material-icons px-4 py-2.5">
            lock
          </span>

          <input :type="showPassword ? 'text' : 'password'" placeholder="Password" v-model="login.password"
            @focus="focusedInput = 'password'" @blur="focusedInput = ''"
            class="flex-1 bg-transparent px-4 py-2.5 text-slate-800 outline-none focus:ring-2 focus:ring-indigo-500 dark:text-slate-100 dark:placeholder:text-slate-400" />

          <button type="button" @click="showPassword = !showPassword"
            class="px-3 bg-transparent text-gray-500 transition hover:text-indigo-600 focus:outline-none dark:text-slate-300 dark:hover:text-cyan-300">
            <span class="material-icons text-xl">
              {{ showPassword ? 'visibility_off' : 'visibility' }}
            </span>
          </button>
        </div>

<label
  class="flex items-center rounded-full  border-gray-300 bg-white px-5 py-3 transition  dark:border-slate-700 dark:bg-slate-900">

  <div class="flex flex-col">
    <span class="text-sm font-semibold text-slate-700 dark:text-slate-100">
      Remember Me
    </span>

  </div>
  <div
    @click="rememberMe = !rememberMe"
    :class="rememberMe
      ? 'bg-indigo-600 border-indigo-600 '
      : 'bg-white border-gray-300 dark:bg-slate-700 dark:border-slate-500'"
    class="flex h-6 w-6 ml-3 cursor-pointer items-center justify-center rounded-full border-2 transition-all duration-300">

    <span
      v-if="rememberMe"
      class="material-icons text-sm text-white">
      check
    </span>
  </div>

</label>

        <button type="submit"
          class="w-full rounded-full bg-indigo-600 py-3 font-semibold text-white shadow-md transition hover:bg-indigo-700">
          Login
        </button>

        <p class="mt-4 text-center text-gray-500 dark:text-slate-300">
          Don't have an account?
          <router-link to="/signup" class="font-semibold text-blue-600 hover:underline dark:text-cyan-300">
            Sign Up
          </router-link>
        </p>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
defineOptions({
  name: 'LoginPage',
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

const API_LOGIN = 'http://localhost/Eshop/Backend/api/auth/login.php'
const API_SESSION = 'http://localhost/Eshop/Backend/api/auth/check_session.php'
const REMEMBER_ME_KEY = 'remember_me'
const REMEMBERED_IDENTIFIER_KEY = 'remembered_identifier'

const focusedInput = ref('')
const showPassword = ref(false)
const rememberMe = ref(localStorage.getItem(REMEMBER_ME_KEY) === '1')

const login = reactive({
  identifier: localStorage.getItem(REMEMBERED_IDENTIFIER_KEY) || '',
  password: ''
})

function applySessionUser(user: SessionUser) {
  auth.setUser(
    user.id,
    user.username,
    user.email,
    user.phone,
    user.role,
    user.image ?? '',
    rememberMe.value
  )

  if (user.role.toLowerCase() === 'admin') {
    router.replace('/admin')
  } else {
    router.replace('/')
  }
}

const handleLogin = async () => {
  if (!login.identifier || !login.password) {
    alert('Please fill all fields')
    return
  }

  try {
    const res = await fetch(API_LOGIN, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify({
        emailOrPhone: login.identifier,
        password: login.password
      })
    })

    const data = await res.json()

    if (data.success && data.user) {
      if (rememberMe.value) {
        localStorage.setItem(REMEMBER_ME_KEY, '1')
        localStorage.setItem(REMEMBERED_IDENTIFIER_KEY, login.identifier)
      } else {
        localStorage.removeItem(REMEMBER_ME_KEY)
        localStorage.removeItem(REMEMBERED_IDENTIFIER_KEY)
      }

      applySessionUser(data.user)
    } else {
      alert(data.message || 'Login failed')
    }
  } catch (err) {
    console.error(err)
    alert('Server error')
  }
}

onMounted(async () => {
  try {
    const res = await fetch(API_SESSION, {
      credentials: 'include'
    })

    const data: SessionResponse = await res.json()

    if (data.loggedIn && data.user) {
      applySessionUser(data.user)
    }
  } catch (err) {
    console.log(err)
  }
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/icon?family=Material+Icons');
</style>
