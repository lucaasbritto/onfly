import { defineStore } from 'pinia'
import { login as apiLogin, logout as apiLogout } from '../api/auth'
import { getUserProfile } from '../api/user'

export const useUserStore = defineStore('user', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null,
    token: localStorage.getItem('token') || null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    userName: (state) => state.user?.name || 'Visitante',
    isAdmin: (state) => state.user?.is_admin,    
  },

  actions: {
    async login(email, password) {
      const { token, user } = await apiLogin(email, password)
      this.token = token
      this.user = user

      localStorage.setItem('token', token)
      localStorage.setItem('user', JSON.stringify(user))
    },

    async fetchUser() {
      try {
        const user = await getUserProfile()
        this.user = user
        localStorage.setItem('user', JSON.stringify(user))
      } catch (err) {
        this.logout()
      }
    },

    logout() {
      apiLogout()
      this.token = null
      this.user = null
    },
  },
})
