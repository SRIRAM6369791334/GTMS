# 16 — File Upload & Storage System

> Index: [PROJECT_MASTER_INDEX.md](PROJECT_MASTER_INDEX.md). Security findings: [18-security-audit.md](18-security-audit.md) S1, S6, S8.

## 1. Two storage roots — VERIFIED
| Root | How written | Who | URL |
|---|---|---|---|
| `public/uploads/<module>/...` | `$file->move(public_path(...))`, chunk assembly | lease (`uploads/lease_applications/<app_no>/`), DGPS, legacy B2, EC certificate, users (`uploads/users/`), invoice seal (`uploads/invoice_seals/custom_seal.<ext>`), chunk engine (`uploads/<target_module>/REF-<id>/`) | `asset($doc->file_path)` (29 occurrences in 15 views) |
| `storage/app/public/...` (`public` disk) | `$file->storeAs($dir, $name, 'public')` | environment unified (`EnverionsoneController` store / upload / add), PPT (`uploads/ppt`), EC compliance (`uploads/compliance`) | via the `public/storage` symlink (`php artisan storage:link` in the entrypoint) or `Storage::disk('public')->download` |
Remove endpoints try `public_path($p)`, `public_path('storage/'.$p)` and `storage_path('app/public/'.$p)`, because the stored path does not say which root it belongs to.

## 2. Validation on direct uploads — VERIFIED
* Extension allow-list (client-supplied extension) shared by all module controllers: pdf, png, jpg, jpeg, webp, **svg**, gif, bmp, zip, rar, 7z, tar, gz, doc(x), ppt(x), xls(x), txt, csv, rtf, kml, kmz, **xml**, dwg, dxf, mp4, webm, mkv, avi, mov; surveys / EC / env add las, laz, tif, tiff, shp, dat, ply, rnx, obs.
* Max `524288000` bytes (500 MB) in validation, but PHP limits are 100 MB upload / 120 MB post (`docker/php.ini`), so larger files must use the chunk engine.
* No MIME sniffing on direct uploads. MIME is detected (finfo) only after chunk assembly, and only recorded.
* User images: `image|mimes:jpeg,png,jpg,gif,webp|max:2048`. Seal: `image|mimes:jpeg,png,jpg,webp,svg|max:5120`.

## 3. Chunked upload engine — VERIFIED (`app/Services/ChunkedUploadService.php`, `ChunkUploadController`)
1. `init`: token `upl_<32 random>`, `file_name = Str::slug(basename) + '.' + lower(ext)` (**the extension is preserved, never checked**), size ≤ 50 GB, chunk size 1 KB–50 MB (default 10 MB), metadata (document_name, folder_id). Chunks go to `storage/app/chunks/<token>/`. There is a 1-in-20 chance that each init purges sessions older than 24 h.
2. `upload`: moves `chunk_<i>.part`, optionally verifies SHA-256, upserts `chunked_upload_parts`, updates the count, status `uploading`.
3. `status`: uploaded / missing indices, progress.
4. `assemble`: verifies that all indices exist → status `assembled` → destination `public_path('uploads/' . trim($module,'/') . ['/REF-'.$refId])` → name `time()_rand6_<file_name>` → streams each chunk (`stream_copy_to_stream`), deleting it as it goes → SHA-256 of the whole file (compared with the client hash if given) → saves `file_path` → dispatches `ProcessUploadedMediaJob` (status `processing`).
5. Jobs: `ProcessUploadedMediaJob` (finfo MIME, dispatches `GenerateVideoThumbnailJob` for video and `VerifyFileChecksumJob`, sets `completed`) → `VerifyFileChecksumJob` (recomputes SHA-256, marks `failed` on mismatch) → `GenerateVideoThumbnailJob` (FFmpeg via `exec` with `escapeshellarg`, else a placeholder).
6. Module endpoints accept `upload_token` instead of a file and call `assembleFile($token, '<module>/<app_no>', $id)` themselves (e.g. `CustomerController@uploadDocument`).

**Security properties:** no authentication, no ownership check on the token (anyone with a token can upload to, assemble or cancel it), `target_module` is not whitelisted (path traversal, e.g. `../js`), and the extension is not checked. See S1.

## 4. Serving and access control
* Files under `public/` are served **without any authentication** by Apache (XAMPP). Anyone who knows or guesses the URL can download statutory documents, and the names (`time()_random6_slug.ext`, or fixed names such as `land_document_title.pdf` inside `uploads/lease_applications/<LA-number>/`) are partly predictable.
* **Docker / Nginx caveat (INFERRED, verify on the NAS):** `docker/nginx.conf` has `location ~* ^/(api/)?(upload/chunk|uploads|chunk-upload|...)` *before* the static and PHP locations. Every `/uploads/...` request is therefore passed to `index.php` instead of being served from disk, and Laravel has no route for `/uploads/...`. Direct links built with `asset($doc->file_path)` would then return 404 in production. A side effect is that `.php` / `.svg` files placed under `/uploads` are not executed or served there. Files written elsewhere under `public/` via traversal still are.
* Downloads through controllers: `EnverionsoneController@downloadDocument` (public disk), `EnvironmentalB2Controller@download` (`public_path`). Both are permission-gated by `environment.view`, with no branch check on the document.

## 5. Persistence (Docker)
Volumes: `${GTMS_DATA_DIR}/storage → storage`, `/uploads → public/uploads`, `/chunks → storage/app/chunks`, `/php-temp`, `/nginx-temp`, `/backups`. **`docker/nas-backup.sh` backs up only the MySQL dump** (read: `mysqldump` into `backups/`). Uploaded files are not backed up by the scripts (VERIFIED from `nas-backup.sh` lines 29-42; NAS-level snapshots are UNKNOWN).

## 6. Change-impact notes
Changing an upload path or root requires updating: the controller's upload / remove / submit file-copy logic, every `asset(...file_path)` view, the download endpoints, `CustomerController@submit` / `moveToMining` / `MiningController@moveToEnvironment` (they copy files between module folders) and the Nginx location rules.
