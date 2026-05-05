import { createApp } from 'vue'
import axios from 'axios'
import App from './App.vue'
import router from './router'
import './assets/tailwind.css'
import '@fortawesome/fontawesome-free/css/all.min.css'
import { createPinia } from 'pinia'

axios.defaults.withCredentials = true

const app = createApp(App)
app.use(router)
app.use(createPinia())
app.mount('#app')
