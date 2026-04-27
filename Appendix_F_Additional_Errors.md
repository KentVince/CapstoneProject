# APPENDIX F (CONTINUED)
## ERRORS FOUND AND LACKING FUNCTIONALITIES

---

### 25.
**Error Found / Lacking Functionality:**
The `storage` folder of the Laravel project was being tracked by Git. This caused conflicts every time the repository was pulled or deployed, because the storage folder contains runtime-generated files (compiled views, file uploads, logs) that differ between environments. Team members encountered merge conflicts and broken symlinks when syncing the project, and uploaded files were sometimes overwritten or lost during a `git pull`.

**Action Taken:**
The `storage` folder was removed from Git tracking entirely using `git rm -r --cached storage`. The `.gitignore` file was updated to exclude the storage directory so it would never be re-added. The necessary empty placeholder directories (`storage/app/public`, `storage/framework/cache`, `storage/logs`) were preserved using `.gitkeep` files. The `php artisan storage:link` command was documented as a required setup step after cloning the repository to restore the public storage symlink.

---

### 26.
**Error Found / Lacking Functionality:**
All pages in the Filament admin panel crashed for MAGRO/agricultural professional users immediately after login. No page could be loaded — every navigation attempt produced an error. This completely blocked agricultural professional users from using the admin panel.

**Action Taken:**
The root cause was identified in the navigation sidebar badge logic. A badge count was being computed for a navigation item by querying a relationship or model that did not exist or was inaccessible for the agricultural professional role. Because the badge callback ran on every page load for every navigation item, this threw an unhandled exception that crashed the entire panel for MAGRO users. The fix was to wrap the badge query in a role check so it only runs for user roles that have access to that data, and to add a try-catch fallback returning zero so a badge query failure can never bring down the full navigation.

---

### 27.
**Error Found / Lacking Functionality:**
Push notifications via Firebase Cloud Messaging (FCM) were not being delivered to users whose device tokens had become stale or expired. When the server attempted to send a notification to an outdated token, the FCM API returned a token-invalid error but the system had no fallback to retry or update the token, causing notifications to silently fail for affected users.

**Action Taken:**
The FCM notification sending logic was updated to handle stale-token responses from the FCM API. When FCM returns a token registration error, the system now attempts a fallback: the outdated token is removed from the user's record and a flag is set so the next app launch will re-register the device token. Notification delivery errors are logged with the affected user ID so they can be monitored. This ensures that temporary token expiry does not permanently break push notification delivery for a user.

---

### 28.
**Error Found / Lacking Functionality:**
Agricultural professional users were unable to log in to the Filament admin panel. The default Filament login form only accepted a username/password combination matched against the `users` table, but agricultural professionals are stored separately and authenticate using their email address through a different credential flow. Attempts to log in returned a generic authentication failure with no indication of the correct login method.

**Action Taken:**
A custom `Login` page class was created at `app/Filament/Pages/Auth/Login.php` extending Filament's default `AuthLogin`. The `authenticate()` method was overridden to detect whether the login input is an email address using `FILTER_VALIDATE_EMAIL`. If it is an email, the request is routed to a new `AgriculturalProfessionalAuthService` which checks whether the email belongs to a professional and validates the password against the agricultural professionals table. On success, the professional's linked user account is logged in via `Auth::login()`. If the input is not an email (i.e., a username), the flow falls through to the standard Filament `parent::authenticate()` for regular admin users.

---

### 24.
**Error Found / Lacking Functionality:**
In the Flutter mobile app, farmer users viewing the Soil Fertility screen were unable to see their own soil sample records after a data refresh. Because soil sample records saved to the Hive `soil_samples` box had a null `farm_id` value, the system could not associate existing records back to the farmer's farm. This caused the screen to appear empty or show incomplete data even when soil analysis records existed on the server for that farmer.

**Action Taken:**
The root cause was the same as the expert user issue — soil records were being written to Hive without a resolved `farm_id`. The fix ensured that when saving a farmer's soil record to Hive, the `farm_id` is always resolved: first from the server-returned value in the sync response, and then falling back to `UserSession.effectiveFarmId` (the farmer's own farm ID from their session). During `_refreshRecommendations()`, `farmId` is captured from the session at the start and used as the fallback for all records fetched under that farmer's `app_no`. Existing Hive records with null `farm_id` are backfilled with the current session's `farmId` on the next refresh, since all records returned for a given `app_no` belong exclusively to that farmer's farm.

---

### 17.
**Error Found / Lacking Functionality:**
In the Flutter mobile app, when an MAGRO/expert user switched to a specific farm, the Pest Detection screen did not filter records by the selected farm. All pest detection records from all farms were displayed regardless of which farm the expert had selected, making it impossible for experts to review only a specific farm's data.

