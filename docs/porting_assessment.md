# Cross-Platform Feature Porting Assessment

## Objective
Evaluate how feasible it is to bring functionality from the PHPFox Legacy, Dolphin Remake, and Cheetah platforms into Ning2.0 while keeping the ongoing PHP 8.4 modernization on track.

## Source Platform Highlights
- **PHPFox Legacy** – Modular social networking features (profiles, feeds, messaging) built on a procedural core with legacy MySQL queries and flat permission rules.
- **Dolphin Remake / Cheetah** – Component-driven PHP application with extensive builder UIs, granular privacy controls, and a mixed PHP/HTML templating system.

## Compatibility Snapshot
| Area | Ning2.0 Baseline | PHPFox Legacy | Dolphin / Cheetah | Porting Considerations |
| --- | --- | --- | --- | --- |
| **PHP Version** | Modernizing toward PHP 8.4 | PHP 5-era constructs (need rewrites) | PHP 7+ with partial 8 support | Align to PHP 8.4, refactor deprecated APIs. |
| **Database Layer** | PDO factory scaffolding, legacy mysql_* usage elsewhere | MySQL procedural queries | MySQLi / custom abstraction | Standardize on PDO, design migrations for schema gaps. |
| **Authentication & Roles** | Single-network admin focus | Basic user roles, admin CP | Advanced ACLs, granular privacy | Map Ning identities to unified user/role service before importing features. |
| **Templating** | Mixed PHP/HTML views | PHP templates with inline logic | Dolphin templating language | Decide on common rendering strategy; consider extracting shared view helpers. |
| **Extensions/Modules** | Widgets with global state | Module marketplace structure | Page builders, blocks, AJAX services | Define module interface layer in Ning2.0, wrap inbound modules in adapters. |

## Incremental Porting Strategy
1. **Stabilise Core Services** – Finish PDO migration, configuration normalization, and request/environment helpers so Ning2.0 offers dependable entry points.
2. **Introduce Module Contracts** – Document the expected lifecycle (install, enable, upgrade) and data access patterns to host imported modules safely.
3. **Build Data Migration Utilities** – Create importers that map PHPFox and Dolphin schema entities to Ning models, handling passwords, media, and privacy flags.
4. **Port Priority Features** – Start with self-contained services (e.g., profile fields, notifications) that require minimal UI rework. Implement adapter layers around data access and permission checks.
5. **Validate Templating Compatibility** – For complex UI components, either port templates into Ning’s widget system or invest in a shared rendering engine (e.g., Twig) and convert progressively.
6. **Consolidate Admin UX** – Ensure super-admin dashboards can toggle imported modules per network and manage automatic updates from GitHub.

## Risks & Mitigations
- **Legacy Dependencies** – Audit each source module for third-party libraries and replace or bundle modern equivalents.
- **Schema Conflicts** – Prototype migration scripts in a scratch database; keep reversible migrations to prevent data loss.
- **Permission Drift** – Define a canonical role/permission matrix before importing modules with bespoke ACLs.
- **Maintenance Overhead** – Track ported modules in `AGENTS.md` files with modernization status to avoid regressions.

## Recommended Next Steps
- Document the desired module contract and configuration schema in Ning2.0.
- Identify two representative modules (e.g., profile custom fields from PHPFox and page builder blocks from Cheetah) for pilot ports.
- Expand automated tests around the PDO factory and environment helpers to catch regressions introduced by foreign code.
- Plan incremental releases so the platform remains deployable on shared hosting throughout the porting effort.

## Multi-Network Modernization Goals
- Mirror a WordPress Multisite-style experience: every signup provisions a dedicated network space
  with its own admin dashboard, optional subdomain, custom base path, and alias domain support while
  sharing a unified identity provider.
- Replace the single flat database with a connection registry that can point each network at either a
  dedicated schema or an isolated database while leaving global super-admin data in the primary
  catalog. Record compatibility notes for both MySQL and MariaDB as tooling is implemented.
- Port PHPFox profile field editors, Dolphin/Cheetah builders, and notification systems into modular
  Ning services so networks can enable or disable features independently without diverging from the
  core upgrade path.
- Expand installer automation to detect server capabilities, seed cron-friendly schedules, and stage
  GitHub-driven automatic updates that super administrators can trigger across all networks while
  respecting network-level opt-out flags.
- Track gaps in real-time collaboration, AJAX widgets, and browser offloading strategies per module in
  the scoped `AGENTS.md` files so new JavaScript helpers and REST endpoints can be introduced
  iteratively.
