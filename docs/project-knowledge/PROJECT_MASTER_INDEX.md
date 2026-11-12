# GTMS Project Knowledge Library — Master Index

Audit date 2026-11-12 · audited commit `5f3169a` · method: read-only static reverse engineering (no DB connection, no code executed, no application files changed).

**Start here:** [00-project-overview.md](00-project-overview.md) → [03-system-architecture.md](03-system-architecture.md) → [04-module-documentation.md](04-module-documentation.md) → [23-feature-change-impact-guide.md](23-feature-change-impact-guide.md).

| # | Document | Contents |
|---|---|---|
| 00 | [Project overview](00-project-overview.md) | Purpose, users, deployment status, scale, headline risks, readiness |
| 01 | [Technology stack](01-technology-stack.md) | Laravel 12 / PHP 8.2 / Spatie / MySQL 8 / Redis / Docker / NAS |
| 02 | [Folder & file inventory](02-folder-file-inventory.md) | Classified inventory, analysis depth, files not analysed |
| 03 | [System architecture](03-system-architecture.md) | Request pipeline, runtime topology, layering |
| 04 | [Module documentation](04-module-documentation.md) | 18 modules + dormant schema |
| 05 | [Controller analysis](05-controller-analysis.md) | Every controller method: route, tables, validation, response |
| 06 | [Model analysis](06-model-analysis.md) | 58 models, BranchScope, SoftDeletes, polymorphic conventions |
| 07 | [Database schema](07-database-schema.md) | 71 tables, column level, provenance-labelled |
| 08 | [Database relationships](08-database-relationships.md) | ER diagram, soft links, delete behaviour |
| 09 | [Routes & API](09-routes-api-documentation.md) | All 218 routes, problems, end-to-end traces |
| 10 | [Blade views](10-blade-view-documentation.md) | 100 views mapped to controllers; patterns; XSS spots |
| 11 | [JavaScript / frontend](11-javascript-frontend-analysis.md) | Load order, first-party scripts, JSON contracts |
| 12 | [Business logic](12-business-logic.md) | Rule catalogue with code citations; absent rules |
| 13 | [Business workflows](13-business-workflows.md) | Mermaid state / sequence diagrams per workflow |
| 14 | [Roles & permissions](14-user-roles-permissions.md) | Spatie model, catalogue, delegated admin, gaps |
| 15 | [Cross-module dependencies](15-cross-module-dependencies.md) | Shared tables / literals / services map |
| 16 | [File upload & storage](16-file-upload-storage-system.md) | Two storage roots, chunk engine, serving |
| 17 | [Integrations & background jobs](17-integrations-background-jobs.md) | External systems (data-only), queue, jobs, no scheduler |
| 18 | [Security audit](18-security-audit.md) | 20 findings, Critical → Informational |
| 19 | [Performance & code quality](19-performance-code-quality-audit.md) | P1–P10, Q1–Q14 |
| 20 | [Testing, deployment, configuration](20-testing-deployment-configuration.md) | PHPUnit / Playwright, CI, Docker, NAS scripts |
| 21 | [Known issues & risks](21-known-issues-risks.md) | Register K1–K29 + open questions for the client |
| 22 | [Glossary](22-glossary-business-terminology.md) | Mining / EC / accounting terms as used in code |
| 23 | [Feature change impact guide](23-feature-change-impact-guide.md) | 12-step procedure ending in an approval plan; module cards; checklists |
| — | [PROGRESS.md](PROGRESS.md) | Audit tracker and raw findings log |

## Evidence tags used
**VERIFIED** — read in code / config · **PARTIALLY VERIFIED** — key paths read, rest inferred from structure · **INFERRED** — deduced, needs confirmation · **UNKNOWN** — cannot be determined from the repo · **NOT APPLICABLE**. Schema facts are labelled **REPOSITORY-DEFINED** (no live DB was inspected).

## Relationship to older docs
`docs/00-…23-*.md`, `docs/GTMS_100_PERCENT_CODEBASE_FUNCTION_CATALOG.*` and `docs/database-analysis/*` predate this audit, were not used as evidence, and may be stale. Where they disagree with this library, re-check the code.
