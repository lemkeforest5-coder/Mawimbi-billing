# Mawimbi Billing

Mawimbi Billing is a Laravel-based hotspot billing system for small ISPs and Wi‑Fi providers. It integrates with MikroTik Hotspot and M‑Pesa to sell and manage prepaid internet access. [web:634][web:628]

## Features

- Voucher-based hotspot access (time and data limits per profile)
- MikroTik Hotspot integration (profiles, session-timeout, automatic user creation)
- M‑Pesa STK push payments with automatic voucher generation and linking to payments
- Admin panel for managing routers, profiles, vouchers, and hotspot users
- REST API endpoints for captive portal/front-end integration

## Tech Stack

- PHP 8.3
- Laravel 10
- MySQL / MariaDB
- MikroTik RouterOS (Hotspot)
- M‑Pesa Daraja API

## System Requirements

- PHP 8.2+ with required Laravel extensions
- Composer
- MySQL or MariaDB
- Node.js (for asset building, if needed)
- Git

## Installation

```bash
git clone git@github.com:lemkeforest5-coder/Mawimbi-billing.git
cd Mawimbi-billing

cp .env.example .env
composer install
php artisan key:generate
```

Edit `.env` and configure:

- Database connection
- APP_URL
- M‑Pesa credentials (consumer key/secret, shortcode, passkey)
- MikroTik connection details (host, port, username, password)

Then run:

```bash
php artisan migrate
php artisan db:seed   # optional, if you have seeders
php artisan serve
```

The app will be available at `http://127.0.0.1:8000`.

## Voucher Profiles and Time Limits

Each **Profile** can define:

- `time_limit_minutes` – total allowed session time in minutes
- `data_limit_mb` – total allowed data in megabytes

Whenever a new voucher is created (via UI, M‑Pesa payment, or artisan command), the system automatically copies limits from the profile:

- `time_limit_seconds = time_limit_minutes * 60`
- `data_limit_mb` from the profile

This ensures vouchers are always in sync with the profile configuration.

## Generating Vouchers (Artisan)

Use the custom artisan command to generate a batch of vouchers:

```bash
php artisan vouchers:generate {router_id} {profile_id} {count} --prefix={PREFIX} --length=8
```

Example:

```bash
# Generate 3 KUMI vouchers for router 1 (profile id 2) with prefix KM
php artisan vouchers:generate 1 2 3 --prefix=KM --length=8
```

The command will print the generated codes and create `Voucher` records with `time_limit_seconds` and `data_limit_mb` derived from the selected profile.

## M‑Pesa Payments

Successful M‑Pesa STK payments automatically:

- Map the paid amount to a plan/profile
- Generate a new voucher linked to the payment
- Apply profile-based time/data limits
- Optionally create/enable the corresponding MikroTik Hotspot user

Failures or missing configuration are logged for troubleshooting.

## MikroTik Integration

Mawimbi Billing talks to MikroTik Hotspot to:

- Create or enable hotspot users using voucher codes as username/password
- Attach the correct MikroTik profile (e.g. KUMI, MBAO, MONTHLY SOLO)
- Leverage RouterOS features like `session-timeout` and rate limiting

Routers and profiles are managed from the admin panel.

## Development

Useful commands:

```bash
php artisan serve           # Run local dev server
php artisan migrate         # Run migrations
php artisan tinker          # Interactive shell
php artisan vouchers:generate ...  # Generate vouchers
```

Pull requests and issues are welcome.
