# Mawimbi Billing

Mawimbi Billing is a Laravel-based hotspot and ISP billing platform integrating Safaricom M-Pesa payments, voucher management, and MikroTik hotspot control.

It is designed to manage prepaid internet access, automate voucher creation from mobile money payments, and control router access from a central admin panel.

> Private repository maintained by [lemkeforest5-coder](https://github.com/lemkeforest5-coder).

---

## Features

- M-Pesa STK payments tied to internet plans and routers
- Automatic voucher creation on successful payments
- Voucher-based hotspot login for customers
- MikroTik hotspot user creation/enabling (via API)
- Admin panel for plans, routers, vouchers, and payments
- Logging and basic reporting (payments, vouchers, sessions – WIP)

## Tech Stack

- PHP (Laravel)
- MySQL/MariaDB
- Safaricom M-Pesa (Daraja STK)
- MikroTik routers (Hotspot, optional RADIUS later)

---

## Project Roadmap (High Level)

1. **Core platform & authentication**
   - Laravel app skeleton, users, roles/permissions
   - Basic admin UI shell (login, dashboard)

2. **Network & MikroTik/RADIUS integration**
   - Router model and config (IP, port, credentials)
   - Profiles/plans that map to MikroTik rate-limits

3. **Plans & vouchers**
   - Plans catalog (price, duration, data, speed)
   - Voucher lifecycle: new → active → used / expired / disabled

4. **M-Pesa billing**
   - STK initiation from Mawimbi UI
   - STK callback handling, payment records, statuses

5. **Hotspot portal**
   - Voucher login endpoint and status page
   - Logout / session tracking (optional MikroTik → Mawimbi callback)

6. **Multi-site support**
   - Multiple routers / sites
   - Reseller model later

7. **Hardening & polish**
   - Security, logging, monitoring, documentation, CI/CD

---

## Getting Started

### Requirements

- PHP 8.x with required Laravel extensions
- MySQL/MariaDB
- Web server: Nginx or Apache with PHP-FPM
- Git and Composer
- Access to a MikroTik router (for full integration)
- Safaricom M-Pesa Daraja credentials (sandbox or production)

### Installation

On the target server:

```bash
git clone git@github.com:lemkeforest5-coder/Mawimbi-billing.git
cd Mawimbi-billing

composer install

cp .env.example .env
php artisan key:generate
php artisan migrate
```

Configure your web server to point the virtual host to the `public/` directory (for example, `https://hotspot.yourdomain.com`).

### Environment Configuration

Edit `.env` and set:

**Application:**

```env
APP_NAME="Mawimbi Billing"
APP_ENV=production
APP_URL=https://hotspot.yourdomain.com
```

**Database:**

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mawimbi_billing
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

Configure cache / queue / mail according to your infrastructure.

#### M-Pesa (Daraja) Settings

Add your Daraja credentials:

```env
MPESA_CONSUMER_KEY=
MPESA_CONSUMER_SECRET=
MPESA_SHORTCODE=
MPESA_PASSKEY=
MPESA_CALLBACK_URL=https://hotspot.yourdomain.com/path-to-callback
```

- The callback URL must be reachable over HTTPS.
- It must match the URL configured in the Daraja portal.

#### MikroTik Settings

Router connection details are stored in the database (Router model) and configured via the admin panel:

- Host/IP and API port
- API username and password
- Optional description/site name

The MikroTik hotspot login page should send the voucher code (and router context if needed) to Mawimbi’s voucher login endpoint.

For usage tracking, MikroTik can optionally call back to Mawimbi on logout or session timeout (to record time/bytes and mark vouchers as consumed).

---

## Core Flows

### 1. Payment → Voucher → Hotspot User

1. User selects a plan and router in Mawimbi and enters their phone number.
2. Mawimbi creates a pending `Payment` record and initiates an M-Pesa STK push.
3. The user receives the STK prompt, enters their M-Pesa PIN, and confirms the payment.
4. Safaricom calls Mawimbi’s STK callback URL with the transaction result.
5. The callback controller:
   - Finds the corresponding `Payment`
   - Updates its status (`successful` or `failed`)
   - Stores the full callback payload for audit
6. If the transaction is successful, Mawimbi:
   - Creates a `Voucher` for the selected router and plan/profile
   - Assigns a generated voucher code
   - Sets expiry and other metadata (face value, customer phone, etc.)
   - Optionally creates/enables a MikroTik hotspot user via API using the voucher code and plan parameters
7. The user receives or can view the voucher code and uses it on the hotspot login page.

### 2. Voucher Login Flow

1. User connects to Wi-Fi and is redirected to the MikroTik hotspot login page.
2. The login page posts the voucher code (and router/context info) to Mawimbi’s voucher login endpoint.
3. Mawimbi looks up the `Voucher` by code and router and validates:
   - Status is `new` or `active`
   - Voucher is not expired
   - Device limit has not been exceeded
4. If the voucher is valid, Mawimbi:
   - Creates or finds a corresponding `HotspotUser`
   - Links the voucher to the hotspot user
   - Increments the used device count
   - Returns a response (e.g. `ok`, `mk_user`, `mk_pass`) in the format required by the MikroTik login page
5. MikroTik uses the returned credentials to log the user in and start the session.
6. If the voucher is invalid (expired, used up, wrong router, etc.), Mawimbi returns an error and the login page shows an appropriate message.

### 3. Logout and Session Tracking (Planned)

- On logout or session timeout, MikroTik can send session usage details (time, bytes) back to Mawimbi.
- Mawimbi can update `Voucher` and `HotspotUser` records with usage data.
- Depending on the plan type, Mawimbi can:
  - Mark vouchers as fully used, or
  - Decrement remaining quota/time/data.

---

## Deployment

### Manual Deployment (Current)

On the server:

```bash
cd /var/www/mawimbi-billing
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force

php artisan config:cache
php artisan route:cache
```

Ensure your web server and PHP-FPM are reloaded or restarted if needed.

### Planned CI/CD (GitHub Actions)

Intended flow:

1. Push changes to the `main` branch from your dev machine.
2. GitHub Actions workflow triggers on `push` to `main`.
3. The workflow:
   - SSHs into the VPS
   - Runs a `deploy.sh` script that:
     - Pulls the latest code
     - Installs dependencies
     - Runs migrations with `--force`
     - Clears and rebuilds caches

This makes deployment a simple `git push` once the workflow and script are in place.

---

## Status

Mawimbi Billing is under active development in a private repository.

This README is written primarily for internal use and for “future you” to quickly remember how the system is structured, deployed, and how the core billing and hotspot flows work end-to-end.
