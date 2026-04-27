# APPENDIX F
## ERRORS FOUND AND LACKING FUNCTIONALITIES

---

### 1.
**Error Found / Lacking Functionality:**
The Farmer Information table displayed an "Application No." (app_no) column that was no longer needed, cluttering the table with irrelevant data.

**Action Taken:**
The `app_no` TextColumn was removed from the Farmer Resource table definition in `app/Filament/Resources/FarmerResource.php`. The column was entirely deleted from the table columns array so it no longer appears in the Farmer Information list view.

---

### 2.
**Error Found / Lacking Functionality:**
The Dashboard filter only had a year/date filter. There was no way to filter the dashboard data (pest detections, soil analysis records, farm counts) by barangay, making it difficult for administrators to view data scoped to a specific area.

**Action Taken:**
The dashboard filter was redesigned in `app/Filament/Pages/Dashboard.php`. The year filter was replaced with a Barangay filter (defaulting to "All Barangays"). All dashboard data-fetching methods (15+ get/count methods) were updated to apply the barangay condition when a specific barangay is selected, using a `LIKE 'BarangayName,%'` pattern against the `pest_and_disease.area` column.

---

### 3.
**Error Found / Lacking Functionality:**
In the dashboard filter, the Municipality field was positioned below Barangay and was editable. The value was not pre-set to "Maragusan" and could be changed by users, causing confusion since the system is scoped to Maragusan municipality only.

**Action Taken:**
The Municipality field was moved to the top of the filter form in `app/Filament/Pages/Dashboard.php`. It was set to be disabled with a default value of "Maragusan", making it read-only while visually informing users of the active municipality scope.

---

### 4.
**Error Found / Lacking Functionality:**
The Barangay dropdown in the dashboard filter was loading barangays from all municipalities instead of only showing barangays belonging to Maragusan. Selecting a barangay from a different municipality would produce incorrect or empty filter results.

**Action Taken:**
The barangay options query was corrected in `app/Filament/Pages/Dashboard.php` to first look up the Maragusan municipality code from the municipalities table, then filter the barangays table using `->where('muni_filter', $munCode)`. This ensures only Maragusan's barangays appear in the dropdown.

---

### 5.
**Error Found / Lacking Functionality:**
The Print Report button on the Dashboard generated a basic, incorrectly formatted report. It did not follow the required MAGSO header format and did not present pest and disease distribution organized by barangay, making it unsuitable for official reporting.

**Action Taken:**
The dashboard print report route in `routes/web.php` was completely rewritten to produce a "Pest & Disease Distribution per Barangay" report. The Blade view `resources/views/filament/pages/dashboard-print-report.blade.php` was redesigned with the MAGSO three-column header (logo left, title center, CofSys logo right), summary cards (total/approved/pending/rejected/low/medium/high severity), a per-barangay distribution table with color-coded sub-headers, a top pests/diseases ranking table, and an official footer. All data is scoped to the active barangay/municipality filter.

---

### 6.
**Error Found / Lacking Functionality:**
The CofSys logo in the print report header was placed on the left side alongside the MAGSO logo, making the header unbalanced and visually inconsistent with the desired three-column layout.

**Action Taken:**
The print report header layout in `resources/views/filament/pages/dashboard-print-report.blade.php` was updated to use a three-column flexbox layout: MAGSO logo and agency details on the left, report title and subtitle in the center, and the CofSys logo on the right.

---

### 7.
**Error Found / Lacking Functionality:**
The Pest & Disease Distribution per Barangay print report was missing a Purok column. Without Purok information, the geographic breakdown of detections was incomplete and could not identify which puroks within a barangay had the most detections.

**Action Taken:**
The print route query in `routes/web.php` was updated to JOIN the farmers table and retrieve purok data. The Blade view was updated to display a Purok column in the per-barangay distribution table, grouped under each barangay section header.

---

### 8.
**Error Found / Lacking Functionality:**
The Print Report button only generated a Pest & Disease report. There was no option to generate a Soil Analysis report, leaving soil data summary with no printable report output for official use.

**Action Taken:**
The Print Report button in `app/Filament/Pages/Dashboard.php` was changed to open a modal selector instead of directly printing. A new component view `resources/views/components/print-report-selector.blade.php` was created with two card-style link options: "Pest & Disease Report" (green) and "Soil Analysis Report" (amber). A new route `dashboard.soil-report` was added in `routes/web.php`, and a full Soil Analysis print view `resources/views/filament/pages/soil-print-report.blade.php` was created with farm summary cards, average nutrient strip, pH distribution table, per-barangay nutrient levels, and a complete records table.

---

### 9.
**Error Found / Lacking Functionality:**
The Data Records section on the Dashboard included an "All Data" tab option that mixed all data types together and had no useful default, causing confusion for users who expected a focused starting view.

**Action Taken:**
The "All Data" option was removed from the dataTypeFilter select element in `resources/views/filament/pages/dashboard.blade.php`. The default selected tab was changed to "Farms" so the Data Records section always starts by displaying the farms table.

