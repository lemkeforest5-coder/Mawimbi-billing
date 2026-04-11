# Mawimbi Wi‑Fi – Operations Guide (Version 1)

Location: Mawimbi Hotspot (MikroTik + Laravel billing)
Contact: WhatsApp 0716794826

## 1. Daily routine

1. Check internet and hotspot
   - Connect to the Wi‑Fi as a normal user.
   - Open any site; confirm the Mawimbi hotspot page appears.
   - Buy the cheapest plan (KUMI) with your own Safaricom line and confirm:
     - STK push appears on your phone.
     - After paying, a voucher code is shown and auto‑filled.
     - You get connected to the internet.

2. Check Admin Overview
   - URL: `http://hotspot.local/admin/overview`
   - Set Range = **Today**.
   - Confirm:
     - Total payments and Total revenue look reasonable.
     - Pending payments = 0 (or very low).
     - Failed payments = 0 (or very low).
     - Top profiles table shows which plans sold today.

3. Check Payments page
   - URL: `http://hotspot.local/payments`
   - Filter Status = **Failed** for Last 7 days.
   - If there are many failures, test STK again and contact support/yourself to debug.

## 2. Handling common situations

### A. Customer paid but did not get internet

1. Go to **Payments** page.
2. Search by their phone number.
3. Check:
   - Status = Successful?
   - Is there a Voucher code linked in the row?
4. If Successful and a voucher exists:
   - Open **Vouchers**, search that code.
   - Tell customer to enter that voucher on the portal.
5. If Successful but no voucher:
   - Manually create a voucher in **Vouchers → Create** with the right profile.
   - Give the code to the customer by SMS/WhatsApp.

### B. STK push not appearing

1. Ask customer to:
   - Confirm the phone number (must be 2547…).
   - Check they have Safaricom signal and M‑Pesa is working.
2. Try again.
3. If it still fails:
   - Check **Payments** for recent Failed or Pending entries.
   - If many failures, there may be an M‑Pesa or API issue. Pause sales and test later.

## 3. Editing plans and prices

1. Go to **Profiles** page.
2. For any plan (KUMI, MBAO, DAILY, etc.):
   - Click **Edit**.
   - Change Time limit, Data limit, or Price.
   - Keep the **Code** field the same unless you also update the M‑Pesa integration.
3. Save and the change will apply immediately for new vouchers/payments.

## 4. Backups

### Router

- In Winbox/WebFig:
  - System → Backup → create a backup file.
  - Download it to your PC and store safely (label with date).

### Billing system database

- Set up a simple daily cron job on the server (example for MySQL):

  ```bash
  mysqldump -u root -pYOURPASSWORD mawimbi_billing > /var/backups/mawimbi_billing_$(date +%F).sql
  ```

- Periodically copy these `.sql` files off the server.

## 5. When to call something “broken”

- If **Pending payments** on Admin Overview stay > 0 for more than 30 minutes.
- If **Failed payments** spike suddenly.
- If customers cannot load the captive portal at all.

In these cases:
1. Test KUMI purchase yourself.
2. If still failing, stop selling temporarily and investigate:
   - Check internet uplink.
   - Check M‑Pesa Daraja / API provider status.
   - Check server logs for errors.

## 6. Version 1 scope

Version 1 is considered complete when:

- Users can:
  - See the Mawimbi login page with logo.
  - Buy a voucher via M‑Pesa and be connected automatically.
  - Reuse an existing voucher code to connect again.

- You can:
  - See revenue and top plans on Admin Overview.
  - See all payments and vouchers.
  - Edit plans (profiles) and prices.
  - Restore from a router + DB backup if needed.
