import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import { createPinia } from 'pinia'
import router from './router'
import VueToast from 'vue-toast-notification'
import 'vue-toast-notification/dist/theme-sugar.css'

import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'

import api from './api'

const app = createApp(App)
const pinia = createPinia()

app.config.globalProperties.$api = api

app.use(pinia)
app.use(router)
app.use(VueToast, {
  position: 'top-right',
  duration: 3000,
})
app.mount('#app')
