# Laravel/vue PWA push notifications

This is a minimal reproduction to test push notifications.

## FE
- copy .env.example to .env
- npm install
- npm run dev

## BE
- copy .env.example to .env
- run `docker compose up`

Open the app and you have `/register` to register an user, when you register go to `/login` to enter the same credentials. After go to `/me` where you an test the notifications. For notifications to work you need to ask for permissions, and subscribe to it. You can do this in multiple browsers and when you click send test notifications you will receive notifications on all browsers/devices.

you can also enter custom notification message like title, body, url. for URL it can be local path like /login or it can be external path like https://google.com

## other
- don't forget to generate new vapid keys if you gonna use this in production!!!
- you can generate VAPID keys by running a command in your console `php artisan push:vapid:generate`