---

### 10.
**Error Found / Lacking Functionality:**
When the user selected a barangay in the dashboard filter and applied it, the Data Records table did not update to reflect the selected barangay — it continued to show data from all barangays regardless of the filter selection.

**Action Taken:**
The dashboard Blade view was updated to store reactive table data in a hidden `<div>` element populated via `@json($tableJsonData)`. JavaScript was updated to read from this div and re-render the Data Records table on every Livewire commit using `Livewire.hook('commit', { succeed })`. The server-side data methods (`getRecentFarms()`, `getRecentFarmers()`, etc.) were updated to apply the barangay filter when constructing their queries so the rendered JSON always reflects the active filter.

---

### 11.
**Error Found / Lacking Functionality:**
After applying the barangay filter on the dashboard, the Data Records table displayed "Showing 0 records" even when matching records existed for the selected barangay.

**Action Taken:**
The root cause was identified: Livewire's morphdom DOM diffing algorithm skips `<script type="application/json">` tags during re-renders, so the data store was never updated after a filter change. The fix was to move the JSON data store from a `<script>` tag to a `<div style="display:none">` element in `resources/views/filament/pages/dashboard.blade.php`. Morphdom updates `<div>` elements normally, so the data is refreshed on every Livewire update and the table renders the correct filtered records.

---

### 12.
**Error Found / Lacking Functionality:**
A Blade parse error appeared on the Dashboard: "Unclosed '[' on line 1027 does not match ')'". This prevented the dashboard page from rendering entirely.

**Action Taken:**
The error was caused by passing a multi-line PHP array literal directly inside `@json([...])`. Blade's parser cannot handle brackets spanning multiple lines inside a directive argument. The fix was to compute the array in a `@php` block first, assign it to `$tableJsonData`, and then use `@json($tableJsonData)` — passing a single variable instead of an inline multi-line array.

---

### 13.
**Error Found / Lacking Functionality:**
The Soil Analysis page had no way to import multiple records at once. All data had to be entered manually one record at a time, which was impractical for bulk entry of laboratory data from spreadsheets.

**Action Taken:**
An Excel import feature was added to `app/Filament/Resources/SoilAnalysisResource/Pages/ListSoilAnalyses.php`. A new "Import Excel" header action with a file upload modal (.xlsx format, max 10 MB) was added. A new import class `app/Imports/SoilAnalysisImport.php` was created implementing `ToCollection` and `WithHeadingRow`, pre-caching farms by name for efficient lookup, handling Excel serial date parsing, skipping duplicate `sample_id` records, and mapping all 20 Excel columns to Soil Analysis model fields. A success or warning notification is shown after import with imported/skipped counts and up to 5 error details.

---

### 14.
**Error Found / Lacking Functionality:**
The Soil Analysis Excel import did not include the `validation_status` field. Imported records had no status assigned instead of the appropriate pending/approved/rejected value already recorded in the source spreadsheet.

**Action Taken:**
A `validation_status` column was added to the import field mapping in `app/Imports/SoilAnalysisImport.php`. A `resolveValidationStatus()` helper method was added that accepts `pending`, `approved`, or `rejected` (case-insensitive) and defaults to `pending` for any unrecognized or blank value. The field is mapped during `SoilAnalysis::create()`.

---

### 15.
**Error Found / Lacking Functionality:**
The Soil Analysis Excel import did not include the `validated_by` field. The name of the validator recorded in the source spreadsheet was lost during import, making it impossible to trace who validated each record.

**Action Taken:**
The `validated_by` column was added to the import field mapping in `app/Imports/SoilAnalysisImport.php`. It is processed through the `nullIfEmpty()` helper to store null for blank cells and otherwise trims and stores the value as a string during `SoilAnalysis::create()`.

---

### 16.
**Error Found / Lacking Functionality:**
On the CofSys Map, clicking "View Soil Analysis" on any farm marker navigated to the Soil Analysis page but displayed all soil analysis records from every farm instead of only the records belonging to the selected farm. There was no way to view soil data scoped to a specific farm from the map.

**Action Taken:**
The root cause was identified as a Filament v3 API misunderstanding: the page was overriding `getEloquentQuery()`, which does not exist on Filament v3 `ListRecords` page classes (it only exists on Resource classes). Because the method was never called by Filament, the farm filter was silently ignored. The fix in `app/Filament/Resources/SoilAnalysisResource/Pages/ListSoilAnalyses.php` was to: (1) capture `?farm_id=` from the URL in `mount()` into a `$filteredFarmId` property, which Livewire serializes in its snapshot so the filter persists across 15-second table polling intervals; and (2) override `getTableQuery()` — the correct Filament v3 page-level method — to apply `WHERE soil_analysis.farm_id = ?` when `$filteredFarmId` is set, and display a "Filtered by farm: [Farm Name]" subheading to confirm the active filter.

---
