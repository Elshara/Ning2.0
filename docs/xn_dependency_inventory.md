# XN\_* Dependency Inventory

The Ning 2.0 code base references a broad set of legacy `XN_*` classes and
constants. The tables below group each dependency by the domain of
functionality and highlight representative call sites within `lib/` and
`widgets/`.

## Application and Environment

| Dependency | Description | Representative usage |
| --- | --- | --- |
| `XN_Application` | Provides network metadata such as name, owner, and premium feature flags. | `lib/XG_App.php`, `widgets/index/controllers/AuthorizationController.php` |
| `XN_AtomHelper` / `XN_Atomhelper` | Builds Ning hostnames and parses Atom feeds. | `lib/XG_MetatagHelper.php`, `widgets/index/controllers/EmbedController.php` |
| `XN_Debug` | Toggles diagnostic logging. | `test/test_header.php` |
| `XN_PHP_START_TIME` | Timestamp constant used by performance logging. | `lib/XG_PerfLogger.php` |
| `XN_ALLOW_ADMIN_ONLY` | Flag used to gate administrative messaging templates. | `lib/XG_Messages.php` |
| `XN_SECURITY_TOKEN` | Token constant referenced by outbound messaging. | `lib/XG_Message.php` |

## Profiles and Membership

| Dependency | Description | Representative usage |
| --- | --- | --- |
| `XN_Profile` | Represents Ning user accounts and handles authentication. | `lib/XG_SecurityHelper.php`, `widgets/index/controllers/AuthorizationController.php` |
| `XN_ProfileSet` | Tracks mailing/broadcast sets for notifications. | `widgets/index/lib/helpers/Index_NotificationHelper.php`, `lib/XG_App.php` |
| `XN_Profiles` | Accesses profile collections and friend data. | `widgets/groups/models/Group.php` |
| `XN_Invitation` / `XN_Invitations` | Manage invitation objects. | `widgets/index/controllers/InvitationController.php` |
| `XN_Contact`, `XN_ContactImportService`, `XN_ContactImportResult`, `XN_ContactImportServices`, `XN_ImportedContact` | Support importing contacts from external providers. | `widgets/index/controllers/InvitationController.php` |

## Content, Queries, and Search

| Dependency | Description | Representative usage |
| --- | --- | --- |
| `XN_Content` | CRUD wrapper for content records (photos, blog posts, etc.). | `lib/XG_Cache.php`, `widgets/photo/controllers/PhotoController.php` |
| `XN_Query` | Query builder for content and profile data. | `lib/XG_Query.php`, `widgets/video/controllers/VideoController.php` |
| `XN_Filter` | Defines filter clauses used with `XN_Query`. | `lib/XG_SecurityHelper.php` |
| `XN_Attribute` | Enumerates attribute types used when storing metadata. | `widgets/music/lib/helpers/Music_TrackHelper.php` |
| `XN_Tag` | Manages tag associations on content. | `lib/XG_TagHelper.php` |
| `XN_SearchResult` / `XN_SearchResults` | Represent search API responses. | `lib/XG_QueryHelper.php` |
| `XN_Shape` | Describes data shapes for profile questions and dynamic forms. | `lib/XG_ShapeHelper.php` |
| `XN_Query_InternalType_FilterClause`, `XN_Query_XG_Query` | Internal helpers for specialized query behaviour. | `lib/XG_QueryHelper.php`, `widgets/events/lib/helpers/Events_NegativePagingList.php` |

## Messaging and Notifications

| Dependency | Description | Representative usage |
| --- | --- | --- |
| `XN_Message`, `XN_MessageFolder`, `XN_Messages` | Messaging API for direct and notification mail. | `lib/XG_MessageHelper.php`, `lib/XG_Messages.php` |
| `XN_Message_Notification` | Helper for composing notification emails. | `widgets/index/lib/helpers/Index_NotificationHelper.php` |
| `XN_Task`, `XN_Tasks`, `XN_Job`, `XN_Jobs` | Background task scheduling utilities. | `lib/XG_JobHelper.php`, `widgets/index/lib/helpers/Index_MembershipHelper.php` |
| `XN_Event` | Event objects used by activity feeds and jobs. | `lib/XG_PerfLogger.php`, `lib/XG_ActivityHelper.php` |

## REST, Requests, and Caching

| Dependency | Description | Representative usage |
| --- | --- | --- |
| `XN_REST` | Performs HTTP requests against Ning REST endpoints. | `lib/XG_MetatagHelper.php`, `widgets/index/controllers/AdminController.php` |
| `XN_Request` | Accesses uploaded file streams and request metadata. | `widgets/index/controllers/EmbeddableController.php` |
| `XN_Cache` | Low-level cache abstraction for content and config objects. | `lib/XG_Cache.php`, `widgets/index/controllers/IndexController.php` |

The compatibility layer introduced in this change set provides lightweight
implementations for each dependency above, enabling the application to run and
be tested without the original proprietary Ning SDK.
