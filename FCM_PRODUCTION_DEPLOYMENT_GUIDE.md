# FCM Push Notification — Production Deployment Guide

**For:** Laravel + Filament backend → Flutter mobile app push notifications via Firebase Cloud Messaging

This guide is a complete checklist derived from real production debugging. Follow the steps in order and notifications will work end-to-end. The Troubleshooting section at the bottom maps real error messages to root causes.

---

## Table of Contents

1. [Architecture Overview](#1-architecture-overview)
2. [Prerequisites](#2-prerequisites)
3. [Firebase Console Setup](#3-firebase-console-setup)
4. [Laravel Backend Configuration](#4-laravel-backend-configuration)
5. [Production Server Requirements](#5-production-server-requirements)
6. [Deploying Firebase Credentials to Production](#6-deploying-firebase-credentials-to-production)
7. [Network / Firewall Configuration](#7-network--firewall-configuration)
8. [PHP Configuration on Production](#8-php-configuration-on-production)
9. [Flutter App Configuration](#9-flutter-app-configuration)
10. [End-to-End Verification](#10-end-to-end-verification)
11. [Security Hardening](#11-security-hardening)
12. [Troubleshooting Reference](#12-troubleshooting-reference)
13. [Pre-Deployment Checklist](#13-pre-deployment-checklist)

---

## 1. Architecture Overview

```
┌────────────────────┐    HTTPS    ┌─────────────────────┐
│  Filament Admin    │ ──────────> │  Laravel Backend    │
│  (Browser)         │             │  - Bulletin saved   │
└────────────────────┘             │  - Observer fires   │
                                   └──────────┬──────────┘
                                              │ Firebase Admin SDK
                                              │ (uses cURL + HTTP/2)
                                              ▼
                                   ┌─────────────────────┐
                                   │ oauth2.googleapis.com│  Get OAuth token
                                   │ fcm.googleapis.com   │  Send push
                                   └──────────┬──────────┘
                                              │
                                              ▼
                                   ┌─────────────────────┐
                                   │  Flutter App        │
                                   │  - FCM token        │
                                   │  - Topic subscribed │
                                   └─────────────────────┘
```

**Two delivery paths used:**
- **Topic-based:** App subscribes to `all_users`, server sends to topic. Reaches everyone subscribed.
- **Token-based fallback:** Server queries `mobile_users.fcm_token` and multicasts. Catches devices that haven't subscribed to topics.

Using both gives redundancy — if topic delivery fails for some OEMs, token delivery still gets through.

---

## 2. Prerequisites

- A Firebase project (free Spark tier is fine for FCM).
- Laravel 10+ with Filament 3.x.
- Flutter 3.x with `firebase_messaging` and `firebase_core` packages.
- SSH access to the production server.
- Ability to update outbound firewall rules on the production network (work with IT/sysadmin).

---

## 3. Firebase Console Setup

### 3.1 Register Android & iOS apps

1. Go to **Firebase Console → Project Settings → Your apps**.
2. Add Android app — package name must match Flutter `applicationId` in `android/app/build.gradle`.
3. Add iOS app — bundle ID must match `ios/Runner.xcodeproj`.
4. Download `google-services.json` and place it at `flutter_app/android/app/google-services.json`.
5. Download `GoogleService-Info.plist` and place it at `flutter_app/ios/Runner/GoogleService-Info.plist`.

### 3.2 Generate Service Account credentials for the server

1. Firebase Console → **Project Settings → Service Accounts → Generate new private key**.
2. Save the downloaded JSON as `cafarm-d907a-firebase-adminsdk-fbsvc-XXXXXXXX.json`.
3. **Treat this file like a password.** Anyone with it can send arbitrary pushes and access Firebase services.

### 3.3 Verify project IDs match

The `project_id` field in the server-side service-account JSON **must equal** the `project_id` in the Flutter app's `google-services.json`. If they differ, pushes silently disappear — the server sends to project A, the app listens on project B.

```bash
# Server side
grep project_id storage/app/firebase/*.json

# Flutter side
grep project_id android/app/google-services.json
```

---

## 4. Laravel Backend Configuration

### 4.1 Install the Firebase Admin SDK

```bash
composer require kreait/laravel-firebase
```

### 4.2 `.env` configuration

```dotenv
FIREBASE_PROJECT=app
FIREBASE_CREDENTIALS=storage/app/firebase/cafarm-d907a-firebase-adminsdk-fbsvc-XXXXXXXX.json
```

> The path is relative to the Laravel project root.

### 4.3 `config/firebase.php`

The Kreait package publishes a config file. The minimum needed:

```php
'projects' => [
    'app' => [
        'credentials' => env('FIREBASE_CREDENTIALS', env('GOOGLE_APPLICATION_CREDENTIALS')),
        // ...
    ],
],
```

### 4.4 Service class

Create `app/Services/FcmNotificationService.php`. **Key design decisions:**

- Use **data-only messages** (no `notification` payload). This guarantees Flutter's background handler runs on all Android OEMs (Xiaomi, Oppo, etc. that aggressively kill background services).
- Set `AndroidConfig::priority = 'high'` so messages wake the device in Doze mode.
- Wrap every `send()` in try/catch — never let an FCM failure break the request.

```php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\CloudMessage;

class FcmNotificationService
{
    private static function androidHighPriority(): AndroidConfig
    {
        return AndroidConfig::fromArray(['priority' => 'high']);
    }

    public static function sendToTopic(string $topic, string $title, string $body, array $data = []): bool
    {
        try {
            $messaging = app('firebase.messaging');
            $data['title'] = $title;
            $data['body']  = $body;

            $message = CloudMessage::withTarget('topic', $topic)
                ->withData($data)
                ->withAndroidConfig(self::androidHighPriority());

            $messaging->send($message);
            Log::info("FCM: Notification sent to topic: {$topic}");
            return true;
        } catch (\Exception $e) {
            Log::error("FCM: Exception while sending to topic", [
                'topic' => $topic,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    // sendToMultiple(), send() to single token, etc.
}
```

### 4.5 Trigger from a Model Observer (or Event)

```php
// app/Observers/BulletinObserver.php
public function created(Bulletin $bulletin): void
{
    FcmNotificationService::sendToTopic(
        'all_users',
        "[{$bulletin->category}] {$bulletin->title}",
        strip_tags($bulletin->content),
        ['type' => 'bulletin', 'bulletin_id' => (string) $bulletin->bulletin_id]
    );
}
```

Register the observer in `AppServiceProvider::boot()`:

```php
Bulletin::observe(BulletinObserver::class);
```

> ⚠️ **Performance caveat:** synchronous FCM calls add ~1–3 sec to every request that triggers them. If FCM endpoints are blocked, the request will hang for the full HTTP timeout (default 60s) and the user sees a 504. For production, **queue the FCM calls** (`ShouldQueue` job) once you have a queue worker running.

---

## 5. Production Server Requirements

### 5.1 PHP version

- **PHP 8.1+** (this project uses 8.3).
- The `curl` extension must be loaded **for the FPM SAPI**, not just CLI.

```bash
# Verify
sudo php-fpm8.3 -i 2>/dev/null | grep -i "curl\|cURL support"
# Expect: cURL support => enabled

# If missing
sudo apt-get install -y php8.3-curl
sudo phpenmod -s fpm curl
sudo systemctl restart php8.3-fpm
```

### 5.2 cURL must support HTTP/2

Google's FCM API requires HTTP/2. Verify:

```bash
php -r "print_r(curl_version());" | grep -E 'version|features'
# version: 7.68+ supports HTTP/2 (older versions don't)
```

If `curl_version()` reports < 7.68, upgrade libcurl on the OS.

### 5.3 `disable_functions` MUST allow `curl_exec`

Many shared-hosting and government-server hardening guides ship with `curl_exec` and `curl_multi_exec` in `disable_functions`. **This silently breaks Firebase Admin SDK** — Guzzle falls back to PHP's stream handler, which doesn't support HTTP/2.

```bash
# Check
grep "^disable_functions" /etc/php/8.3/fpm/php.ini

# Bad (FCM will fail with "HTTP/2.0 is not supported by the stream handler"):
disable_functions = exec,passthru,shell_exec,system,proc_open,popen,curl_exec,curl_multi_exec,parse_ini_file,show_source

# Good (FCM works):
disable_functions = exec,passthru,shell_exec,system,proc_open,popen,parse_ini_file,show_source
```

Fix:

```bash
sudo sed -i 's/,curl_exec//g; s/,curl_multi_exec//g' /etc/php/8.3/fpm/php.ini
sudo systemctl restart php8.3-fpm
```

### 5.4 Web server

Either nginx (with `php-fpm`) or Apache (with `mod_php` or PHP-FPM) is fine. No FCM-specific config needed. If you're behind a reverse proxy, increase the upstream timeout (`proxy_read_timeout`) to handle slow FCM calls — or better, queue the jobs.

---

## 6. Deploying Firebase Credentials to Production

The service-account JSON is **gitignored on purpose** (credentials must never be in git). It will not be deployed automatically by `git pull`. You must copy it manually.

### 6.1 Copy the file

From a workstation that already has the file:

```bash
scp /local/path/to/cafarm-d907a-firebase-adminsdk-fbsvc-XXXXXXXX.json \
    user@prod-host:/var/www/html/PROJECT/storage/app/firebase/
```

If SSH from outside the network is blocked (common on `.gov.ph` hosts), use the **paste-via-nano** method:

```bash
# On your local machine — copy the contents to clipboard
sudo cat /local/path/to/cafarm-d907a-firebase-adminsdk-fbsvc-XXXXXXXX.json
# Select all output → copy

# On the production server
mkdir -p /var/www/html/PROJECT/storage/app/firebase
nano /var/www/html/PROJECT/storage/app/firebase/cafarm-d907a-firebase-adminsdk-fbsvc-XXXXXXXX.json
# Paste with Ctrl+Shift+V (or right-click)
# Save: Ctrl+O, Enter, Ctrl+X
```

### 6.2 Set ownership and permissions

The web user (typically `www-data`) must be able to read the file. Other users should not.

```bash
sudo chown www-data:www-data storage/app/firebase/*.json
sudo chmod 640 storage/app/firebase/*.json
```

### 6.3 Clear the config cache

Without this, Laravel may keep using a cached config that doesn't see the new `.env`/file:

```bash
php artisan config:clear
```

### 6.4 Verify

```bash
# File exists and is readable by www-data
sudo -u www-data cat storage/app/firebase/cafarm-*.json | head -3

# Laravel can resolve the path
php artisan tinker --execute="echo config('firebase.projects.app.credentials');"
```

---

## 7. Network / Firewall Configuration

The production server must be able to make **outbound HTTPS (port 443)** to:

| Hostname | Purpose |
|---|---|
| `oauth2.googleapis.com` | Service-account OAuth token exchange |
| `fcm.googleapis.com` | Push delivery endpoint |
| `firebase.googleapis.com` | (Auxiliary Firebase services) |
| `*.googleapis.com` | (Easier blanket allow for IT) |

### 7.1 Verify reachability from production

```bash
curl -v --max-time 10 https://oauth2.googleapis.com/token 2>&1 | head -20
curl -v --max-time 10 https://fcm.googleapis.com 2>&1 | head -20
```

A successful run shows `Connected to ... port 443` and a TLS handshake completing. A blocked port shows `Connection refused`, `Connection timed out`, or `Could not resolve host`.

### 7.2 If blocked

Open a ticket with your network/IT team to **whitelist outbound HTTPS** to the hostnames above. This is a one-time change. On government and corporate networks this is often the longest step (waiting on IT).

> **Note:** opening one (e.g. `oauth2.googleapis.com`) does NOT automatically open the other. Each FQDN may be filtered separately. Always test both.

---

## 8. PHP Configuration on Production

Summary of hard requirements (verified during this debugging session):

| Setting | Required value | Why |
|---|---|---|
| `php-fpm` `cURL` extension | loaded | Required for HTTPS to Google APIs |
| `disable_functions` | must NOT include `curl_exec`, `curl_multi_exec` | Guzzle uses these to send HTTP requests |
| libcurl version | ≥ 7.68 | HTTP/2 support (FCM rejects HTTP/1) |
| `max_execution_time` | ≥ 30s | Allow time for OAuth+send round-trip |
| `APP_ENV` | `production` | Don't expose stack traces publicly |
| `APP_DEBUG` | `false` | Same — security |

After any change to `php.ini`:

```bash
sudo systemctl restart php8.3-fpm
```

After any change to `.env`:

```bash
php artisan config:clear
```

---

## 9. Flutter App Configuration

### 9.1 `pubspec.yaml`

```yaml
dependencies:
  firebase_core: ^2.x.x
  firebase_messaging: ^14.x.x
```

### 9.2 `main.dart` initialization

```dart
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';

@pragma('vm:entry-point')
Future<void> _firebaseBackgroundHandler(RemoteMessage message) async {
  await Firebase.initializeApp();
  // Optional: show local notification here for guaranteed display
}

Future<void> main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await Firebase.initializeApp();
  FirebaseMessaging.onBackgroundMessage(_firebaseBackgroundHandler);
  runApp(const MyApp());
}
```

### 9.3 Subscribe to topics + register token

```dart
final messaging = FirebaseMessaging.instance;

// Request permission (iOS + Android 13+)
await messaging.requestPermission();

// Subscribe to broadcast topic
await messaging.subscribeToTopic('all_users');

// Send token to backend so it can address this device individually
final token = await messaging.getToken();
await api.post('/mobile/fcm-token', { 'fcm_token': token });

// Listen for token rotations
messaging.onTokenRefresh.listen((newToken) async {
  await api.post('/mobile/fcm-token', { 'fcm_token': newToken });
});

// Foreground messages
FirebaseMessaging.onMessage.listen((RemoteMessage msg) {
  // Show in-app banner / local notification
});
```

### 9.4 Android manifest essentials

In `android/app/src/main/AndroidManifest.xml`:

```xml
<uses-permission android:name="android.permission.INTERNET"/>
<uses-permission android:name="android.permission.POST_NOTIFICATIONS"/>
<uses-permission android:name="android.permission.WAKE_LOCK"/>
```

For data-only messages to display visibly, you typically also use `flutter_local_notifications` to render the notification yourself in `_firebaseBackgroundHandler`.

### 9.5 Build with the right `google-services.json`

Make sure the `google-services.json` in `android/app/` matches the same Firebase project as the server's service-account JSON (`project_id` field must match exactly).

---

## 10. End-to-End Verification

1. **Backend log when bulletin is created** (`tail -f storage/logs/laravel.log`):
   ```
   [TIMESTAMP] production.INFO: FCM: Notification sent to topic: all_users
   [TIMESTAMP] production.INFO: BulletinObserver: Bulletin #N sent to 'all_users' topic.
   [TIMESTAMP] production.INFO: FCM: Multicast sent {"success":N,"failures":0}
   ```

2. **Phone receives push** within ~1–5 seconds.

3. **Tap notification** → app opens to relevant screen (deep-link/data payload routing).

If step 1 succeeds but step 2 doesn't, the problem is on the Flutter side (topic subscription, project mismatch, or device-specific aggressive battery management).

---

## 11. Security Hardening

- **Rotate the service-account key** if it has ever been exposed (chat, screenshot, accidentally committed). Firebase Console → Project Settings → Service Accounts → Generate new key, then delete the old one in IAM.
- **Set `APP_ENV=production` and `APP_DEBUG=false`** on production. Verify by checking that `storage/logs/laravel.log` lines start with `production.` rather than `local.`.
- **File permissions** on the credential JSON: `640`, owned by `www-data:www-data`. Never world-readable.
- **Don't commit** `google-services.json`, `GoogleService-Info.plist`, or any service-account JSON to git. Add them to `.gitignore`.
- **Limit topic exposure.** If you use `all_users`, anyone who reverse-engineers the app can subscribe and receive every push. For sensitive content, use token-based delivery to a vetted user list.

---

## 12. Troubleshooting Reference

Real errors encountered during this deployment, in the order they appeared, with root cause and fix:

### 12.1 `SplFileObject::__construct(...): Failed to open stream: No such file or directory`

**Cause:** Service-account JSON missing on production (gitignored, never deployed).
**Fix:** Manually copy the JSON to `storage/app/firebase/`, set ownership to `www-data:www-data`, chmod 640, run `php artisan config:clear`.

### 12.2 `Unable to connect to the API: Connection refused for URI https://oauth2.googleapis.com/token`

**Cause:** Outbound HTTPS to Google blocked by network firewall.
**Fix:** Have IT whitelist `oauth2.googleapis.com` and `fcm.googleapis.com` (port 443).
**Confirm:** `curl -v --max-time 10 https://oauth2.googleapis.com/token` from production.

### 12.3 Browser shows `504 Gateway Timeout` when creating a bulletin

**Cause:** PHP synchronously waiting for FCM call that hangs against the firewall. nginx upstream timeout (default 60s) trips.
**Symptom in nginx log:** `upstream timed out (60: Operation timed out) while reading response header`.
**Fix (short-term):** Either fix the firewall (12.2) or set a short cURL timeout in the FCM service.
**Fix (proper):** Convert FCM calls into a queued job (`ShouldQueue`).

### 12.4 `Unable to connect to the API: HTTP/2.0 is not supported by the stream handler`

**Cause:** PHP's `curl_exec` is in `disable_functions`. Guzzle silently falls back to PHP's built-in stream handler, which only speaks HTTP/1. FCM requires HTTP/2.
**Fix:** Remove `curl_exec` and `curl_multi_exec` from `/etc/php/8.x/fpm/php.ini` `disable_functions` line:
```bash
sudo sed -i 's/,curl_exec//g; s/,curl_multi_exec//g' /etc/php/8.3/fpm/php.ini
sudo systemctl restart php8.3-fpm
```

### 12.5 Backend says "FCM sent" but Flutter app receives nothing

**Possible causes (check in order):**

1. **Project mismatch.** `project_id` in server JSON ≠ `project_id` in Flutter `google-services.json`.
2. **Topic not subscribed.** Add a debug log inside `subscribeToTopic('all_users')` to confirm it ran.
3. **Aggressive OEM battery management** (Xiaomi, Oppo, Huawei, Vivo). User must disable "battery optimization" / "auto-start restriction" for the app. Some OEMs require the app to be added to a "protected apps" list.
4. **Notification permission not granted** (Android 13+). App must call `requestPermission()` at first launch.
5. **App in "stopped" state** after a force-stop. Background pushes are blocked until the user opens the app once.
6. **`google-services.json` outdated** after rotating the key. Re-download and rebuild.

### 12.6 Logs say `local.INFO` instead of `production.INFO` on the production server

**Cause:** Production `.env` still has `APP_ENV=local`.
**Fix:** Set `APP_ENV=production` and `APP_DEBUG=false`, then `php artisan config:clear`.
**Why it matters:** With `APP_DEBUG=true` Laravel exposes full stack traces (including DB credentials) on any error page.

### 12.7 `php-fpm` reads a different `php.ini` than I'm editing

```bash
sudo php-fpm8.3 -i 2>/dev/null | grep "Loaded Configuration"
```
This shows the actual file being read. Edit that one.

---

## 13. Pre-Deployment Checklist

Print this and tick off before declaring "FCM done":

**Backend:**
- [ ] Service-account JSON in `storage/app/firebase/`, chmod 640, owned by web user
- [ ] `.env` has correct `FIREBASE_CREDENTIALS` path
- [ ] `php artisan config:clear` run after deploy
- [ ] `APP_ENV=production`, `APP_DEBUG=false`
- [ ] Observer registered, code path tested locally first

**Server:**
- [ ] PHP cURL extension enabled for FPM
- [ ] `disable_functions` does NOT include `curl_exec` / `curl_multi_exec`
- [ ] libcurl ≥ 7.68 (HTTP/2)
- [ ] System time accurate (NTP synced — required for JWT auth)

**Network:**
- [ ] Outbound 443 to `oauth2.googleapis.com` ALLOWED
- [ ] Outbound 443 to `fcm.googleapis.com` ALLOWED
- [ ] `curl` from prod to both endpoints succeeds (TLS handshake)

**Flutter:**
- [ ] `google-services.json` matches server's Firebase project
- [ ] `firebase_core` initialized in `main()`
- [ ] Notification permission requested at launch
- [ ] `subscribeToTopic('all_users')` called
- [ ] Token sent to backend, refresh listener registered
- [ ] Background handler annotated `@pragma('vm:entry-point')`
- [ ] Test on a real device, not emulator (FCM is unreliable on some emulators)

**End-to-end:**
- [ ] Create test bulletin → backend log shows `FCM: Notification sent to topic: all_users`
- [ ] Push arrives on phone within 5 seconds
- [ ] Tapping push opens correct screen

---

## Appendix A — Quick reference: production diagnostic commands

```bash
# Where am I?
pwd && grep APP_ENV .env

# Recent FCM activity
tail -n 50 storage/logs/laravel.log | grep -iE "fcm|bulletin"

# Confirm credentials file
ls -la storage/app/firebase/
sudo -u www-data cat storage/app/firebase/cafarm-*.json | head -3

# Test outbound network
curl -v --max-time 10 https://oauth2.googleapis.com/token 2>&1 | head -15
curl -v --max-time 10 https://fcm.googleapis.com         2>&1 | head -15

# PHP-FPM config check
sudo php-fpm8.3 -i 2>/dev/null | grep -E "Loaded Configuration|disable_functions|cURL support"

# Restart pipeline after config changes
php artisan config:clear
sudo systemctl restart php8.3-fpm
```

---

*Document version: 1.0*
*Derived from production deployment of CofSys (cofsys.davaodeoro.gov.ph) — Laravel + Filament + Flutter + Firebase Cloud Messaging.*
