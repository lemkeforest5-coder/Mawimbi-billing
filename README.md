# Mawimbi Billing

Mawimbi Billing is a Laravel-based hotspot billing system for small ISPs and Wi‑Fi providers, with MikroTik integration and M‑Pesa payments.

## Features

- Voucher-based hotspot access (time and data limits per profile)
- MikroTik Hotspot integration (session-timeout, profiles)
- M-Pesa STK push payments with automatic voucher generation
- Admin panel for managing routers, profiles, and vouchers

## Tech Stack

- PHP 8.3, Laravel 10
- MySQL/MariaDB
- MikroTik RouterOS Hotspot
- M-Pesa Daraja API

## Local Setup

```bash
git clone git@github.com:lemkeforest5-coder/Mawimbi-billing.git
cd Mawimbi-billing

cp .env.example .env
composer install
php artisan key:generate

# configure DB, M-Pesa, and MikroTik details in .env

php artisan migrate
php artisan db:seed   # optional

php artisan serve
```

## Voucher Time Limits

Profiles define `time_limit_minutes` and `data_limit_mb`. New vouchers automatically get:

- `time_limit_seconds = time_limit_minutes * 60`
- `data_limit_mb` copied from the profile

Artisan command to generate vouchers:

```bash
php artisan vouchers:generate {router_id} {profile_id} {count} --prefix=MB --length=8
```
