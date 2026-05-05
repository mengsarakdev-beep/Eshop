import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

const AUTH_KEYS = ['user_id', 'username', 'email', 'phone', 'role', 'image'] as const

const getStoredValue = (key: (typeof AUTH_KEYS)[number]) =>
  localStorage.getItem(key) ?? sessionStorage.getItem(key) ?? ''

const getStoredNumber = (key: 'user_id') => {
  const value = localStorage.getItem(key) ?? sessionStorage.getItem(key)
  return value ? Number(value) : null
}

const persistValue = (key: (typeof AUTH_KEYS)[number], value: string) => {
  const remember = localStorage.getItem('remember_me') === '1'
  const activeStorage = remember ? localStorage : sessionStorage
  const inactiveStorage = remember ? sessionStorage : localStorage

  inactiveStorage.removeItem(key)
  activeStorage.setItem(key, value)
}

export const useAuth = defineStore('auth', () => {
  /* ---------------- STATE ---------------- */

  const user_id = ref<number | null>(getStoredNumber('user_id'))

  const username = ref<string>(getStoredValue('username'))

  const email = ref<string>(getStoredValue('email'))

  const phone = ref<string>(getStoredValue('phone'))

  const role = ref<string>(getStoredValue('role'))

  const image = ref<string>(getStoredValue('image'))

  /* ---------------- GETTERS ---------------- */

  const isLoggedIn = computed(() => user_id.value !== null)

  const isAdmin = computed(() => role.value.toLowerCase() === 'admin')

  /* ---------------- ACTIONS ---------------- */

  function setUser(
    id: number,
    name: string,
    mail: string,
    ph: string,
    userRole: string,
    imagePath = '',
    remember = localStorage.getItem('remember_me') === '1',
  ) {
    user_id.value = id
    username.value = name
    email.value = mail
    phone.value = ph
    role.value = userRole
    image.value = imagePath

    const activeStorage = remember ? localStorage : sessionStorage
    const inactiveStorage = remember ? sessionStorage : localStorage

    inactiveStorage.removeItem('user_id')
    inactiveStorage.removeItem('username')
    inactiveStorage.removeItem('email')
    inactiveStorage.removeItem('phone')
    inactiveStorage.removeItem('role')
    inactiveStorage.removeItem('image')

    activeStorage.setItem('user_id', id.toString())
    activeStorage.setItem('username', name)
    activeStorage.setItem('email', mail)
    activeStorage.setItem('phone', ph)
    activeStorage.setItem('role', userRole)
    activeStorage.setItem('image', imagePath)
  }

  function updateProfile(profile: {
    username: string
    email: string
    phone: string
    image?: string
  }) {
    username.value = profile.username
    email.value = profile.email
    phone.value = profile.phone

    if (typeof profile.image === 'string') {
      image.value = profile.image
    }

    persistValue('username', username.value)
    persistValue('email', email.value)
    persistValue('phone', phone.value)
    persistValue('role', role.value)
    persistValue('image', image.value)
  }

  function setProfileImage(imagePath: string) {
    updateProfile({
      username: username.value,
      email: email.value,
      phone: phone.value,
      image: imagePath,
    })
  }

  function logout() {
    user_id.value = null
    username.value = ''
    email.value = ''
    phone.value = ''
    role.value = ''
    image.value = ''

    for (const key of AUTH_KEYS) {
      localStorage.removeItem(key)
      sessionStorage.removeItem(key)
    }
  }

  return {
    user_id,
    username,
    email,
    phone,
    role,
    image,
    isLoggedIn,
    isAdmin,
    setUser,
    updateProfile,
    setProfileImage,
    logout,
  }
})
