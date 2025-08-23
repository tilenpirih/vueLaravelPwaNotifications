/// <reference lib="webworker" />
/// <reference lib="es2018" />

// This is the Service Worker for VitePWA (injectManifest strategy)
// Workbox will inject the precache manifest at build time.

declare const self: ServiceWorkerGlobalScope & typeof globalThis

// Precache manifest will be injected here by Workbox
const _precacheManifest = (self as unknown as { __WB_MANIFEST: unknown }).__WB_MANIFEST

self.addEventListener('install', () => {
  // Activate immediately on install
  self.skipWaiting()
})

self.addEventListener('activate', event => {
  // Claim control so SW starts controlling clients without reload
  event.waitUntil(self.clients.claim())
})

// Basic push handler: expects event.data.json() with { title, body, url }
self.addEventListener('push', event => {
  const data = (() => {
    try {
      return event.data ? event.data.json() : {}
    }
    catch {
      return { body: event.data?.text() }
    }
  })()

  const title = data.title || 'New Notification'
  const body = data.body || 'You have a new message.'
  const url = data.url || '/'

  const options: NotificationOptions = {
    body,
    data: { url },
    icon: '/favicon.ico',
    badge: '/favicon.ico',
  }

  event.waitUntil(self.registration.showNotification(title, options))
})

self.addEventListener('notificationclick', event => {
  event.notification.close()
  const url = (event.notification.data && (event.notification.data as { url?: string }).url) || '/'

  event.waitUntil(
    (async () => {
      const allClients = await self.clients.matchAll({ type: 'window', includeUncontrolled: true })
      for (const client of allClients) {
        const win = client as WindowClient
        if (win.url.includes(new URL(url, self.location.origin).pathname)) {
          await win.focus()
          return
        }
      }
      await self.clients.openWindow(url)
    })(),
  )
})
