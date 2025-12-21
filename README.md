# Interdiscount Laravel Backend

Laravel backend for interdiscount.ch clone — handles product catalog, orders, user auth, and payment processing.

## Stack

- PHP 8.3 / Laravel 12
- MySQL 8.4
- Redis (sessions + cache)
- Stripe / TWINT / PostFinance payment integrations

## Setup

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Or with Docker:

```bash
docker compose up -d
```
