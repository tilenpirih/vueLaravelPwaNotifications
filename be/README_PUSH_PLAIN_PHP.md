# Self-Hosted Web Push Notifications in Plain PHP

This guide explains how to implement self-hosted web push notifications in a plain PHP backend (no framework required).

---

## 1. Install Required Library

-   Use [Minishlink/web-push](https://github.com/web-push-libs/web-push-php) for sending push notifications.
-   Install via Composer:
    ```sh
    composer require minishlink/web-push
    ```

## 2. Generate VAPID Keys

-   Run:
    ```sh
    ./vendor/bin/web-push generate-vapid-keys
    ```

## 3. Store Subscriptions

-   When a user subscribes (from frontend), you will receive a JSON object with `endpoint`, `keys[p256dh]`, and `keys[auth]`.
-   Store these in a database (e.g., MySQL) in a table like:
    | id | endpoint | p256dh | auth | user_id (optional) |

## 4. Create Endpoints

-   Create PHP scripts to:
    -   Receive and store new subscriptions (e.g., `subscribe.php`)
    -   Remove subscriptions (e.g., `unsubscribe.php`)

## 5. Send Push Notifications

-   Use a script (e.g., `send_push.php`) to send notifications to all stored endpoints.
-   Example code:

    ```php
    require 'vendor/autoload.php';
    use Minishlink\WebPush\WebPush;
    use Minishlink\WebPush\Subscription;
    $auth = [
    		'VAPID' => [
    				'subject' => 'mailto:your@email.com',
      ],
    ];

    $webPush = new WebPush($auth);
    // Fetch subscriptions from DB
    $subscriptions = [
    		// ['endpoint' => ..., 'p256dh' => ..., 'auth' => ...],
    foreach ($subscriptions as $sub) {
    		$subscription = Subscription::create([
    				'endpoint' => $sub['endpoint'],
    	  'publicKey' => $sub['p256dh'],
    	  'authToken' => $sub['auth'],
      ]);
      $webPush->queueNotification($subscription, json_encode([
    	  'title' => 'Hello!',
    	  'body' => 'This is a test notification.'
      foreach ($webPush->flush() as $report) {
    	  $endpoint = $report->getRequest()->getUri()->__toString();
    	  if ($report->isSuccess()) {
    		  echo "Message sent successfully to {$endpoint}\n";
    	  } else {
    		  echo "Message failed for {$endpoint}: {$report->getReason()}\n";
      }
    }

    ```

## 6. Frontend Integration

-   Register a service worker and subscribe to push notifications using the Push API.
-   Send the subscription object to your backend via AJAX to store it.

## 7. Test

-   Subscribe from the frontend and trigger a notification from your backend script.

---

## References

-   [Web Push PHP Library](https://github.com/web-push-libs/web-push-php)
-   [MDN: Using the Push API](https://developer.mozilla.org/en-US/docs/Web/API/Push_API)
