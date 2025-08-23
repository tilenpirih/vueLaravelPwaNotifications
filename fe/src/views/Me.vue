<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import api from '@/api'
import {
  getExistingSub,
  getPublicKey,
  sendTestPush,
  subscribeToPush,
  unsubscribeFromPush,
} from '@/push'
import router from '@/router'

const user = ref<any>(null)
const permission = ref<NotificationPermission>(typeof Notification !== 'undefined' ? Notification.permission : 'default')
const swSupported = 'serviceWorker' in navigator && 'PushManager' in window
const isSubscribed = ref<boolean>(false)
const publicKey = ref<string>('')
const loading = ref(false)
const error = ref<string>('')

const canSubscribe = computed(() => swSupported && permission.value !== 'denied')

async function refreshSubState() {
  try {
    const sub = await getExistingSub()
    isSubscribed.value = !!sub
  }
  catch (e: any) {
    error.value = e?.message || 'Failed to check subscription'
  }
}

async function askPermission() {
  if (!('Notification' in window))
    return
  const result = await Notification.requestPermission()
  permission.value = result
}

async function onSubscribe() {
  loading.value = true
  error.value = ''
  try {
    await subscribeToPush()
    await refreshSubState()
  }
  catch (e: any) {
    error.value = e?.message || 'Subscribe failed'
  }
  finally {
    loading.value = false
  }
}

async function onUnsubscribe() {
  loading.value = true
  error.value = ''
  try {
    await unsubscribeFromPush()
    await refreshSubState()
  }
  catch (e: any) {
    error.value = e?.message || 'Unsubscribe failed'
  }
  finally {
    loading.value = false
  }
}

async function onSendTest() {
  loading.value = true
  error.value = ''
  try {
    await sendTestPush()
  }
  catch (e: any) {
    error.value = e?.message || 'Send test failed'
  }
  finally {
    loading.value = false
  }
}

async function logout() {
  try {
    await api.post('/auth/logout')
    localStorage.removeItem('access_token')
    router.push('/login')
  }
  catch (e: any) {
    error.value = e?.message || 'Logout failed'
  }
}

onMounted(async () => {
  try {
    const res = await api.get('/auth/me')
    user.value = res.data
  }
  catch {
    user.value = null
  }
  try {
    publicKey.value = await getPublicKey()
  }
  catch (e: any) {
    error.value = e?.message || 'Failed to fetch public key'
  }
  await refreshSubState()
})
</script>

<template>
  <div>
    <h2>User</h2>
    <button @click="logout">
      Logout
    </button>
    <pre style="background:#f6f6f6;padding:8px;border-radius:6px;max-width:100%;overflow:auto">{{ user }}</pre>

    <h3>Web Push Demo</h3>
    <p v-if="!swSupported" style="color:#b00">
      This browser doesn't support ServiceWorker/Push.
    </p>
    <p><strong>Permission:</strong> {{ permission }}</p>
    <p><strong>Public Key:</strong> <code>{{ publicKey }}</code></p>
    <div style="display:flex; gap:8px; flex-wrap:wrap; margin:8px 0;">
      <button :disabled="permission === 'granted'" @click="askPermission">
        Grant Notification Permission
      </button>
      <button :disabled="!canSubscribe || isSubscribed || loading" @click="onSubscribe">
        Subscribe
      </button>
      <button :disabled="!isSubscribed || loading" @click="onUnsubscribe">
        Unsubscribe
      </button>
      <button :disabled="!isSubscribed || loading" @click="onSendTest">
        Send Test
      </button>
    </div>
    <p><strong>Status:</strong> {{ isSubscribed ? 'Subscribed' : 'Not subscribed' }}</p>
    <p v-if="error" style="color:#b00">
      Error: {{ error }}
    </p>
  </div>
</template>
