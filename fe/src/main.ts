import { registerSW } from 'virtual:pwa-register'
import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import './assets/main.css'

const app = createApp(App)

app.use(router)

app.mount('#app')

// Register PWA service worker via VitePWA (handles dev/prod URLs)
registerSW({
  immediate: true,
  onNeedRefresh() {
    // You could show a toast to refresh the page
  },
  onOfflineReady() {
    // App ready to work offline
  },
})
