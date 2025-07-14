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

       <div class="notification-wrapper position-relative">
      <button @click="toggleMenu" class="btn btn-light position-relative">
        <i class="bi bi-bell fs-5"></i>
        <span v-if="store.unreadCount" class="badge bg-danger position-absolute top-0 start-100 translate-middle">
          {{ store.unreadCount }}
        </span>
      </button>

      <div v-if="menuOpen" class="dropdown-notifs shadow bg-white">
        <ul class="list-unstyled m-0 p-2">
          <li
            v-for="n in store.items"
            :key="n.id"
            :class="[
              'position-relative',
              !n.read_at && n.data.status === 'aprovado' ? 'bg-aprove' : '',
              !n.read_at && n.data.status === 'cancelado' ? 'bg-cancel' : '',
              !n.read_at && !['aprovado', 'cancelado'].includes(n.data.status) ? 'bg-lightblue' : ''
            ]"
          >
            <small>{{ formatDate(n.created_at) }}</small>
            <div>{{ n.data.mensagem }}</div>  
            
            <button
              v-if="!n.read_at"
              @click="markAsRead(n.id)"
              class="btn btn-sm btn-light rounded-circle position-absolute top-50 end-0 translate-middle-y me-2"
              title="Marcar como lida"
            >
              <i class="bi bi-check-lg text-success"></i>
            </button>
          </li>
          <li v-if="!store.items.length" class="text-center text-muted small">Nenhuma notificação</li>
        </ul>
        <button
          v-if="store.unreadCount"
          class="btn btn-sm btn-link"
          @click="markRead"
        >
          Marcar todos como lidas
        </button>
      </div>
    </div>
    </header>

    <main>
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { useRoute, useRouter } from 'vue-router'
import { computed, onMounted,ref, watch } from 'vue'
import { useUserStore } from './stores/user.js'
import { useNotificationStore } from './stores/notifications'

const route = useRoute()
const router = useRouter()
const userStore = useUserStore()

const store = useNotificationStore()
const menuOpen = ref(false)

const isLoginPage = computed(() => route.name === 'Login')

onMounted(async () => {
  if (userStore.isAuthenticated) {
    if (!userStore.user) {
      await userStore.fetchUser()
    }
    await store.fetch()
  }

  setInterval(() => {
      if (userStore.isAuthenticated) {
        store.fetch()
      }
    }, 30000)
})

watch(() => userStore.isAuthenticated, async (newVal) => {
  if (newVal) {
    if (!userStore.user) {
      await userStore.fetchUser()
    }
    await store.fetch()
  }
})

function toggleMenu(){
  menuOpen.value = !menuOpen.value
}

function markRead(){
  store.markAllRead()
  menuOpen.value=false
}

function markAsRead(id) {
  store.markOneRead(id)
}

function formatDate(dt) {
  return new Intl.DateTimeFormat('pt-BR', { dateStyle: 'short', timeStyle: 'short' }).format(new Date(dt))
}
const logout = () => {
  userStore.logout()
  router.push('/login')
}
</script>

<style>

</style>
