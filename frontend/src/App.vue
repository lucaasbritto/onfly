<template>
  <div id="app">
    <header 
        v-if="!isLoginPage && userStore.isAuthenticated"
        class="app-header bg-white shadow-sm px-4 py-3 d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Onfly Portal</h5>

      <div v-if="userStore.isAuthenticated" class="d-flex align-items-center gap-3">
        <span>Olá, {{ userStore.userName }}</span>
        <button class="btn btn-outline-danger btn-sm" @click="logout">Sair</button>
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
body {
  margin: 0;
  font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
  background-color: #f9f9f9;
}

.app-header {
  position: sticky;
  top: 0;
  z-index: 100;
}

main {
  min-height: 100vh;
}
</style>
