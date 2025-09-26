# Self-Hosted Web Push Notifications in Laravel

This guide explains how to implement self-hosted web push notifications in a Laravel backend.

---

## 1. Install Required Packages

-   Install the Minishlink/web-push PHP library (already present in `vendor/`).
-   Ensure your frontend can register service workers and subscribe to push notifications.

## 2. Set Up VAPID Keys

-   Generate VAPID keys:
    ```sh
    ./vendor/bin/web-push generate-vapid-keys
    ```

## 3. Create Subscription Model & Migration

-   Use a model like `WebPushSubscription` (`app/Models/WebPushSubscription.php`).
-   Migration example: `database/migrations/2025_08_23_000000_create_web_push_subscriptions_table.php`.

## 4. Create Subscription Controller & Routes

-   Add endpoints to:
    -   Store a new subscription (`POST /api/push/subscribe`)
    -   Remove a subscription (`POST /api/push/unsubscribe`)
-   See `app/Http/Controllers/` for controller examples.
-   Register routes in `routes/api.php`.

## 5. Store Subscriptions

-   When a user subscribes, save their endpoint, public key, and auth secret in the `web_push_subscriptions` table.

## 6. Send Push Notifications

-   Use a service like `WebPushService` (`app/Services/WebPushService.php`) to send notifications.
-   Use the Minishlink/web-push library to send messages to all stored endpoints.

## 7. Create a Command or Job for Sending

-   Use a job like `SendWebPush` (`app/Jobs/SendWebPush.php`) to queue and send notifications.

## 8. Frontend Integration

-   Register a service worker in your frontend (`resources/js/`).
-   Use the Push API to subscribe and send the subscription to your backend.

## 9. Test

-   Use your frontend to subscribe and trigger a notification from the backend.

---

### Example File References

-   Subscription Model: `app/Models/WebPushSubscription.php`
-   Service: `app/Services/WebPushService.php`
-   Job: `app/Jobs/SendWebPush.php`
-   Migration: `database/migrations/2025_08_23_000000_create_web_push_subscriptions_table.php`
-   Config: `config/webpush.php`
-   Controller: `app/Http/Controllers/` (create e.g., `PushController.php`)
-   Routes: `routes/api.php`

---

If you need code samples or more details for any step, see the referenced files or ask for more details.
