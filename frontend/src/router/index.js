import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/LoginView/LoginView.vue'
import DashboardView from '../views/DashboardView/DashboardView.vue'
import { useUserStore } from '../stores/user'

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: LoginView,
  },
  {
    path: '/',
    name: 'Dashboard',
    component: DashboardView,
    meta: { requiresAuth: true },
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach(async (to, from, next) => {
  const store = useUserStore()

  if (!store.user && localStorage.getItem('token')) {
    await store.fetchUser()
  }

  if (to.meta.requiresAuth && !store.isAuthenticated) {
    next('/login')
  } else {
    next()
  }
})

export default router
