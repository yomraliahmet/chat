# Laravel, Socket.io Chat


## Gereklilikler

- Php 7.4.10
- Mysql 8.0.21
- Composer 1.10.10
- Nodejs v14.15.0
- socket.io
- redis

## Kurulum

```
composer install
php artisan migrate --seed
npm install
npm run dev
```
## Çalıştırma

```
php artisan queue:work
node server.js
```
