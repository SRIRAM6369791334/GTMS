# 17 — Integrations & Background Jobs

> Index: [PROJECT_MASTER_INDEX.md](PROJECT_MASTER_INDEX.md).

## 1. External systems — VERIFIED (no outbound integration code exists)
A grep of `app/` found no `Http::` client, Guzzle calls, mail sending or SMS. The external systems are **referenced as data only**:

| System | What GTMS stores | Where |
|---|---|---|
| MIMAS (TN mineral portal) | customer `mimas_no`, `mimas_number`, `mimas_status`; per lease `mimas_credentials` (user id, **encrypted** password, email, contact, ack no., portal_status) | customers, mimas_credentials |
| PARIVESH (MoEFCC EC portal) | `parivesh_app_no`, acknowledgement no., uploaded date; **plaintext** `parivesh_password` and `email_password` on customers / environment / PPT / EC certificates | several tables |
| SEAC / SEIAA committees | presentation stage and status on PPT; designed `ppt_agendas` table unused | ppt_applications |
| NABL labs | lab name, certificate no. on EC compliance | ec_compliances |
| Google Docs Viewer / Earth / Maps, Autodesk Viewer, MS Office protocol handlers | none. The browser opens them via `gtms-open-with.js` | client side |
| GitHub Container Registry / Watchtower | deployment only | CI / compose |
| jsDelivr CDN | Bootstrap Icons CSS | layout |

There are no webhooks, scheduled syncs, e-mail or SMS notifications. The "notifications" are in-app rows only.

## 2. Queue & jobs — VERIFIED
* Connection: Redis in production (`QUEUE_CONNECTION=redis`), `sync` in tests, `database` in `.env.example`.
* Worker: the compose service `queue` runs `queue:work redis --queue=uploads,default --timeout=3600 --tries=3 --memory=1024`. The supervisord `gtms-queue-worker` program exists but is `autostart=false`.
* Jobs (all `ShouldQueue`, no explicit `onQueue`, so they use the `default` queue; the `uploads` queue name in the worker is unused):

| Job | Trigger | Work | tries / timeout |
|---|---|---|---|
| `ProcessUploadedMediaJob` | `ChunkedUploadService::assembleFile` | finfo MIME; dispatch thumbnail (video) + checksum jobs; status `completed` | 3 / 600 s |
| `VerifyFileChecksumJob` | above | SHA-256 recompute; `failed` on mismatch | 3 / 600 s |
| `GenerateVideoThumbnailJob` | above (video) | FFmpeg frame grab via `exec`, placeholder fallback; FFmpeg is not in the Docker image (INFERRED) | 2 / 600 s |

* Failure handling: `failed_jobs` table (database). Nothing alerts on failures.

## 3. Scheduler / cron — VERIFIED
`routes/console.php` defines only `inspire`. No `Schedule` definitions exist and no cron is configured in Docker. Expiry alerts suggested by index comments (lease `end_date`, EC `expiry_date`) are **not implemented**. Abandoned chunk sessions are purged only by the 5% lottery inside `init`.

## 4. Notifications
Written directly into the `notifications` table (`Notification::create`) by `MasterPipelineService::dispatchReturnNotification`, plus demo seeding in `NotificationController`. No Laravel Notification classes exist (the `type` values such as `App\Notifications\ReturnedForCorrection` name classes that do not exist).
