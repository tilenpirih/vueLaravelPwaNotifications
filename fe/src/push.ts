import api from '@/api'

export async function getPublicKey(): Promise<string> {
  const res = await api.get('/push/public-key')
  return res.data?.publicKey || res.data
}

function urlBase64ToUint8Array(base64String: string) {
  const padding = '='.repeat((4 - (base64String.length % 4)) % 4)
  const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/')
  const rawData = globalThis.atob(base64)
  const outputArray = new Uint8Array(rawData.length)
  for (let i = 0; i < rawData.length; i++) outputArray[i] = rawData.charCodeAt(i)
  return outputArray
}

export async function ensureServiceWorker(): Promise<ServiceWorkerRegistration> {
  if (!('serviceWorker' in navigator))
    throw new Error('Service workers not supported')
  const reg = await navigator.serviceWorker.getRegistration()
  if (reg)
    return reg
  return navigator.serviceWorker.ready
}

export async function subscribeToPush() {
  const reg = await ensureServiceWorker()
  const key = await getPublicKey()
  const sub = await reg.pushManager.subscribe({
    userVisibleOnly: true,
    applicationServerKey: urlBase64ToUint8Array(key),
  })
  // Send to backend
  await api.post('/push/subscribe', sub.toJSON())
  return sub
}

export async function getExistingSub(): Promise<PushSubscription | null> {
  const reg = await ensureServiceWorker()
  return reg.pushManager.getSubscription()
}

export async function unsubscribeFromPush() {
  const sub = await getExistingSub()
  if (sub) {
    await api.post('/push/unsubscribe', sub.toJSON())
    await sub.unsubscribe()
  }
}

export async function sendTestPush() {
  await api.post('/push/send-test')
}
