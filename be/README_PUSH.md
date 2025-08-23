# Backend Web Push (Self-hosted)

What was added:

-   `web_push_subscriptions` table, model, and relations.
-   `WebPushService` using VAPID (Minishlink/web-push).
-   Endpoints under `/api/push/*` to get VAPID public key, subscribe/unsubscribe, and send a test.
-   Artisan `push:vapid:generate` to create VAPID keys.

Setup steps:

1. Install deps inside container and migrate.
2. Generate VAPID keys and set in `.env`:
    - VAPID_PUBLIC_KEY, VAPID_PRIVATE_KEY, VAPID_SUBJECT
3. Expose public key to frontend via `/api/push/public-key`.
4. Frontend must register a Service Worker and call subscribe with the Push API using the VAPID public key; POST the subscription JSON to `/api/push/subscribe`.

Notes:

-   Job `SendWebPush` exists for queued fanout; wire to events as needed.
-   Expired subscriptions are pruned on send.
