<template>
  <div id="app">
    <header
      v-if="!isLoginPage && userStore.isAuthenticated"
      class="app-header shadow-sm px-4 py-3 d-flex justify-content-between align-items-center"
    >
      <img src="/images/logo-onfly-black.webp" alt="Logo Onfly" height="32" class="logo" />

      <div v-if="userStore.isAuthenticated" class="d-flex align-items-center gap-3">
        <span class="fw-semibold text-dark">Bem vindo, {{ userStore.userName }}</span>
        <button class="btn btn-sm btn-outline-secondary" @click="logout">Sair</button>
      </div>
    </header>

    <main>
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { useRoute, useRouter } from 'vue-router'
import { computed, onMounted } from 'vue'
import { useUserStore } from './stores/user.js'

const route = useRoute()
const router = useRouter()
const userStore = useUserStore()

const isLoginPage = computed(() => route.name === 'Login')

onMounted(() => {
  if (userStore.isAuthenticated && !userStore.user) {
    userStore.fetchUser()
  }
})

const logout = () => {
  userStore.logout()
  router.push('/login')
}
</script>

<style>

</style>
