import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import router from './router'

import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'

import api from './api'

const app = createApp(App)
app.config.globalProperties.$api = api

app.use(router).mount('#app')
