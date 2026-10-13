# Deployment Checklist — LOHA MANDI go-live

Target: **https://lohamandigzb.online** (Hostinger, hPanel FTP/File Manager)
Authored: 18 Aug 2026 · Base: local workspace `C:\wamp64\www\loha-mandi-gzb`

> Do this AFTER backup. Verify each step at the end with the live smoke test section.

---

## 1. Pre-flight (local)

- [ ] Confirm local build passes QA: see `QA_SIGNOFF.md` (PASS conditional).
- [ ] Zip a backup of the entire live site first (hPanel File Manager → Compress, or FTP download) — **rollback required**.
- [ ] Note current live `.env` values (DB credentials, Razorpay keys, JWT secret, any `RECAPTCHA_SITE_KEY`). You will merge Firebase/reCAPTCHA lines into it — do **not** blindly overwrite it with `.env.production` (that template's secrets are placeholders).

## 2. Files to upload (overwrite)

Upload all of these from the local repo root to the live docroot (same relative paths). The local repo is the source of truth — overwrite even files you think already match.

```
index.php
.htaccess
composer.json
composer.lock
app/                            (entire directory – controllers, models, views, config, libraries)
writable/loha-mandi-gzb-firebase-adminsdk-fbsvc-26dc397bdf.json   (service account - keep in writable/)
```

Do **not** upload:
- `.env` from local (it contains local/baseURL settings). Manage `.env` on the server instead (step 3).
- `.env.production` (template only; contains placeholders and a live DB password comment — git-ignored, keep it out of the repo).
- `writable/uploads/*`, `writable/session/*`, `writable/logs/*`, `writable/cache/*`, `writable/debugbar/*` (runtime data, server-specific).

### Files with the functional fixes packed into `app/` (changed recently):
- `app/Views/account/login_otp.php` — non-blocking reCAPTCHA Enterprise + verifier reuse (fixes "no request placed")
- `app/Views/home.php` — removed 10s OTP popup/auto-redirect
- `app/Controllers/Account/Auth.php` — seller subscription gate at login, Firebase login fixes
- `app/Controllers/Account/AccountBase.php` — `sellerGate()` helper
- `app/Controllers/Account/Rfqs.php` — buyer `isBuyer` fix
- `app/Models/RfqSellerModel.php` — `rfq_status` for seller list
- `app/Views/account/rfqs.php` — seller Reply button + correct RFQ id/status
- `app/Views/account/rfq_view.php` — seller Send Quotation button
- `app/Controllers/Account/Chats.php` — new `newChat()` method
- `app/Views/account/chats.php` — **+ New Chat** button
- `app/Views/account/chats_new.php` — **NEW file** (seller picker)
- `app/Config/Routes.php` — `account/chats/new` route

## 3. Server `.env` (edit on server, via hPanel file editor or FTP)

Merge into the server's existing `.env`. Keep existing secrets untouched; update only:

```ini
CI_ENVIRONMENT = production
app.baseURL = 'https://lohamandigzb.online/'

# Firebase (OTP login) — confirm these exist / match
FIREBASE_API_KEY = 'AIzaSyA0huw61qUeSzXCZiQx8657EADUYxFiTBY'
FIREBASE_AUTH_DOMAIN = 'loha-mandi-gzb.firebaseapp.com'
FIREBASE_PROJECT_ID = 'loha-mandi-gzb'
FIREBASE_STORAGE_BUCKET = 'loha-mandi-gzb.firebasestorage.app'
FIREBASE_MESSAGING_SENDER_ID = '944013061885'
FIREBASE_APP_ID = '1:944013061885:web:b0cf76de8e031e03eb5894'
FIREBASE_MEASUREMENT_ID = 'G-7EPZG6R32C'

# reCAPTCHA Enterprise – if you already have a key set on the server (e.g. 6LeL6Ygt…),
# keep it. Otherwise LEAVE EMPTY — OTP sending is now non-blocking either way.
RECAPTCHA_SITE_KEY = '6LeL6YgtAAAAAGEqOyzDnbOx2cwiewB5LQYHlsb4'   # or ''
RECAPTCHA_API_KEY = ''                                              # Google Cloud API key for assessments (optional)

# Server-side Firebase admin credentials
FIREBASE_CREDENTIALS = './writable/loha-mandi-gzb-firebase-adminsdk-fbsvc-26dc397bdf.json'
GOOGLE_APPLICATION_CREDENTIALS = './writable/loha-mandi-gzb-firebase-adminsdk-fbsvc-26dc397bdf.json'
```

- [ ] Keep DB credentials / Razorpay keys / `jwt.secret` exactly as already configured on the live `.env`.
- [ ] Ensure `writable/` (session, cache, logs, uploads) remains writable by PHP.
- [ ] Run `composer install --no-dev --optimize-autoloader` on the server if `vendor/` is absent or stale.
- [ ] Clear caches: delete `writable/cache/*` and restart PHP (hPanel → PHP services / restart, or clear opcache).

## 4. Firebase console toggles (console.firebase.google.com → project `loha-mandi-gzb`)

- [ ] **Billing:** Project on **Blaze (pay-as-you-go)** plan — must be active for SMS (currently active).
- [ ] **Authentication → Sign-in method → Phone:** enabled; allowed region `+91` (India).
- [ ] **Authentication → Settings → Authorized domains:** confirm ALL of these are present:
  - `lohamandigzb.online`
  - `localhost`
  - `127.0.0.1`
  (Missing hosts cause `CAPTCHA_CHECK_FAILED: Hostname match not found`.)
- [ ] **Authentication → Settings → reCAPTCHA Enterprise** (recommended): create/link a Google Cloud **reCAPTCHA Enterprise** site key registered for `lohamandigzb.online`, then put that key in `RECAPTCHA_SITE_KEY` and a Cloud API key in `RECAPTCHA_API_KEY` (only then does server-side risk-scoring run; **not required** to send OTP).
- [ ] Do NOT enable App Check enforcement (this app does not initialize App Check — enforcement would block phone auth).

## 5. Post-deploy verification (on live URL)

- [ ] `https://lohamandigzb.online/login/otp` → HTTP 200, page contains `sendOtp` button and `window.FIREBASE_CONFIG` with the 6 Firebase values.
- [ ] **OTP smoke (real browser/Incognito, NOT automation):** enter a test mobile → Send OTP → SMS arrives → enter code → redirected. Expected landing:
  - subscribed seller/buyer → `/account`
  - unsubscribed seller → `/account/subscribe` (forced gate)
- [ ] `/account/chats` (buyer login) → shows **+ New Chat** → picker lists active sellers → chat thread opens.
- [ ] Seller login (unsubscribed) → forced to `/account/subscribe`; after payment, next login goes to `/account`.
- [ ] Seller inbox `/account/rfqs` → **Reply** on open RFQs; RFQ detail → **Send Quotation**.
- [ ] Home page `/` → loads and **does not** auto-open/redirect to login after 10s.

## 6. Rollback

- Restore the backup zip created in step 1 (files) + the previous `.env` from your saved copy.

---
*Run again on live after 24h soak. Report any regression to the QA record in `QA_SIGNOFF.md`.*