/// <reference lib="webworker" />
/// <reference lib="es2018" />

// This is the Service Worker for VitePWA (injectManifest strategy)
// Workbox will inject the precache manifest at build time.

declare const self: ServiceWorkerGlobalScope & typeof globalThis

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
  const notificationData = data.data

  const options: NotificationOptions = {
    body,
    data: notificationData,
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

      // Check if the URL is external (different origin)
      const isExternalUrl = url.startsWith('http') && !url.startsWith(self.location.origin)

      if (isExternalUrl) {
        // For external URLs, just open a new window/tab
        await self.clients.openWindow(url)
      }
      else {
        // For internal URLs, try to find existing client or navigate
        const targetPath = url.startsWith('/') ? url : new URL(url, self.location.origin).pathname

        for (const client of allClients) {
          const win = client as WindowClient
          if (win.url.includes(targetPath) || win.url.includes(self.location.origin)) {
            await win.focus()
            // Navigate to the new URL if it's different
            if (!win.url.includes(targetPath)) {
              await win.navigate(url)
            }
            return
          }
        }
        await self.clients.openWindow(url)
      }
    })(),
  )
})
