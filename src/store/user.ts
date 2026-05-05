// src/store/user.ts
import { defineStore } from 'pinia'

export const useUserStore = defineStore('user', {
  state: () => ({
    id: 0,
    username: '',
    role: 'user',
    isLoggedIn: false,
  }),
  actions: {
    logout() {
      this.id = 0
      this.username = ''
      this.role = 'user'
      this.isLoggedIn = false
    },
  },
  // persist: true // <-- removed, as plugin not installed
})