**Action Taken:**
The root cause was identified in `PestAndDiseaseController.php`: the `formatDetection()` method was not returning `farm_id` and `farmer_id` in its response. As a result, pest detection records saved to the local SQLite database (LocalDB) had null `farm_id` values. The fix applied was: (1) `formatDetection()` was updated to include both `farm_id` and `farmer_id` in its response payload; (2) `_loadRecords()` in the Flutter Pest Detection screen was updated to query `WHERE farm_id = ?` using `UserSession.effectiveFarmId`; and (3) `LocalDB.updateValidationStatus()` was updated to accept a `farmId` parameter to backfill `farm_id` on existing null records during the next refresh.

---

### 18.
**Error Found / Lacking Functionality:**
In the Flutter mobile app, the Soil Fertility screen for MAGRO/expert users did not filter soil sample records by the selected farm. When an expert switched farms, the screen continued to show soil samples from all farms stored in the local Hive database, rather than only those belonging to the currently selected farm.

**Action Taken:**
The Soil Fertility screen was updated so that `_refreshRecommendations()` captures `farmId = UserSession.effectiveFarmId` at the start of the refresh. When writing soil records to the Hive `soil_samples` box, the `farm_id` is now always resolved — preferring the server-returned value and falling back to the current session's `farmId`. The display filter in the `ValueListenableBuilder` was updated to filter entries by `farm_id == selectedFarmId` for expert users, while still always showing unsynced local records. A loading spinner was also added for the case when `sortedEntries.isEmpty && _isRefreshing`.

---

### 19.
**Error Found / Lacking Functionality:**
Pest detection records were being saved with an incorrect area value. Instead of using the farmer's registered barangay name (e.g., "Mainit, Maragusan"), the app used reverse geocoding from the GPS coordinates, which returned administrative region strings like "Maragusan (San Mariano), Davao Region". This caused the barangay filter on the dashboard and print reports to fail to match detection records, since the stored area did not match any registered barangay name.

**Action Taken:**
Reverse geocoding was removed as the primary area resolution method. The correct approach was implemented across the Flutter app: when saving a pest detection, the app reads `barangay` and `municipality` from the Hive store (populated at login). The detection's `area` field is set to the resolved `"Barangay, Municipality"` string using these registered values. Fallback order is: (1) Hive barangay+municipality, (2) LocalDB farm record, (3) reverse geocoding as a last resort only. Existing "Unknown area" detection records are patched during `_loadRecords()` using the Hive-stored barangay and municipality.

---

### 20.
**Error Found / Lacking Functionality:**
The farmer login API was returning raw municipality and barangay codes (e.g., `"05"`) for `farmer_address_bgy` and `farmer_address_mun` instead of resolved human-readable names (e.g., `"Mainit"`, `"Maragusan"`). The Flutter app stored these raw codes in Hive, causing the area field on pest detections to display code values instead of proper barangay names, and breaking all barangay-based filtering and reporting.

**Action Taken:**
The farmer login route in `routes/api.php` was updated to resolve barangay and municipality codes to names server-side before returning them. The resolution uses `Barangay::where('code', $code)->value('barangay')` and `Municipality::where('code', $code)->value('municipality')` — the same pattern already used for expert/MAGRO login. Both the `farmer` and `farmer.farm` objects in the login response now return resolved `barangay` and `municipality` name fields. Users were required to log out and log back in once after this fix to get the resolved names stored in their Hive cache.

---

### 21.
**Error Found / Lacking Functionality:**
The home screen panel displayed for MAGRO/expert users in the Flutter app had invisible text and icons. The panel background color was a light green (`Color(0xFFE8F5E9)`) but the text and icon colors were white, making all content unreadable on that panel for expert-role users.

**Action Taken:**
The text and icon colors on the MAGRO/expert home screen panel were changed from white to dark green (`Color(0xFF1E5631)`), which provides sufficient contrast against the light green (`Color(0xFFE8F5E9)`) background. All labels, values, and icon widgets within the expert panel were updated to use this dark green color.

---

### 22.
**Error Found / Lacking Functionality:**
In the Filament admin panel, role permissions were not functioning correctly. Certain user roles could access pages or perform actions they were not authorized for, while other roles were incorrectly blocked from pages they should be able to access. This created a security and usability issue across the admin panel.

**Action Taken:**
Role-based access control was reviewed and corrected across the Filament resources and pages. The role definitions and permission checks were updated so that each role (administrator, agricultural professional, MAGSO staff) is restricted to only the pages and actions appropriate to their level of access. Authorization logic in resource classes and page policies was aligned with the intended permission matrix for the CofSys system.

---

### 23.
**Error Found / Lacking Functionality:**
The `pest_and_disease.area` column stored values in the format `"Barangay, Municipality"` but the dashboard barangay filter was not correctly matching records for a selected barangay. Records where the area exactly equaled just the barangay name (without municipality) were missed, causing incomplete counts in the filtered dashboard view.

**Action Taken:**
The barangay filter query condition in `app/Filament/Pages/Dashboard.php` was updated to use a two-condition OR pattern: `WHERE area LIKE 'BarangayName,%' OR area = 'BarangayName'`. This ensures both the standard `"Barangay, Municipality"` format and any legacy records storing only the barangay name are correctly matched when a barangay filter is applied.

---
